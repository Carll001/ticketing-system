<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // pwede mo lagyan ng auth logic
    }

    public function rules(): array
    {
        return [
            'task_step_id' => 'required|exists:task_steps,id',
            'description' => 'nullable|string',
        ];
    }
}
