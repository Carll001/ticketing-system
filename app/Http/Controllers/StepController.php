<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Models\Task;
use App\Models\User;
use App\Models\Preset;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StepRequest;
use App\Http\Resources\StepResource;
use App\Http\Services\StepService;

class StepController extends Controller
{
    protected StepService $stepService;

    public function __construct(StepService $stepService)
    {
        $this->stepService = $stepService;
    }

    public function index()
    {
        //
    }

    public function create(Task $task)
    {
        return Inertia::render('Step/Create', [
            'task' => $task,
            'users' => User::all(),
            'presets' => Preset::with(['fields'])->get(),
        ]);
    }

    public function store(StepRequest $request)
    {
        $step = $this->stepService->store($request->validated());

        if ($request->again) {
            return back();
        }

        return redirect()->route('task.show', $step->task_id);
    }

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
                        ELSE 4 END
                ");
                $query->with(['responses' => function ($q) {
                    $q->where('user_id', Auth::id());
                }]);
            }
        ]);

        return Inertia::render('Step/Edit', [
            'users' => User::all(),
            'step' => $step,
        ]);
    }

    public function update(StepRequest $request, Task $task, Step $step)
    {
        $this->stepService->update($step, $request->validated());

        if ($request->again === true) {
            return back();
        }

        return redirect()->route('task.show', $step->task_id);
    }

    public function destroy(Task $task, Step $step)
    {
        $this->stepService->delete($step);

        return back();
    }
}
