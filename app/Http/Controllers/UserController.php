<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Department;
use App\Models\User;
use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        $rolesAllowed = match ($user->role) {
            'superadmin' => ['!=', 'superadmin'],
            'admin' => ['=', 'staff'],
            default => abort(403, 'Unauthorized action.'),
        };

        $users = User::query()
            ->when($rolesAllowed[0] === '!=', fn($q) => $q->where('role', '!=', $rolesAllowed[1]))
            ->when($rolesAllowed[0] === '=', fn($q) => $q->where('role', $rolesAllowed[1]))
            ->when($search, function ($q) use ($search) {
                $q->where(fn($query) =>
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                );
            })
            ->with('departments')
            ->get();

        return Inertia::render('User/Index', [
            'users' => $users,
            'departments' => Department::all(),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('User/Create', [
            'departments' => Department::all(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['permissions'])) {
            $user->givePermissionTo($data['permissions']);
        }

        $user->departments()->sync($data['department_id'] ?? []);

        return redirect()->route('user.index');
    }

    public function show(User $user)
    {
        $departmentIds = $user->departments()->pluck('departments.id');

        $tasks = Task::whereIn('assigned_to', $departmentIds)
            ->with('creator', 'steps')
            ->get();

        return Inertia::render('User/Show', [
            'user' => $user->load('departments'),
            'tasks' => $tasks,
            'userPermissions' => $user->permissions->pluck('name'),
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('User/Edit', [
            'user' => $user->load('departments', 'permissions'),
            'departments' => Department::all(),
            'userPermissions' => $user->permissions->pluck('name'),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        $user->syncPermissions($data['permissions'] ?? []);
        $user->departments()->sync($data['department_id'] ?? []);

        return back();
    }

    public function destroy(User $user)
    {
        $user->departments()->detach();
        $user->delete();

        return back();
    }
}
