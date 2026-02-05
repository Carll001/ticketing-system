<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   
        return [
            'title' => "required|min:4|max:255",
            'description' => 'nullable|nullable',
            'assigned_to' => 'nullable|nullable|uuid|exists:departments,id',
            'due_date' => 'nullable|date',
            'order' => 'required|in:random,sequential',
        ];
    }

}
