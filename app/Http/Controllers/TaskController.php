<?php

namespace App\Http\Controllers;

use App\Http\Requests\StepRequest;
use App\Http\Requests\TaskStepRequest;
use App\Models\Task;
use App\Models\Transaction;
use Inertia\Inertia;
use App\Http\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskStepResource;
use App\Models\Department;
use App\Models\Preset;
use App\Models\Step;
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




    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Get current user's department IDs
        $userDepartmentIds = Auth::user()->departments()->pluck('department_id');

        // FETCH TASK DATA with proper filtering
        $tasks = Task::with(['steps', 'assigned'])
            ->where(function ($query) use ($userDepartmentIds) {
                // Show tasks created by the user
                $query->where('creator_id', Auth::id())
                    // OR tasks with no assignment (visible to everyone)
                    ->orWhereNull('assigned_to')
                    // OR tasks assigned to user's departments
                    ->orWhereIn('assigned_to', $userDepartmentIds);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'ILIKE', "%{$search}%")
                        ->orWhere('description', 'ILIKE', "%{$search}%");
                });
            })
            ->paginate(8);

        return Inertia::render('Task/Index', [
            'tasks' => [
                'data' => TaskResource::collection($tasks->items())->resolve(),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'from' => $tasks->firstItem(),
                'to' => $tasks->lastItem(),
            ],
            'departments' => DepartmentResource::collection(Department::all()),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        return Inertia::render('Task/Create', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        // VALIDATION REQUEST
        $data = $request->validated();

        // STORING IN SERVICE
        $task = $this->taskService->store($data);

        return redirect()->route('task.show', $task->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $isCreator = $task->creator_id === Auth::id();

        $task->load([
            'creator',
            'assigned',
            'steps' => function ($query) use ($isCreator, $task) {
                // if (!$isCreator) {
                //     $query->where(function ($q) {
                //         $q->whereNull('assigned_to')
                //             ->orWhere('assigned_to', Auth::id());
                //     });
                // }

                // Order steps by position for sequential tasks
                if ($task->order === 'sequence') {
                    $query->orderBy('position', 'asc');
                }
            },
            'steps.assigned',
            'steps.fields',
            'steps.fields.responses.user'
        ]);

        // Get next available step for current user
        $nextStep = null;
        if ($task->order === 'sequence') {
            $nextStep = $task->getNextAvailableStep(Auth::id());
        }

        $departments = Department::all();

        return Inertia::render('Task/Show', [
            'task' => TaskResource::make($task),
            'nextAvailableStep' => $nextStep?->id,
            'departments' => $departments,
            'presets' => Preset::with(['fields'])->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $departments = Department::all();

        return Inertia::render('Task/Edit', [
            'task' => TaskResource::make($task),
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTaskRequest $request, Task $task)
    {

        $data = $request->validated();
        $this->taskService->update($task, $data);

        return redirect()->route('task.show', $task->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $taskId = $task->id;
        
        // Log the task deletion before deleting
        $user = Auth::user();
        $roleLabel = $user->role ?? 'user';
        if ($roleLabel === 'super-admin') {
            $roleLabel = 'super admin';
        }

        Transaction::create([
            'content' => sprintf('%s deleted task "%s"', $roleLabel, $task->title),
            'user_id' => $user->id,
            'task_id' => $taskId,
        ]);

        $task->delete();

        return redirect()->route('task.index');
    }
}
