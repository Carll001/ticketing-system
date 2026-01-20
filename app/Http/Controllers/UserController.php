<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\User;
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
    public function index()
    {
        $users = User::where('role', 'staff')
            ->with('departments')
            ->get();
        $departments = Department::all();

        return Inertia::render('User/Index', [
            'users' => $users,
            'departments' => $departments,
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
            'department_id' => ['required', 'array', 'min:1'],
            'department_id.*' => ['exists:departments,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->departments()->sync($validated['department_id']);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // return Inertia::render('User/Show', [
        //     'user' => $user,
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
