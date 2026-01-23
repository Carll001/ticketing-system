<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Services\DepartmentService;
use App\Models\Department;
use Inertia\Inertia;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TaskResource;



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
    // public function index(Request $request)
    // {
    //     $search = $request->input('search');

    //     $departments = Department::with('users')
    //         ->when($search, function ($query, $search) {
    //             $query->where('name', 'like', "%{$search}%");
    //         })
    //         ->latest()
    //         ->get();

    //     $users = User::with('departments')->get();

    //     return Inertia::render('Department', [
    //         'departments' => DepartmentResource::collection($departments),
    //         'users' => UserResource::collection($users),
    //         'filters' => [
    //             'search' => $search,
    //         ],
    //     ]);
    // }

    public function index(Request $request)
{
    
    $search = $request->input('search');
    
      /** @var \App\Models\User $user */
    $user = Auth::user();

    
    $userDepartmentIds = $user
        ->departments()
        ->pluck('department_id');


    $tasks = Task::with(['steps', 'assigned'])
        ->where(function ($query) use ($userDepartmentIds) {
            $query->whereIn('assigned_to', $userDepartmentIds)
                  ->orWhere('creator_id', Auth::id());
        })
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        })
        ->get();

    return Inertia::render('Task/Index', [
        'tasks' => TaskResource::collection($tasks),
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $this->departmentService->store($request->validated());

        return back()->with('success', 'department created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $tasks = Task::where('assigned_to', $department->id)->get();

        return inertia('Department/Show', [
            'department' => [
                'data' => $department
            ],
            'tasks' => $tasks, // ✅ dito
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
        return back()->with('success', 'Department created successfully!');
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
