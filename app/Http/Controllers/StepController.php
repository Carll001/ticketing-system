<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Step;
use App\Models\Task;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\StepRequest;
use App\Http\Resources\StepResource;
use Illuminate\Support\Facades\Auth;

class StepController extends Controller
{
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
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StepRequest $request)
    {
        $data = $request->validated();

        // Wrap in a transaction for safety
        $step = DB::transaction(function () use ($data) {
            // 1. Create the Step
            $step = Step::create([
                'task_id'     => $data['task_id'],
                'title'       => $data['title'],
                'description' => $data['description'],
                'assigned_to' => $data['assigned_to'],
                'status'      => $data['assigned_to'] ? 'assigned' : 'pending',
            ]);

            // 2. Create the Field Definitions (Questions)
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
            'fields.responses.user'
        ]);

        return Inertia::render('Step/Show', [
            'step' => StepResource::make($step),
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

    /**
     * Update the specified resource in storage.
     */
    public function update(StepRequest $request, Task $task, Step $step)
    {
        $data = $request->validated();

        // Logic for setting status based on assignment
        $data['status'] = !empty($data['assigned_to']) ? 'assigned' : 'pending';

        DB::transaction(function () use ($data, $step) {
            // 1. Update the Step basic info
            $step->update([
                'title'       => $data['title'],
                'description' => $data['description'],
                'assigned_to' => $data['assigned_to'],
                'status'      => $data['status'],
            ]);

            // 2. Sync Field Definitions (Questions)
            // Simplest approach: delete old ones and insert new ones
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

        return redirect()->route('task.show', $step->task_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task, Step $step)
    {
        $step->delete();

        return back();
    }
}
