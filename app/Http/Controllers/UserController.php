<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\User;
use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Http\Request;
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
        'superadmin' => ['!=', 'superadmin'], // can see everyone except superadmins
        'admin' => ['=', 'staff'],            // can see only staff
        'staff' => abort(403, 'Unauthorized action.'),
        default => abort(403, 'Unauthorized action.'),
    };

    $usersQuery = User::query()
        // Apply role filters
        ->when($rolesAllowed[0] === '!=', fn($q) => $q->where('role', '!=', $rolesAllowed[1]))
        ->when($rolesAllowed[0] === '=', fn($q) => $q->where('role', $rolesAllowed[1]))
        // Apply case-insensitive search
        ->when($search, function ($q) use ($search) {
            $search = strtolower($search); // Lowercase input
            $q->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        })
        ->with('departments');

    $users = $usersQuery->get();
    $departments = Department::all();

    return Inertia::render('User/Index', [
        'users' => $users,
        'departments' => $departments,
        'filters' => [
            'search' => $search,
        ],
    ]);
}

    // public function index(Request $request)
    // {
    //     $search = $request->input('search');
    //     $user = auth()->user();

    //     // Determine which roles the user is allowed to see
    //     $rolesAllowed = match ($user->role) {
    //         'superadmin' => ['!=', 'superadmin'], // can see everyone except superadmins
    //         'admin' => ['=', 'staff'],            // can see only staff
    //         'staff' => abort(403, 'Unauthorized action.'),
    //         default => abort(403, 'Unauthorized action.'),
    //     };

    //     $usersQuery = User::query()
    //         ->when($rolesAllowed[0] === '!=', fn($q) => $q->where('role', '!=', $rolesAllowed[1]))
    //         ->when($rolesAllowed[0] === '=', fn($q) => $q->where('role', $rolesAllowed[1]))
    //         ->when($search, function ($q) use ($search) {
    //             $q->where(
    //                 fn($query) => $query
    //                     ->where('name', 'like', "%{$search}%")
    //                     ->orWhere('email', 'like', "%{$search}%")
    //             );
    //         })
    //         ->with('departments');

    //     $users = $usersQuery->get();

    //     $departments = Department::all();

    //     return Inertia::render('User/Index', [
    //         'users' => $users,
    //         'departments' => $departments,
    //         'filters' => [
    //             'search' => $search,
    //         ],
    //     ]);
    // }




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
    public function store(Request $request)
    {

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
    public function update(Request $request, User $user)
    {
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
