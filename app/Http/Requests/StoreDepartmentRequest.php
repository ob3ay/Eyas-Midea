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
        return $this->user()?->can('create-department') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:10', 'unique:departments,code'],
            'name' => ['required', 'string', 'max:150', 'unique:departments,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'manager_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
