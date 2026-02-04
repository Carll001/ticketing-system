<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Models\Task;
use App\Models\User;
use App\Models\Transaction;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\StepRequest;
use App\Http\Resources\StepResource;
use App\Models\Preset;
use App\Models\RejectedStep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Services\TransactionService;

class StepController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Task $task)
    {
        $users = User::all();

        return Inertia::render('Step/Create', [
            'task' => $task,
            'users' => $users,
            'presets' => Preset::with(['fields'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StepRequest $request)
    {
        $data = $request->validated();

        $step = DB::transaction(function () use ($data) {
            // Get the task to calculate position
            $task = Task::findOrFail($data['task_id']);
            $maxPosition = $task->steps()->max('position') ?? -1;

            // Create the Step
            $step = Step::create([
                'task_id'     => $data['task_id'],
                'preset_id'   => $data['preset_id'] ?? null,
                'title'       => $data['title'],
                'has_cost'    => $data['has_cost'] ?? false,
                'description' => $data['description'],
                'assigned_to' => $data['assigned_to'] ?? null,
                'status'      => $data['assigned_to'] ? 'assigned' : 'pending',
                'position'    => $maxPosition + 1,
            ]);

            // Create the Field Definitions (Questions)
            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $field) {
                    $step->fields()->create([
                        'type'  => $field['type'],
                        'label' => $field['label'],
                    ]);
                }
            }

            return $step;
        });

        // Log the step creation using TransactionService
        $this->transactionService->logStepCreated($step);

        if ($request->again) {
            return back();
        }

        return redirect()->route('task.show', $step->task_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task, Step $step)
    {
        $step->load([
            'task',
            'assigned',
            'fields.responses.user',
            'proofs',
            'comments.user',
            'rejections' => function ($query) {
                $query->with('rejectedBy')->latest(); // Load rejections with user, newest first
            },
        ]);

        return Inertia::render('Step/Show', [
            'step' => StepResource::make($step),
            'users' => User::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task, Step $step)
    {
        $step->load(['assigned', 'task', 'fields' => function ($query) {
            $query->orderByRaw("CASE 
            WHEN type = 'Checkbox' THEN 1 
            WHEN type = 'Input' THEN 2 
            WHEN type = 'Description' THEN 3 
            ELSE 4 END");
            // Load responses for the logged-in user so the form is pre-filled
            $query->with(['responses' => function ($q) {
                $q->where('user_id', Auth::id());
            }]);
        }]);

        return Inertia::render('Step/Edit', [
            'users' => User::all(),
            'step' => $step,
        ]);
    }

    public function updateStatus(Task $task, Step $step, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,rejected,accepted,cancelled,in-progress,completed',
        ]);

        $step->status = $request->status;

        if ($request->status === 'rejected') {
            // Verify the user is assigned to this step
            if ($step->assigned_to !== Auth::id()) {
                abort(403, 'You are not assigned to this step');
            }

            // Create the rejection record
            RejectedStep::create([
                'step_id' => $step->id,
                'rejected_by' => Auth::id(),
                'reason' => $request->reason,
            ]);

            // Update the step - back to pending and unassign
            $step->update([
                'status' => 'rejected',
            ]);

            return redirect()->route('task.show', $step->task_id)
                ->with('success', 'Step rejected successfully');
        }

        $step->assigned_to = Auth::id();
        $step->save();

        // Log the status change
        $user = Auth::user()->name;

        // Determine action based on status
        $action = $user . 'updated step status to ' . $request->status;
        if ($request->status === 'accepted') {
            $action = 'accepted task';
        } elseif ($request->status === 'completed') {
            $action = 'completed task';
        } elseif ($request->status === 'cancelled') {
            $action = 'cancelled task';
        }

        $content = sprintf('%s %s', $roleLabel, $action);

        Transaction::create([
            'content' => $content,
            'user_id' => $user->id,
            'task_id' => $task->id,
        ]);
        
        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StepRequest $request, Task $task, Step $step)
    {
        $data = $request->validated();

        // Logic for setting status based on assignment
        $data['status'] = !empty($data['assigned_to']) ? 'assigned' : 'pending';

        DB::transaction(function () use ($data, $step) {
            // Update the Step basic info
            $step->update([
                'preset_id'   => $data['preset_id'] ?? null,
                'title'       => $data['title'],
                'description' => $data['description'],
                'assigned_to' => $data['assigned_to'] ?? null,
                'status'      => $data['status'],
                'has_cost'    => $data['has_cost'] ?? false,
            ]);

            // Sync Field Definitions (Questions)
            $step->fields()->delete();

            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $field) {
                    $step->fields()->create([
                        'type'  => $field['type'],
                        'label' => $field['label'],
                    ]);
                }
            }
        });

        if ($request->again === true) {
            return back();
        }

        return redirect()->route('step.show', ['task' => $step->task_id, 'step' => $step->id]);
    }

    public function reassign(Request $request, Task $task, Step $step)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id', // Changed to nullable
            'status' => 'sometimes|in:pending,assigned',
        ]);

        // Verify the current user is the task creator
        if ($task->creator_id !== Auth::id()) {
            abort(403, 'Only the task creator can reassign steps');
        }

        // Verify the step is rejected
        if ($step->status !== 'rejected') {
            abort(403, 'Only rejected steps can be reassigned');
        }

        // If user_id is null, set status to pending (anyone can take)
        // If user_id is set, set status to assigned
        $status = $request->user_id ? 'assigned' : 'pending';

        $step->update([
            'assigned_to' => $request->user_id, // Can be null
            'status' => $status,
        ]);

        $message = $request->user_id
            ? 'Step reassigned successfully'
            : 'Step is now available to anyone';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task, Step $step)
    {
        $taskId = $step->task_id;
        $stepId = $step->id;
        
        // Log the step deletion only if super-admin (before deleting)
        $user = Auth::user();
        $roleLabel = $user->role ?? 'user';
        if ($roleLabel === 'super-admin') {
            $roleLabel = 'super admin';
            Transaction::create([
                'content' => sprintf('%s deleted step', $roleLabel),
                'user_id' => $user->id,
                'task_id' => $taskId,
                'step_id' => $stepId,
            ]);
        }
        
        $step->delete();

        return redirect()->route('step.show', ['task' => $step->task_id, 'step' => $step->id]);
    }
}
