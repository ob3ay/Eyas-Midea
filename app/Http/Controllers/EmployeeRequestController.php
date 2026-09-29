<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRequest;
use App\Models\User;
use App\Services\NotificationService;
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

        $isSuperAdmin = $user->hasRole('SA') || $user->hasRole('EX');
        $isAdmin = $isSuperAdmin || $user->hasRole('OM') || $user->hasRole('HOD') || $user->hasRole('SH');

        $query = EmployeeRequest::with(['employee.department', 'employee.section', 'reviewer'])
            ->latest();

        if ($user->hasRole('SA') || $user->hasRole('EX') || $user->hasRole('OM')) {
            // الإدارة العليا ترى جميع طلبات الكوادر
        } elseif ($user->hasRole('SH') && $employee?->section_id) {
            $query->whereHas('employee', fn ($q) => $q->where('section_id', $employee->section_id));
        } elseif ($user->hasRole('HOD') && $employee?->department_id) {
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $employee->department_id));
        } else {
            $query->where('employee_id', $employee?->id ?? 0);
        }

        $requests = $query->get();

        $myRequests = $employee
            ? EmployeeRequest::where('employee_id', $employee->id)->latest()->get()
            : collect();

        $stats = [
            'total' => $requests->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
        ];

        return view('requests.index', compact('requests', 'myRequests', 'stats', 'employee', 'isAdmin', 'isSuperAdmin'));
    }

    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        if (! $employee) {
            abort(403, 'حسابك غير مرتبط بسجل موظف لتقديم طلب.');
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

        $empRequest = EmployeeRequest::create([
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

        $adminUsers = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['SA', 'EX']))->get();
        foreach ($adminUsers as $admin) {
            $notifications->createForUser(
                $admin,
                'new_employee_request',
                'طلب موظف جديد وارد',
                'قام الموظف ' . $employee->full_name . ' بتقديم: ' . $validated['title'],
                $empRequest
            );
        }

        return redirect()->route('requests.index')->with('success', 'تم تقديم طلبك بنجاح، وهو الآن قيد المراجعة والاعتماد لدى الإدارة.');
    }

    public function approve(Request $request, EmployeeRequest $employeeRequest, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $this->assertCanDecide($user, $employeeRequest);

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $employeeRequest->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        $notifications->createForEmployee(
            $employeeRequest->employee_id,
            'request_approved',
            'تمت الموافقة على طلبك',
            'تمت الموافقة الرسمية على: ' . $employeeRequest->title . ($validated['admin_notes'] ? ' — ملاحظة: ' . $validated['admin_notes'] : ''),
            $employeeRequest
        );

        return redirect()->route('requests.index')->with('success', 'تم اعتماد وقبول الطلب بنجاح، وتم إشعار الموظف بذلك.');
    }

    public function reject(Request $request, EmployeeRequest $employeeRequest, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $this->assertCanDecide($user, $employeeRequest);

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $employeeRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        $notifications->createForEmployee(
            $employeeRequest->employee_id,
            'request_rejected',
            'تم رفض الطلب',
            'تم رفض: ' . $employeeRequest->title . ($validated['admin_notes'] ? ' — السبب: ' . $validated['admin_notes'] : ''),
            $employeeRequest
        );

        return redirect()->route('requests.index')->with('success', 'تم رفض الطلب وتسجيل الملاحظات، وتم إشعار الموظف.');
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

    private function assertCanDecide(User $user, EmployeeRequest $employeeRequest): void
    {
        if ($user->hasRole('SA') || $user->hasRole('EX') || $user->hasRole('OM')) {
            return;
        }

        $employee = $user->employee;
        $targetEmployee = $employeeRequest->employee;

        if ($user->hasRole('HOD') && $employee && $targetEmployee && $employee->department_id === $targetEmployee->department_id) {
            return;
        }

        if ($user->hasRole('SH') && $employee && $targetEmployee && $employee->section_id === $targetEmployee->section_id) {
            return;
        }

        abort(403, 'غير مصرح لك باتخاذ قرار بشأن هذا الطلب.');
    }
}

