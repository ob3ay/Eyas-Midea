<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

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
            'code' => ['nullable', 'string', 'max:10', Rule::unique('departments', 'code')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:150', Rule::unique('departments', 'name')->whereNull('deleted_at')],
            'description' => ['nullable', 'string', 'max:500'],
            'manager_employee_id' => ['nullable'],
            'sections' => ['nullable', 'array'],
            'sections.*' => ['required', 'string', 'max:150'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يرجى إدخال اسم الدائرة.',
            'name.unique' => 'اسم هذه الدائرة مستخدم بالفعل، يرجى اختيار اسم آخر.',
            'code.unique' => 'رمز الدائرة مستخدم بالفعل، يرجى اختيار رمز آخر.',
            'code.max' => 'رمز الدائرة يجب ألا يتجاوز 10 أحرف.',
        ];
    }
}
