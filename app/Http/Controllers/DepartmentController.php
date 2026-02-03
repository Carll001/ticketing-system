<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\DepartmentService;
use App\Http\Services\TransactionService;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;



class DepartmentController extends Controller
{

    protected DepartmentService $departmentService;
    protected TransactionService $transactionService;

    public function __construct(DepartmentService $departmentService, TransactionService $transactionService)
    {
        $this->departmentService = $departmentService;
        $this->transactionService = $transactionService;
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

        $departments = Department::with('users')
            ->when($search, function ($query, $search) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            })

            ->latest()
            ->paginate(8);

        $users = User::with('departments')->get();

        return Inertia::render('Department', [
            'departments' => [
                'data' => DepartmentResource::collection($departments->items())->resolve(),
                'current_page' => $departments->currentPage(),
                'last_page' => $departments->lastPage(),
                'per_page' => $departments->perPage(),
                'total' => $departments->total(),
                'from' => $departments->firstItem(),
                'to' => $departments->lastItem(),
            ],
            'users' => UserResource::collection($users),
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
        $department = $this->departmentService->store($request->validated());

        // Log transaction for department creation
        $this->transactionService->logDepartmentCreated($department);

        return back()->with('success', 'department created successfully!');
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
        $changes = [];
        if ($department->name !== $request->validated()['name']) {
            $changes['name'] = $request->validated()['name'];
        }

        $this->departmentService->update($request->validated(), $department);

        // Log transaction for department update
        $this->transactionService->logDepartmentUpdated($department, $changes);

        return back()->with('success', 'Department created successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $departmentName = $department->name;
        $departmentId = $department->id;

        $department->delete();

        // Log transaction for department deletion
        $this->transactionService->logDepartmentDeleted($departmentName, $departmentId);

        return back()->with('success', 'Department deleted successfully!');
    }
}
