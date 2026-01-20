<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Services\DepartmentService;
use App\Models\Department;
use Illuminate\Http\Request; // Add this import
use Inertia\Inertia;

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
    // public function index(Request $request) // Add Request $request parameter
    // {
    //       $departments = Department::latest()->get();

    //     return Inertia::render('Department',[
    //         'departments'=> DepartmentResource::collection($departments),
    //     ]);
        // $departments = Department::query()
        //     ->when($request->search, function ($query, $search) {
        //         $query->where('name', 'like', "%{$search}%");
        //     })
        //     ->latest()
        //     ->paginate(10)
        //     ->withQueryString()
        //     ->through(fn($department) => new DepartmentResource($department));

        // return Inertia::render('Department', [
        //     'departments' => $departments,
        //     'filters' => [
        //         'search' => $request->search,
        //     ],
        // ]);
    // }



    public function index(Request $request)
{
    $departments = Department::query()
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('Department', [
        'departments' => DepartmentResource::collection($departments),
        'filters' => [
            'search' => $request->search,
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

        return back()->with('success', 'Department created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
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
        return back()->with('success', 'Department updated successfully!'); // Fixed typo
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