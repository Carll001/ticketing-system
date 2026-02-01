<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\User;
use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = auth()->user();

        // Determine which roles the user is allowed to see
        $rolesAllowed = match ($user->role) {
            'superadmin' => ['!=', 'superadmin'],
            'admin' => ['=', 'staff'],
            'staff' => abort(403, 'Unauthorized action.'),
            default => abort(403, 'Unauthorized action.'),
        };

        $usersQuery = User::query()
            ->when($rolesAllowed[0] === '!=', fn($q) => $q->where('role', '!=', $rolesAllowed[1]))
            ->when($rolesAllowed[0] === '=', fn($q) => $q->where('role', $rolesAllowed[1]))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            })
            ->with('departments');

        $users = $usersQuery->paginate(10);

        $departments = Department::all();

        return Inertia::render('User/Index', [
            'users' => $users,
            'departments' => $departments,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();

        return Inertia::render('User/Create', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'] ?? 'staff', // Set role if provided
            ]);

            // Sync permissions
            if (!empty($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
            }

            // Sync departments
            if (!empty($validated['department_id'])) {
                $user->departments()->sync($validated['department_id']);
            }
        });

        return redirect()->route('user.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Get tasks assigned to departments this user belongs to
        $departmentIds = $user->departments()->pluck('departments.id');
        $tasks = Task::whereIn('assigned_to', $departmentIds)->with('creator', 'steps')->get();

        return Inertia::render('User/Show', [
            'user' => $user->load('departments'),
            'tasks' => $tasks,
            'userPermissions' => $user->permissions->pluck('name'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $departments = Department::all();

        return Inertia::render('User/Edit', [
            'user' => $user->load('departments', 'permissions'),
            'departments' => $departments,
            'userPermissions' => $user->permissions->pluck('name'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $user) {
            // Update basic info
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            // Update role if provided
            if (!empty($validated['role'])) {
                $updateData['role'] = $validated['role'];
            }

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Sync permissions
            $user->syncPermissions($validated['permissions'] ?? []);

            // Sync departments
            $user->departments()->sync($validated['department_id'] ?? []);
        });

        return redirect()->route('user.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->departments()->detach();
        $user->delete();

        return back();
    }
}