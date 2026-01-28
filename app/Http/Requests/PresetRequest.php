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
        // Optional: pwede lagyan ng policy or auth logic
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:4', 'max:255'],
            'description' => ['nullable', 'string'],
            'has_cost'    => ['nullable', 'boolean'],

            // Dynamic fields
            'fields'        => ['nullable', 'array'],
            'fields.*.id'   => ['nullable', 'string'], // allow null for new fields
            'fields.*.type' => ['required', 'in:Checkbox,Input,Description'],
            'fields.*.label'=> ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Optional: custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required'          => 'Preset name is required.',
            'name.min'               => 'Preset name must be at least 4 characters.',
            'fields.*.type.required' => 'Field type is required.',
            'fields.*.type.in'       => 'Field type must be Checkbox, Input, or Description.',
            'fields.*.label.required'=> 'Field label is required.',
        ];
    }
}
