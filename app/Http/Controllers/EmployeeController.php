<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'section', 'user'])
            ->orderByDesc('hire_date')
            ->get();

        $totalCount = $employees->count();
        $activeCount = $employees->where('employment_status', 'active')->count();
        $onLeaveCount = $employees->where('employment_status', 'on_leave')->count();
        $departmentCount = Department::count();

        // الرقم الوظيفي التالي المقترح بالفورم (للحقل القرائي فقط)
        $nextEmployeeCode = 'EMP-' . str_pad((Employee::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        $departments = Department::orderBy('name')->get();

        return view('employees.index', compact(
            'employees',
            'totalCount',
            'activeCount',
            'onLeaveCount',
            'departmentCount',
            'nextEmployeeCode',
            'departments',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'emp_code' => ['nullable', 'string', 'max:30', 'unique:employees,employee_code'],
            'national_id' => ['required', 'string', 'max:20', 'unique:employees,national_id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:25'],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')],
            'job_title' => ['required', 'string', 'max:100'],
            'hire_date' => ['required', 'date'],
        ]);

        $employee = Employee::create([
            'employee_code' => $data['emp_code'] ?? null,
            'national_id' => $data['national_id'],
            'full_name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'phone' => $data['phone'],
            'department_id' => $data['department_id'],
            'position' => $data['job_title'],
            'hire_date' => $data['hire_date'],
            'employment_status' => 'active',
        ]);

        // إنشاء حساب دخول تلقائي للموظف بالإيميل يلي انبعت
        $username = $this->generateUniqueUsername($data['first_name'], $data['last_name']);
        $temporaryPassword = Str::random(10);

        User::create([
            'employee_id' => $employee->id,
            'username' => $username,
            'email' => $data['email'],
            'password' => Hash::make($temporaryPassword),
            'is_active' => true,
        ]);

        // بما إنه ما في نظام إرسال إيميلات لهلق، منعرض كلمة السر المؤقتة مرة وحدة بالرسالة
        // عشان الإداري يقدر يسلمها يدوياً للموظف. لاحقاً لما نبني إرسال بريد فعلي، بنشيل هاد.
        return back()->with('success', "تم حفظ بيانات الموظف بنجاح. بيانات الدخول: {$username} / كلمة سر مؤقتة: {$temporaryPassword}");
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:200'],
            'position' => ['nullable', 'string', 'max:100'],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')],
            'section_id' => ['nullable', 'integer', Rule::exists('sections', 'id')],
            'phone' => ['nullable', 'string', 'max:25'],
            'employment_status' => ['required', Rule::in(['active', 'inactive', 'on_leave'])],
        ]);

        $employee->update($data);

        return back()->with('success', 'تم تحديث بيانات الموظف');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->user()->exists()) {
            return back()->with('error', 'ما فيك تحذف هاد الموظف، إله حساب دخول مرتبط فيه. عطّل الحساب أولاً من موديول المستخدمين.');
        }

        $employee->delete();

        return back()->with('success', 'تم حذف الموظف');
    }

    private function generateUniqueUsername(string $firstName, string $lastName): string
    {
        $base = Str::slug($firstName . '.' . $lastName, '.');
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }
}
