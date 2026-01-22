<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresetRequest extends FormRequest
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
            'name'       => 'required|string|min:4|max:255',
            'description' => 'nullable|string',
            // Add validation for dynamic fields
            'fields'      => 'array',
            'fields.*.type'  => 'required|in:Checkbox,Input,Description',
            'fields.*.label' => 'required|string|max:255',
        ];
    }
}
