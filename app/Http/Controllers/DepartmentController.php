<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Services\DepartmentService;
use App\Models\Department;
use Inertia\Inertia;
use App\Models\Task;

class DepartmentController extends Controller
{

    protected DepartmentService $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with('users')->latest()->get();

        return Inertia::render('Department',[
            'departments'=> DepartmentResource::collection($departments),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $this->departmentService->store($request->validated());

        return back()->with('success','department created successfully!');
    }

    /**
     * Display the specified resource.
     */
   public function show(Department $department)
{
    // Eager load assigned users
    $department->load('users');

    // Fetch tasks assigned to this department
    $tasks = Task::where('assigned_to', $department->id)->get();

    return inertia('Department/Show', [
        'department' => [
            'data' => [
                'id' => $department->id,
                'name' => $department->name,
                'created_at' => $department->created_at,
                'updated_at' => $department->updated_at,
                'assigned_users' => $department->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                }),
            ],
        ],
        'tasks' => $tasks,
    ]);
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
      $this->departmentService->update($request->validated(), $department); 
        return back()->with('success','Department created successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return back()->with('success', 'Department deleted successfully!');
    }


    
}
