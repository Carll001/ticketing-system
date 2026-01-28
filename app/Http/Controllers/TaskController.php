<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Inertia\Inertia;
use App\Http\Services\TaskService;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\TaskResource;
use App\Models\Department;
use App\Models\Preset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function list()
    {
        return TaskResource::collection(Task::all());
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $userDepartmentIds = Auth::user()
            ->departments()
            ->pluck('department_id');

        $tasks = Task::with(['steps', 'assigned'])
            ->whereIn('assigned_to', $userDepartmentIds)
            ->orWhere('creator_id', Auth::id())
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->get();

        return Inertia::render('Task/Index', [
            'tasks' => TaskResource::collection($tasks),
            'departments' => DepartmentResource::collection(Department::all()),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Task/Create', [
            'departments' => Department::all(),
        ]);
    }

    public function store(TaskRequest $request)
    {
        $data = $request->validated();

        $task = $this->taskService->store($data);

        return redirect()->route('task.show', $task->id);
    }

    public function show(Task $task)
    {
        $task->load([
            'creator',
            'assigned',
            'steps.assigned',
            'steps.fields' => function ($query) {
                $query->orderByRaw("CASE 
                    WHEN type = 'Checkbox' THEN 1 
                    WHEN type = 'Input' THEN 2 
                    WHEN type = 'Description' THEN 3 
                    ELSE 4 END");
            },
            'steps.fields.responses.user'
        ]);

        return Inertia::render('Task/Show', [
            'task' => TaskResource::make($task),
            'departments' => Department::all(),
            'presets' => Preset::with(['fields'])->get(),
        ]);
    }

    public function edit(Task $task)
    {
        return Inertia::render('Task/Edit', [
            'task' => TaskResource::make($task),
            'departments' => Department::all(),
        ]);
    }

    public function update(TaskRequest $request, Task $task)
    {
        $data = $request->validated();

        $this->taskService->update($task, $data);

        return redirect()->route('task.show', $task->id);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('task.index');
    }
}
