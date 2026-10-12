<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('edit-department') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', Rule::exists('employees', 'id')->whereNull('deleted_at')],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'section_id' => ['nullable', 'integer', Rule::exists('sections', 'id')->where(fn ($query) => $query->where('department_id', $this->integer('department_id'))->whereNull('deleted_at'))],
        ];
    }
}
