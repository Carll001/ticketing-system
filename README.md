# ticketing-system





<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BasePaginationRequest extends FormRequest
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
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'order_by' => 'nullable|string',
            'order_direction' => 'nullable|string|in:asc,desc',
        ];
    }

    /**
     * Get default values for pagination parameters
     */
    public function paginationDefaults(): array
    {
        return [
            'page' => 1,
            'per_page' => 15,
            'order_direction' => 'asc',
        ];
    }

    /**
     * Get pagination parameters from the request
     *
     * @param  string|null  $defaultOrderBy  Default column to sort by
     */
    public function paginationParams(?string $defaultOrderBy = null): array
    {
        $defaults = $this->paginationDefaults();
        $params = [
            'page' => $this->input('page', $defaults['page']),
            'per_page' => $this->input('per_page', $defaults['per_page']),
            'order_by' => $this->input('order_by', $defaultOrderBy),
            'order_direction' => $this->input('order_direction', $defaults['order_direction']),
        ];

        return $params;
    }
}