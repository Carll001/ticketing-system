<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StepCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Optional: pwede ilagay additional authorization logic
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Comment cannot be empty.',
            'content.max'      => 'Comment cannot exceed 2000 characters.',
        ];
    }
}
