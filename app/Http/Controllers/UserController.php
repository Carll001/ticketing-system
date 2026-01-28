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
        $user = auth()->user();

<<<<<<< HEAD
        // Determine which roles the user is allowed to see
        $rolesAllowed = match ($user->role) {
            'superadmin' => ['!=', 'superadmin'], // can see everyone except superadmins
            'admin' => ['=', 'staff'],            // can see only staff
            'staff' => abort(403, 'Unauthorized action.'),
            default => abort(403, 'Unauthorized action.'),
        };

        $usersQuery = User::query()
            ->when($rolesAllowed[0] === '!=', fn($q) => $q->where('role', '!=', $rolesAllowed[1]))
            ->when($rolesAllowed[0] === '=', fn($q) => $q->where('role', $rolesAllowed[1]))
            ->when($search, function ($q) use ($search) {
                $q->where(
                    fn($query) => $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                );
            })
            ->with('departments');

        $users = $usersQuery->get();
=======
        $users = User::where('role', 'staff')
            ->when($search, fn($query) => $query->where(fn($q) => 
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            ))
            ->with('departments')
            ->get();
>>>>>>> TS-30

        $departments = Department::all();

        return Inertia::render('User/Index', [
            'users' => $users,
            'departments' => $departments,
            'filters' => ['search' => $search],
        ]);
    }

<<<<<<< HEAD



=======
>>>>>>> TS-30
    /**
     * Show form for creating a new user.
     */
    public function create()
    {
<<<<<<< HEAD
        $departments = Department::all();

        return Inertia::render('User/Create', [
            'departments' => $departments,
=======
        return Inertia::render('User/Create', [
            'departments' => Department::all(),
>>>>>>> TS-30
        ]);
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $validated = $this->userService->validateCreate($request);
        $this->userService->store($validated);

<<<<<<< HEAD
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class) // No dot concatenation here
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults() // Use the object directly in the array
            ],
            // Validate that department_id is an array and each UUID exists in the departments table
            'role' => 'nullable|in:superadmin,admin,staff',
            'department_id' => ['nullable', 'array'],
            'department_id.*' => ['exists:departments,id'],
        ]);

        $user = User::create($validated + [
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->has('permissions')) {
            $user->givePermissionTo($request->permissions);
        }

        $user->departments()->sync($validated['department_id']);

        return redirect()->route('user.index');
=======
        return back()->with('success', 'User created successfully.');
>>>>>>> TS-30
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
            'userPermissions' => $user->permissions->pluck('name'),
        ]);
    }

    /**
     * Show form for editing a user.
     */
    public function edit(User $user)
    {
        return Inertia::render('User/Edit', [
<<<<<<< HEAD
            'user' => $user->load('departments', 'permissions'),
            'departments' => $departments,
            'userPermissions' => $user->permissions->pluck('name'),
=======
            'user' => $user->load('departments'),
            'departments' => Department::all(),
>>>>>>> TS-30
        ]);
    }


    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
<<<<<<< HEAD
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'department_id' => ['nullable', 'array'],
            'department_id.*' => ['exists:departments,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);


        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);



        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncPermissions($validated['permissions'] ?? []);

        $user->departments()->sync($validated['department_id']);

        // return redirect()->route('user.index');
        return back();
=======
        $validated = $this->userService->validateUpdate($request, $user);
        $this->userService->update($user, $validated);

        return back()->with('success', 'User updated successfully.');
>>>>>>> TS-30
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
