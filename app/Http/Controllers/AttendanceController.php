<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    /** أوقات الدوام الرسمي المعتمدة. */
    private const WORK_START_TIME = '10:00:00';

    private const WORK_END_TIME = '16:00:00';

    public function index(Request $request)
    {
        $user = $request->user();
        $viewScope = $this->authorizeScope('attendance.view');
        $createScope = $user->highestScopeFor('attendance.create');

        $today = today();

        // استخراج الفلاتر
        $quickDate = $request->input('quick_date');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');
        $status = $request->input('status');
        $search = $request->input('search');

        // تحديد الفترات بناء على الفلتر السريع أو المدخلات اليدوية
        if ($quickDate === 'today') {
            $dateFrom = $today->toDateString();
            $dateTo = $today->toDateString();
        } elseif ($quickDate === 'yesterday') {
            $yesterday = today()->subDay()->toDateString();
            $dateFrom = $yesterday;
            $dateTo = $yesterday;
        } elseif ($quickDate === 'this_week') {
            $dateFrom = today()->startOfWeek(Carbon::SATURDAY)->toDateString();
            $dateTo = today()->endOfWeek(Carbon::FRIDAY)->toDateString();
        } elseif ($quickDate === 'this_month') {
            $dateFrom = today()->startOfMonth()->toDateString();
            $dateTo = today()->endOfMonth()->toDateString();
        } elseif ($quickDate === 'all') {
            $dateFrom = null;
            $dateTo = null;
        } elseif (! $request->has('date_from') && ! $request->has('date_to')) {
            if ($viewScope === 'self') {
                $dateFrom = today()->subDays(30)->toDateString();
                $dateTo = $today->toDateString();
            } else {
                $dateFrom = $today->toDateString();
                $dateTo = $today->toDateString();
            }
        }

        // بناء استعلام السجلات
        $recordsQuery = $this->buildFilteredQuery($user, $viewScope, [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'department_id' => $departmentId,
            'employee_id' => $employeeId,
            'status' => $status,
            'search' => $search,
        ]);

        $records = $recordsQuery->with(['employee.department'])->get();

        // حساب الإحصائيات التراكمية بناءً على السجلات الناتجة من الفلتر
        $totalRecorded = $records->count();
        $presentCount = $records->whereIn('status', ['Present', 'Late'])->count();
        $onTimeCount = $records->where('status', 'Present')->count();
        $lateCount = $records->where('status', 'Late')->count();
        $totalLateMinutes = $records->where('status', 'Late')->sum('late_minutes');
        $leaveCount = $records->where('status', 'Leave')->count();
        $absentCount = $records->where('status', 'Absent')->count();

        $activeEmployeesCount = $user->employeesWithinScope('attendance.view', $viewScope)
            ->where('employment_status', 'active')
            ->count();

        // احتساب معدل الحضور
        if ($dateFrom === $dateTo && $dateFrom === $today->toDateString() && $viewScope !== 'self') {
            $totalTargetEmployees = $activeEmployeesCount;
            $attendanceRate = $totalTargetEmployees > 0 ? round(($presentCount / $totalTargetEmployees) * 100, 1) : 0;
        } else {
            $attendanceRate = $totalRecorded > 0 ? round(($presentCount / $totalRecorded) * 100, 1) : 0;
            $totalTargetEmployees = $totalRecorded;
        }

        // قائمة الموظفين لتسجيل الحضور السريع اليوم (للمستخدم المخول)
        $notCheckedInEmployees = collect();
        $checkedInNotOutEmployees = collect();

        if ($createScope !== null) {
            $recordableEmployees = $user->employeesWithinScope('attendance.create', $createScope)
                ->where('employment_status', 'active')
                ->orderBy('full_name')
                ->get();

            $todayRecordsByEmployee = AttendanceRecord::whereDate('date', $today)
                ->whereIn('employee_id', $recordableEmployees->pluck('id'))
                ->get()
                ->keyBy('employee_id');

            $notCheckedInEmployees = $recordableEmployees->filter(
                fn ($emp) => ! $todayRecordsByEmployee->has($emp->id)
            )->values();

            $checkedInNotOutEmployees = $recordableEmployees->filter(function ($emp) use ($todayRecordsByEmployee) {
                $record = $todayRecordsByEmployee->get($emp->id);

                return $record && $record->check_in_time && ! $record->check_out_time;
            })->values();
        }

        // قوائم خيارات الفلترة
        $departments = collect();
        if ($viewScope === 'organization') {
            $departments = Department::where('is_active', true)->orderBy('name')->get();
        } elseif ($user->employee && $user->employee->department) {
            $departments = collect([$user->employee->department]);
        }

        $allFilterEmployees = $user->employeesWithinScope('attendance.view', $viewScope)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'employee_code', 'department_id']);

        $canCreate = $createScope !== null;
        $canUpdate = $user->hasPermission('attendance.update');
        $canDelete = $user->hasPermission('attendance.delete');

        return view('attendance.index', compact(
            'records',
            'totalRecorded',
            'totalTargetEmployees',
            'activeEmployeesCount',
            'presentCount',
            'onTimeCount',
            'lateCount',
            'totalLateMinutes',
            'leaveCount',
            'absentCount',
            'attendanceRate',
            'notCheckedInEmployees',
            'checkedInNotOutEmployees',
            'departments',
            'allFilterEmployees',
            'canCreate',
            'canUpdate',
            'canDelete',
            'viewScope',
            'dateFrom',
            'dateTo',
            'departmentId',
            'employeeId',
            'status',
            'search',
            'quickDate'
        ));
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('attendance.create');

        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $this->assertWithinScope($user, 'attendance.create', $employee);

        $today = today();
        $now = now();
        $workStart = today()->setTimeFromTimeString(self::WORK_START_TIME);
        $isLate = $now->greaterThan($workStart);
        $lateMinutes = $isLate ? (int) round($now->diffInMinutes($workStart, true)) : 0;

        $record = DB::transaction(function () use ($employee, $today, $now, $isLate, $lateMinutes, $user): ?AttendanceRecord {
            Employee::query()->lockForUpdate()->findOrFail($employee->id);
            $existing = AttendanceRecord::withTrashed()
                ->where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();
            if ($existing !== null && ! $existing->trashed()) {
                return null;
            }

            $attributes = [
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in_time' => $now,
                'status' => $isLate ? 'Late' : 'Present',
                'late_minutes' => $lateMinutes,
                'recorded_by' => $user->id,
            ];
            if ($existing !== null) {
                $existing->restore();
                $existing->fill($attributes)->save();
                $record = $existing;
                AuditLog::record('RestoreCheckIn', 'AttendanceRecord', $record->id);
            } else {
                $record = AttendanceRecord::create($attributes);
                AuditLog::record('CheckIn', 'AttendanceRecord', $record->id);
            }

            return $record;
        });

        if ($record === null) {
            return back()->with('error', "تم تسجيل حضور {$employee->full_name} اليوم مسبقاً.");
        }

        return back()->with('success', "تم تسجيل حضور {$employee->full_name} بنجاح الساعة {$now->format('h:i A')}"
            .($isLate ? " (متأخر {$lateMinutes} دقيقة)" : ' - بالوقت المحدد'));
    }

    public function checkOut(Request $request): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('attendance.create');

        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $this->assertWithinScope($user, 'attendance.create', $employee);

        $record = AttendanceRecord::where('employee_id', $employee->id)->whereDate('date', today())->first();

        if (! $record || ! $record->check_in_time) {
            return back()->with('error', "لا يوجد تسجيل حضور للموظف {$employee->full_name} اليوم.");
        }
        if ($record->check_out_time) {
            return back()->with('error', "تم تسجيل انصراف {$employee->full_name} مسبقاً.");
        }

        $now = now();
        DB::transaction(function () use ($record, $now): void {
            $lockedRecord = AttendanceRecord::query()->lockForUpdate()->findOrFail($record->id);
            if ($lockedRecord->check_out_time !== null) {
                abort(409, 'تم تسجيل الانصراف مسبقاً.');
            }
            $lockedRecord->update([
                'check_out_time' => $now,
                'work_minutes' => (int) round($lockedRecord->check_in_time->diffInMinutes($now, true)),
            ]);
            AuditLog::record('CheckOut', 'AttendanceRecord', $lockedRecord->id);
        });

        $workEnd = today()->setTimeFromTimeString(self::WORK_END_TIME);
        $isEarly = $now->lessThan($workEnd);

        return back()->with('success', "تم تسجيل انصراف {$employee->full_name} بنجاح الساعة {$now->format('h:i A')}"
            .($isEarly ? ' (قبل نهاية الدوام الرسمي 04:00 م)' : ''));
    }

    public function manualLog(Request $request): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('attendance.create');

        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Present', 'Absent', 'Late', 'Leave'])],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $this->assertWithinScope($user, 'attendance.create', $employee);

        $checkIn = $data['check_in_time'] ? $data['date'].' '.$data['check_in_time'] : null;
        $checkOut = $data['check_out_time'] ? $data['date'].' '.$data['check_out_time'] : null;

        $workMinutes = ($checkIn && $checkOut)
            ? (int) round(Carbon::parse($checkIn)->diffInMinutes(Carbon::parse($checkOut), true))
            : null;
        $this->assertCheckOutAfterCheckIn($checkIn, $checkOut);

        $lateMinutes = 0;
        if ($data['status'] === 'Late' && $checkIn) {
            $workStart = Carbon::parse($data['date'].' '.self::WORK_START_TIME);
            $checkInCarbon = Carbon::parse($checkIn);
            if ($checkInCarbon->greaterThan($workStart)) {
                $lateMinutes = (int) round($checkInCarbon->diffInMinutes($workStart, true));
            }
        }

        DB::transaction(function () use ($employee, $data, $checkIn, $checkOut, $workMinutes, $lateMinutes, $user): void {
            Employee::query()->lockForUpdate()->findOrFail($employee->id);
            $record = AttendanceRecord::withTrashed()->firstOrNew(
                ['employee_id' => $employee->id, 'date' => $data['date']],
            );
            if ($record->exists && $record->trashed()) {
                $record->restore();
            }
            $record->fill([
                'status' => $data['status'],
                'check_in_time' => $checkIn,
                'check_out_time' => $checkOut,
                'work_minutes' => $workMinutes,
                'late_minutes' => $lateMinutes,
                'recorded_by' => $user->id,
            ])->save();
            AuditLog::record('ManualLog', 'AttendanceRecord', $record->id);
        });

        return back()->with('success', "تم حفظ القيد اليدوي للموظف {$employee->full_name} بنجاح");
    }

    public function update(Request $request, AttendanceRecord $attendanceRecord): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('attendance.update');

        $this->assertWithinScope($user, 'attendance.update', $attendanceRecord->employee);

        $data = $request->validate([
            'status' => ['required', Rule::in(['Present', 'Absent', 'Late', 'Leave'])],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'late_minutes' => ['nullable', 'integer', 'min:0'],
        ]);

        $recordDate = $attendanceRecord->date->toDateString();
        $checkIn = $data['check_in_time'] ? $recordDate.' '.$data['check_in_time'] : null;
        $checkOut = $data['check_out_time'] ? $recordDate.' '.$data['check_out_time'] : null;

        $workMinutes = ($checkIn && $checkOut)
            ? (int) round(Carbon::parse($checkIn)->diffInMinutes(Carbon::parse($checkOut), true))
            : null;
        $this->assertCheckOutAfterCheckIn($checkIn, $checkOut);

        $lateMinutes = (int) ($data['late_minutes'] ?? 0);
        if ($data['status'] === 'Late' && $checkIn && $lateMinutes === 0) {
            $workStart = Carbon::parse($recordDate.' '.self::WORK_START_TIME);
            $checkInCarbon = Carbon::parse($checkIn);
            if ($checkInCarbon->greaterThan($workStart)) {
                $lateMinutes = (int) round($checkInCarbon->diffInMinutes($workStart, true));
            }
        }

        DB::transaction(function () use ($attendanceRecord, $data, $checkIn, $checkOut, $workMinutes, $lateMinutes): void {
            $lockedRecord = AttendanceRecord::query()->lockForUpdate()->findOrFail($attendanceRecord->id);
            $lockedRecord->update([
                'status' => $data['status'],
                'check_in_time' => $checkIn,
                'check_out_time' => $checkOut,
                'work_minutes' => $workMinutes,
                'late_minutes' => $lateMinutes,
            ]);
            AuditLog::record('Update', 'AttendanceRecord', $lockedRecord->id);
        });

        return back()->with('success', "تم تعديل سجل دوام الموظف {$attendanceRecord->employee->full_name} بنجاح.");
    }

    public function destroy(Request $request, AttendanceRecord $attendanceRecord): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('attendance.delete');

        $this->assertWithinScope($user, 'attendance.delete', $attendanceRecord->employee);

        $employeeName = $attendanceRecord->employee->full_name ?? 'الموظف';
        $recordDate = $attendanceRecord->date ? $attendanceRecord->date->format('Y-m-d') : '';

        DB::transaction(function () use ($attendanceRecord): void {
            $lockedRecord = AttendanceRecord::query()->lockForUpdate()->findOrFail($attendanceRecord->id);
            $lockedRecord->delete();
            AuditLog::record('Delete', 'AttendanceRecord', $lockedRecord->id);
        });

        return back()->with('success', "تم حذف سجل دوام {$employeeName} لتاريخ {$recordDate} بنجاح.");
    }

    public function printSheet(Request $request)
    {
        $user = $request->user();
        $viewScope = $this->authorizeScope('attendance.view');

        $dateFrom = $request->input('date_from', today()->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $records = $this->buildFilteredQuery($user, $viewScope, [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'department_id' => $departmentId,
            'employee_id' => $employeeId,
            'status' => $status,
            'search' => $search,
        ])->with(['employee.department'])->get();

        $selectedDepartment = $departmentId ? Department::find($departmentId) : null;
        $selectedEmployee = $employeeId ? Employee::find($employeeId) : null;

        $totalCount = $records->count();
        $presentCount = $records->where('status', 'Present')->count();
        $lateCount = $records->where('status', 'Late')->count();
        $totalLateMinutes = $records->where('status', 'Late')->sum('late_minutes');
        $leaveCount = $records->where('status', 'Leave')->count();
        $absentCount = $records->where('status', 'Absent')->count();
        $totalWorkMinutes = $records->sum('work_minutes');
        $totalWorkHours = round($totalWorkMinutes / 60, 1);

        return view('attendance.print', compact(
            'records',
            'dateFrom',
            'dateTo',
            'selectedDepartment',
            'selectedEmployee',
            'status',
            'totalCount',
            'presentCount',
            'lateCount',
            'totalLateMinutes',
            'leaveCount',
            'absentCount',
            'totalWorkHours'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $viewScope = $this->authorizeScope('attendance.view');

        $dateFrom = $request->input('date_from', today()->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $records = $this->buildFilteredQuery($user, $viewScope, [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'department_id' => $departmentId,
            'employee_id' => $employeeId,
            'status' => $status,
            'search' => $search,
        ])->with(['employee.department'])->get();

        $filename = 'attendance_report_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $statusLabels = [
            'Present' => 'حاضر بالوقت المحدد',
            'Late' => 'متأخر',
            'Absent' => 'غائب',
            'Leave' => 'إجازة / مهمة عمل',
        ];

        $callback = function () use ($records, $statusLabels) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                '#',
                'الرقم الوظيفي',
                'اسم الموظف',
                'الدائرة الإدارية',
                'المسمى الوظيفي',
                'التاريخ',
                'وقت الحضور',
                'وقت الانصراف',
                'ساعات العمل',
                'الحالة',
                'دقائق التأخير',
            ]);

            foreach ($records as $index => $record) {
                $workHours = $record->work_minutes ? round($record->work_minutes / 60, 2) : 0;
                $statusText = $statusLabels[$record->status] ?? $record->status;

                fputcsv($handle, [
                    $index + 1,
                    $record->employee->employee_code ?? '-',
                    $record->employee->full_name ?? '-',
                    $record->employee->department->name ?? '-',
                    $record->employee->position ?? '-',
                    $record->date ? $record->date->format('Y-m-d') : '-',
                    $record->check_in_time ? $record->check_in_time->format('h:i A') : '--:--',
                    $record->check_out_time ? $record->check_out_time->format('h:i A') : '--:--',
                    $workHours,
                    $statusText,
                    $record->late_minutes ?? 0,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildFilteredQuery($user, string $viewScope, array $filters)
    {
        $query = $this->scopedQuery($user, $viewScope);

        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        if (! empty($filters['department_id'])) {
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $filters['department_id']));
        }

        if (! empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->whereHas('employee', function ($q) use ($term) {
                $q->where('full_name', 'like', "%{$term}%")
                    ->orWhere('employee_code', 'like', "%{$term}%")
                    ->orWhere('position', 'like', "%{$term}%");
            });
        }

        return $query->orderByDesc('date')->orderBy('employee_id');
    }

    private function authorizeScope(string $permissionKey): string
    {
        $user = auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        return $scope;
    }

    private function assertWithinScope($user, string $permissionKey, ?Employee $employee): void
    {
        if (! $employee) {
            abort(404, 'سجل الموظف غير موجود.');
        }

        if (! $user->allowsEmployeeWithinScope($permissionKey, $employee)) {
            abort(403, 'لا تملك صلاحية إلا على موظفين ضمن نطاقك الإداري.');
        }
    }

    private function assertCheckOutAfterCheckIn(?string $checkIn, ?string $checkOut): void
    {
        if ($checkIn !== null && $checkOut !== null && Carbon::parse($checkOut)->lessThanOrEqualTo(Carbon::parse($checkIn))) {
            throw ValidationException::withMessages([
                'check_out_time' => 'يجب أن يكون وقت الانصراف بعد وقت الحضور.',
            ]);
        }
    }

    private function scopedQuery($user, string $scope)
    {
        return AttendanceRecord::query()->whereIn(
            'employee_id',
            $user->employeesWithinScope('attendance.view', $scope)->select('employees.id'),
        );
    }
}
