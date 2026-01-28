<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProofRequest extends FormRequest
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
     * Validation rules for creating/updating proofs.
     */
    public function rules(): array
    {
        return [
            'task_step_id' => ['required', 'exists:steps,id'], // ensure valid step ID
            'description'  => ['nullable', 'string'],
            'attachments'  => ['nullable', 'array'],
            'attachments.*'=> ['file', 'max:20480'], // each file max 20MB
        ];
    }

    /**
     * Optional: custom validation messages.
     */
    public function messages(): array
    {
        return [
            'task_step_id.required' => 'Step is required.',
            'task_step_id.exists'   => 'Selected step does not exist.',
            'attachments.*.file'    => 'Each attachment must be a valid file.',
            'attachments.*.max'     => 'Each attachment cannot exceed 20MB.',
        ];
    }
}
