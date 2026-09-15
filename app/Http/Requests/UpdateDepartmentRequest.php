<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('department')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $department = $this->route('department');
        $id = $department instanceof Department ? $department->id : null;

        return [
            'code' => ['nullable', 'string', 'max:10', Rule::unique('departments', 'code')->ignore($id)],
            'name' => ['required', 'string', 'max:150', Rule::unique('departments', 'name')->ignore($id)],
            'description' => ['nullable', 'string', 'max:500'],
            'manager_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
