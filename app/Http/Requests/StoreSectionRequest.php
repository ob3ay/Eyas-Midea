<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create-section') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('sections', 'name')->where(fn ($query) => $query->where('department_id', $this->integer('department_id')))],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'section_head_employee_id' => ['nullable', 'integer', Rule::exists('employees', 'id')->where(fn ($query) => $query->where('department_id', $this->integer('department_id')))],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
