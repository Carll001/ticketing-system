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
        $rules = [
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'role' => 'nullable|in:superadmin,admin,staff',
            'department_id' => ['nullable', 'array'],
            'department_id.*' => ['exists:departments,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ];

        // For store (create)
        if ($this->isMethod('post')) {
            $rules['email'][] = Rule::unique(User::class);
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        // For update
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['email'][] = Rule::unique(User::class)->ignore($this->user->id);
            $rules['password'] = ['nullable', 'confirmed', Password::defaults()];
        }

        return $rules;
    }
}