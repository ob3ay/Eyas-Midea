<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\EmployeeRequest;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployeeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $employee = $user->employee;
        $scope = $this->authorizeScope($user, 'requests.view');
        $isAdmin = in_array($scope, ['section', 'department', 'organization'], true);
        $canCreateRequest = $user->hasPermission('requests.create');
        $canCancelRequest = $user->hasPermission('requests.cancel');
        $canApproveRequest = $user->hasPermission('requests.approve');
        $canRejectRequest = $user->hasPermission('requests.reject');
        $isSuperAdmin = $user->hasRole('SA');

        $query = EmployeeRequest::with(['employee.department', 'employee.section', 'reviewer'])
            ->latest();

        $query->whereIn('employee_id', $user->employeesWithinScope('requests.view')->select('employees.id'));

        $statusCounts = (clone $query)
            ->selectRaw('status, count(*) as aggregate')
            ->reorder()
            ->groupBy('status')
            ->pluck('aggregate', 'status');
        $requests = $query->paginate(25)->withQueryString();

        $stats = [
            'total' => (int) $statusCounts->sum(),
            'pending' => (int) $statusCounts->get('pending', 0),
            'approved' => (int) $statusCounts->get('approved', 0),
            'rejected' => (int) $statusCounts->get('rejected', 0),
        ];

        return view('requests.index', compact(
            'requests', 'stats', 'employee', 'isAdmin', 'isSuperAdmin',
            'canCreateRequest', 'canCancelRequest', 'canApproveRequest', 'canRejectRequest',
        ));
    }

    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope($user, 'requests.create');
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

        DB::transaction(function () use ($employee, $validated, $daysCount, $notifications): void {
            $employeeRequest = EmployeeRequest::create([
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
            AuditLog::record('Create', 'EmployeeRequest', $employeeRequest->id);

            $adminUsers = User::whereHas('roles.permissions', fn ($q) => $q
                ->where('permissions.key', 'requests.approve')
                ->where('role_permissions.scope', 'organization'))
                ->get();
            foreach ($adminUsers as $admin) {
                $notifications->createForUser(
                    $admin,
                    'new_employee_request',
                    'طلب موظف جديد وارد',
                    'قام الموظف '.$employee->full_name.' بتقديم: '.$validated['title'],
                    $employeeRequest
                );
            }
        });

        return redirect()->route('requests.index')->with('success', 'تم تقديم طلبك بنجاح، وهو الآن قيد المراجعة والاعتماد لدى الإدارة.');
    }

    public function approve(Request $request, EmployeeRequest $employeeRequest, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);
        DB::transaction(function () use ($user, $employeeRequest, $validated, $notifications): void {
            $decided = $this->decide($user, $employeeRequest, 'requests.approve', 'approved', $validated['admin_notes'] ?? null);
            $notifications->createForEmployee($decided->employee_id, 'request_approved', 'تمت الموافقة على طلبك',
                'تمت الموافقة الرسمية على: '.$decided->title.(($validated['admin_notes'] ?? null) ? ' — ملاحظة: '.$validated['admin_notes'] : ''), $decided);
        });

        return redirect()->route('requests.index')->with('success', 'تم اعتماد وقبول الطلب بنجاح، وتم إشعار الموظف بذلك.');
    }

    public function reject(Request $request, EmployeeRequest $employeeRequest, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);
        DB::transaction(function () use ($user, $employeeRequest, $validated, $notifications): void {
            $decided = $this->decide($user, $employeeRequest, 'requests.reject', 'rejected', $validated['admin_notes'] ?? null);
            $notifications->createForEmployee($decided->employee_id, 'request_rejected', 'تم رفض الطلب',
                'تم رفض: '.$decided->title.(($validated['admin_notes'] ?? null) ? ' — السبب: '.$validated['admin_notes'] : ''), $decided);
        });

        return redirect()->route('requests.index')->with('success', 'تم رفض الطلب وتسجيل الملاحظات، وتم إشعار الموظف.');
    }

    public function cancel(Request $request, EmployeeRequest $employeeRequest): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        abort_unless($employee !== null, 403, 'غير مصرح لك بإلغاء هذا الطلب.');
        $cancelled = DB::transaction(function () use ($user, $employeeRequest): bool {
            $lockedRequest = EmployeeRequest::query()->lockForUpdate()->with('employee')->findOrFail($employeeRequest->id);
            $this->authorizeScope($user, 'requests.cancel');
            if ($lockedRequest->employee === null || ! $user->allowsEmployeeWithinScope('requests.cancel', $lockedRequest->employee)) {
                abort(403, 'غير مصرح لك بإلغاء هذا الطلب.');
            }

            if ($lockedRequest->status !== 'pending') {
                return false;
            }

            $lockedRequest->update(['status' => 'cancelled']);
            AuditLog::record('Cancel', 'EmployeeRequest', $lockedRequest->id);

            return true;
        });

        if (! $cancelled) {
            return redirect()->route('requests.index')->with('error', 'لا يمكن إلغاء هذا الطلب لأنه تمت مراجعته مسبقاً.');
        }

        return redirect()->route('requests.index')->with('success', 'تم إلغاء الطلب بنجاح.');
    }

    private function assertCanDecide(User $user, EmployeeRequest $employeeRequest, string $permission): void
    {
        abort_unless($employeeRequest->status === 'pending', 409, 'تمت مراجعة هذا الطلب مسبقاً.');

        // منع الموافقة الذاتية (تضارب مصالح): حتى لو دوره يسمحله يدير طلبات
        // دائرته/قسمه، ما بيقدر يعتمد قرار بطلبه الشخصي هو.
        if ($user->employee && $employeeRequest->employee_id === $user->employee->id) {
            abort(403, 'لا يمكنك اعتماد أو رفض طلبك الشخصي - لازم مسؤول تاني يراجعه.');
        }

        $this->authorizeScope($user, $permission);
        $targetEmployee = $employeeRequest->employee;
        if ($targetEmployee === null || ! $user->allowsEmployeeWithinScope($permission, $targetEmployee)) {
            abort(403, 'غير مصرح لك باتخاذ قرار بشأن هذا الطلب.');
        }
    }

    private function decide(User $user, EmployeeRequest $employeeRequest, string $permission, string $status, ?string $notes): EmployeeRequest
    {
        return DB::transaction(function () use ($user, $employeeRequest, $permission, $status, $notes): EmployeeRequest {
            $lockedRequest = EmployeeRequest::query()->lockForUpdate()->with('employee')->findOrFail($employeeRequest->id);
            $this->assertCanDecide($user, $lockedRequest, $permission);
            $lockedRequest->update([
                'status' => $status,
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
                'admin_notes' => $notes,
            ]);
            AuditLog::record(ucfirst($status), 'EmployeeRequest', $lockedRequest->id);

            return $lockedRequest->fresh('employee');
        });
    }

    private function authorizeScope(User $user, string $permission): string
    {
        $scope = $user->highestScopeFor($permission);
        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        return $scope;
    }
}
