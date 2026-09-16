<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $employee = $user->employee;

        if (! $employee) {
            abort(403, 'حسابك غير مرتبط بسجل موظف في المنظومة.');
        }

        // نطاق العرض: الموظف يرى طلباته الخاصة، والإداري يرى طلبات دائرته أو المنظمة
        $scope = $user->highestScopeFor('requests.view') ?? 'self';

        $query = EmployeeRequest::with(['employee.department', 'reviewer'])
            ->latest();

        if ($scope === 'self' || ! $user->hasRole('SA')) {
            $query->where('employee_id', $employee->id);
        } elseif ($scope === 'department') {
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $employee->department_id));
        }

        $requests = $query->get();

        $stats = [
            'total' => $requests->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
        ];

        return view('requests.index', compact('requests', 'stats', 'employee'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        if (! $employee) {
            abort(403, 'حسابك غير مرتبط بسجل موظف.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:leave,advance,certificate,other'],
            'title' => ['required', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['nullable', 'required_if:type,leave', 'date'],
            'end_date' => ['nullable', 'required_if:type,leave', 'date', 'after_or_equal:start_date'],
            'amount' => ['nullable', 'required_if:type,advance', 'numeric', 'min:1', 'max:100000'],
        ], [
            'type.required' => 'يرجى تحديد نوع الطلب.',
            'title.required' => 'يرجى إدخال عنوان أو موضوع الطلب.',
            'start_date.required_if' => 'تاريخ بدء الإجازة مطلوب عند اختيار طلب إجازة.',
            'end_date.required_if' => 'تاريخ نهاية الإجازة مطلوب عند اختيار طلب إجازة.',
            'end_date.after_or_equal' => 'تاريخ نهاية الإجازة يجب أن يكون مساوياً أو بعد تاريخ البدء.',
            'amount.required_if' => 'المبلغ المالي مطلوب عند اختيار طلب سلفة.',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر.',
        ]);

        $daysCount = null;
        if (! empty($validated['start_date']) && ! empty($validated['end_date'])) {
            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);
            $daysCount = $start->diffInDays($end) + 1;
        }

        EmployeeRequest::create([
            'employee_id' => $employee->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'reason' => $validated['reason'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'days_count' => $daysCount,
            'amount' => $validated['amount'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('requests.index')->with('success', 'تم تقديم طلبك بنجاح، وهو الآن قيد المراجعة لدى الإدارة.');
    }

    public function cancel(Request $request, EmployeeRequest $employeeRequest): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        if (! $employee || ($employeeRequest->employee_id !== $employee->id && ! $user->hasRole('SA'))) {
            abort(403, 'غير مصرح لك بإلغاء هذا الطلب.');
        }

        if ($employeeRequest->status !== 'pending') {
            return redirect()->route('requests.index')->with('error', 'لا يمكن إلغاء هذا الطلب لأنه تمت مراجعته مسبقاً.');
        }

        $employeeRequest->update(['status' => 'cancelled']);

        return redirect()->route('requests.index')->with('success', 'تم إلغاء الطلب بنجاح.');
    }
}
