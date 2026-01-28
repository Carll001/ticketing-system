<?php

namespace App\Http\Controllers;

use App\Http\Requests\StepRequest;
use App\Models\Step;
use App\Models\Task;
use App\Models\User;
use Inertia\Inertia;
use App\Http\Resources\StepResource;
use App\Models\Preset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Optional: implement list of steps if needed
    }

    /**
     * Show the form for creating a new step.
     */
    public function create(Task $task)
    {
        return Inertia::render('Step/Create', [
            'task' => $task,
            'users' => User::all(),
            'presets' => Preset::with(['fields'])->get(),
        ]);
    }

    /**
     * Store a newly created step in storage.
     */
    public function store(StepRequest $request)
    {
        $data = $request->validated();

        $step = DB::transaction(function () use ($data) {
            // Create Step
            $step = Step::create([
                'task_id'     => $data['task_id'],
                'preset_id'   => $data['preset_id'] ?? null,
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
                'status'      => !empty($data['assigned_to']) ? 'assigned' : 'pending',
            ]);

            // Create dynamic fields
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

        return !empty($data['again'])
            ? back()
            : redirect()->route('task.show', $step->task_id);
    }

    /**
     * Display the specified step.
     */
    public function show(Task $task, Step $step)
    {
        $step->load([
            'task',
            'assigned',
            'fields.responses.user',
            'proofs',
            'comments.user',
        ]);

        return Inertia::render('Step/Show', [
            'step' => StepResource::make($step),
        ]);
    }

    /**
     * Show the form for editing the specified step.
     */
    public function edit(Task $task, Step $step)
    {
        $step->load([
            'assigned',
            'task',
            'fields' => function ($query) {
                $query->orderByRaw("
                    CASE
                        WHEN type = 'Checkbox' THEN 1
                        WHEN type = 'Input' THEN 2
                        WHEN type = 'Description' THEN 3
                        ELSE 4
                    END
                ")->with(['responses' => function ($q) {
                    $q->where('user_id', Auth::id());
                }]);
            },
        ]);

        return Inertia::render('Step/Edit', [
            'users' => User::all(),
            'step' => $step,
        ]);
    }

    /**
     * Update the specified step in storage.
     */
    public function update(StepRequest $request, Task $task, Step $step)
    {
        $data = $request->validated();
        $data['status'] = !empty($data['assigned_to']) ? 'assigned' : 'pending';

        DB::transaction(function () use ($data, $step) {
            // Update Step
            $step->update([
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
                'status'      => $data['status'],
            ]);

            // Sync Fields (delete old + create new)
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

        return !empty($data['again'])
            ? back()
            : redirect()->route('task.show', $step->task_id);
    }

    /**
     * Remove the specified step from storage.
     */
    public function destroy(Task $task, Step $step)
    {
        $step->delete();
        return back();
    }
}
