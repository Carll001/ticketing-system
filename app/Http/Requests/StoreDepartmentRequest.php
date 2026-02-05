<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
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
            'name' => 'required|min:4|max:255|unique:departments,name|regex:/^[a-zA-Z][a-zA-Z\s]*$/u',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name must start with a letter and contain only letters and spaces.',
            'name.unique' => 'This department name already exists.',
            'name.required' => 'Please enter a department name.',
        ];
    }
}
