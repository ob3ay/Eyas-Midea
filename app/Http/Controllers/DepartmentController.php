<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount(['sections', 'employees'])
            ->with('manager')
            ->orderBy('name')
            ->get();

        // الصفحة كمان بتعرض جدول الأقسام (sections) تحت شبكة الدوائر
        $sections = \App\Models\Section::with(['department', 'head'])
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('departments.index', compact('departments', 'sections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:10', 'unique:departments,code'],
            'name' => ['required', 'string', 'max:150', 'unique:departments,name'],
            'manager_employee_id' => ['nullable', 'integer', Rule::exists('employees', 'id')],
        ]);

        Department::create($data);

        return back()->with('success', 'تمت إضافة الدائرة بنجاح');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:10', Rule::unique('departments', 'code')->ignore($department->id)],
            'name' => ['required', 'string', 'max:150', Rule::unique('departments', 'name')->ignore($department->id)],
            'manager_employee_id' => ['nullable', 'integer', Rule::exists('employees', 'id')],
        ]);

        $department->update($data);

        return back()->with('success', 'تم تحديث الدائرة');
    }

    public function destroy(Department $department): RedirectResponse
    {
        // منع حذف دائرة لسا فيها موظفين مرتبطين (بدل ما ياخد error غامض من قاعدة البيانات)
        if ($department->employees()->exists()) {
            return back()->with('error', 'ما فيك تحذف هاي الدائرة، فيه موظفين مرتبطين فيها.');
        }

        $department->delete();

        return back()->with('success', 'تم حذف الدائرة');
    }
}
