<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id' => ['required', 'exists:tasks,id'],
            'amount'  => ['required', 'numeric', 'min:0'],
            'type'    => ['required', 'string', 'max:50'],
            'notes'   => ['nullable', 'string', 'max:1000'],
        ];
    }
}
