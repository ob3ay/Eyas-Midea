<?php

namespace App\Http\Requests;

use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('section')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $section = $this->route('section');
        $id = $section instanceof Section ? $section->id : null;

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('sections', 'name')->where(fn ($query) => $query->where('department_id', $this->integer('department_id')))->ignore($id)],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'section_head_employee_id' => ['nullable', 'integer', Rule::exists('employees', 'id')->where(fn ($query) => $query->where('department_id', $this->integer('department_id')))],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
