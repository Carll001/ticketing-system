<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user'); // may value kapag update

        return [
            'name' => ['required', 'string', 'min:4', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user?->id),
            ],

            'password' => [
                $user ? 'nullable' : 'required',
                'confirmed',
                Password::defaults(),
            ],

            'role' => ['nullable', 'in:superadmin,admin,staff'],

            'department_id' => ['nullable', 'array'],
            'department_id.*' => ['exists:departments,id'],

            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ];
    }
}
