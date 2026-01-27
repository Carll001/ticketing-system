<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Services\UserService;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('role', 'staff')
            ->when($search, fn($query) => $query->where(fn($q) => 
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            ))
            ->with('departments')
            ->get();

        $departments = Department::all();

        return Inertia::render('User/Index', [
            'users' => $users,
            'departments' => $departments,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show form for creating a new user.
     */
    public function create()
    {
        return Inertia::render('User/Create', [
            'departments' => Department::all(),
        ]);
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $validated = $this->userService->validateCreate($request);
        $this->userService->store($validated);

        return back()->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user and their tasks.
     */
    public function show(User $user)
    {
        $departmentIds = $user->departments()->pluck('departments.id');

        $tasks = Task::whereIn('assigned_to', $departmentIds)
            ->with(['creator', 'steps'])
            ->get();

        return Inertia::render('User/Show', [
            'user' => $user->load('departments'),
            'tasks' => $tasks,
        ]);
    }

    /**
     * Show form for editing a user.
     */
    public function edit(User $user)
    {
        return Inertia::render('User/Edit', [
            'user' => $user->load('departments'),
            'departments' => Department::all(),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $this->userService->validateUpdate($request, $user);
        $this->userService->update($user, $validated);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $user->departments()->detach();
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
