<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserService
{
    /**
     * Validate data for creating a new user
     */
    public function validateCreate(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
            'department_id' => ['nullable', 'array'],
            'department_id.*' => ['exists:departments,id'],
        ]);
    }

    /**
     * Validate data for updating an existing user
     */
    public function validateUpdate(Request $request, User $user): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'confirmed',
                Password::defaults(),
            ],
            'department_id' => ['required', 'array', 'min:1'],
            'department_id.*' => ['exists:departments,id'],
        ]);
    }

    /**
     * Store a new user with validated data
     */
    public function store(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign departments if provided
        if (!empty($data['department_id'])) {
            $user->departments()->sync($data['department_id']);
        }

        return $user;
    }

    /**
     * Update an existing user with validated data
     */
    public function update(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Update password only if provided
        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        // Sync departments
        $user->departments()->sync($data['department_id']);

        return $user;
    }
}
