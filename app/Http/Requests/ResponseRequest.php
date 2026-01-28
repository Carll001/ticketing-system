<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Optional: pwede ilagay ang policy-based authorization
        return true;
    }

    public function rules(): array
    {
        return [
            'step_field_id' => ['required', 'uuid', 'exists:step_fields,id'],
            'response'      => ['nullable', 'array'],
            'response.*'    => ['nullable'], // can be string, boolean, etc.
            'description'   => ['nullable', 'string'],
            'attachments'   => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:20480'], // 20MB max per file
        ];
    }

    public function messages(): array
    {
        return [
            'step_field_id.required' => 'Step field is required.',
            'step_field_id.uuid'     => 'Step field ID must be a valid UUID.',
            'step_field_id.exists'   => 'Selected step field does not exist.',
            'attachments.*.file'     => 'Each attachment must be a valid file.',
            'attachments.*.max'      => 'Each attachment cannot exceed 20MB.',
        ];
    }
}
