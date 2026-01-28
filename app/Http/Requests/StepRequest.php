<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StepRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pwede ka ring maglagay ng policy-based authorization dito
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'task_id'       => ['required', 'exists:tasks,id'],
            'title'         => ['required', 'string', 'min:4', 'max:255'],
            'description'   => ['nullable', 'string'],
            'assigned_to'   => ['nullable', 'exists:users,id'],
            'again'         => ['nullable', 'boolean'],

            // Dynamic fields
            'fields'        => ['nullable', 'array'],
            'fields.*.type'  => ['required_with:fields', 'in:Checkbox,Input,Description'],
            'fields.*.label' => ['required_with:fields', 'string', 'max:255'],
        ];
    }

    /**
     * Custom messages (optional, for nicer feedback)
     */
    public function messages(): array
    {
        return [
            'fields.*.type.required_with'  => 'Each field must have a type.',
            'fields.*.type.in'             => 'Field type must be Checkbox, Input, or Description.',
            'fields.*.label.required_with' => 'Each field must have a label.',
            'fields.*.label.max'           => 'Field label cannot exceed 255 characters.',
        ];
    }
}
