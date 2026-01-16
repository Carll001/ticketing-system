<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Inertia\Inertia;
use App\Http\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Department;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }


    public function list()
    {
        // RETURN TASK DATA
        return TaskResource::collection(Task::all());
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // FETCH ALL TASK DATA
        $tasks = Task::all();
        $departments = Department::all();

        return Inertia::render('Task/Index', [
            'tasks' => TaskResource::collection($tasks),
            'departments' => $departments,
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
        $this->taskService->store($data);

        return redirect()->route('task.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load('creator');    
        $departments = Department::all();

        return Inertia::render('Task/Show', [
            'task' => TaskResource::make($task),
            'departments' => $departments,
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
        $task->delete();

        return redirect()->route('task.index');
    }
}
