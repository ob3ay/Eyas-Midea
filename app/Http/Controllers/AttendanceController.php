<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /** أوقات الدوام الرسمي المعتمدة. لاحقاً ممكن تُسحب من system_settings بدل ما تكون ثابتة بالكود. */
    private const WORK_START_TIME = '10:00:00';
    private const WORK_END_TIME = '16:00:00';

    public function index(Request $request)
    {
        $user = $request->user();
        $viewScope = $this->authorizeScope('attendance.view');
        $createScope = $user->highestScopeFor('attendance.create'); // ممكن تكون null لو ما عندو صلاحية تسجيل لغيره

        $today = today();

        if ($viewScope === 'self') {
            // للموظف العادي: نعرض سجله الكامل التراكمي (آخر 30 يوم)
            $records = $this->scopedQuery($user, $viewScope)
                ->with(['employee.department'])
                ->orderByDesc('date')
                ->limit(30)
                ->get();

            $totalEmployees = 1;
            $presentCount = $records->whereIn('status', ['Present', 'Late'])->count();
            $onTimeCount = $records->where('status', 'Present')->where('late_minutes', 0)->count();
            $lateCount = $records->where('status', 'Late')->count();
            $leaveCount = $records->where('status', 'Leave')->count();
            $totalRecorded = $records->count();
            $attendanceRate = $totalRecorded > 0 ? round(($presentCount / $totalRecorded) * 100, 1) : 100;
        } else {
            $records = $this->scopedQuery($user, $viewScope)
                ->with(['employee.department'])
                ->whereDate('date', $today)
                ->get();

            $totalEmployees = Employee::where('employment_status', 'active')->count();
            $presentCount = $records->whereIn('status', ['Present', 'Late'])->count();
            $onTimeCount = $records->where('status', 'Present')->count();
            $lateCount = $records->where('status', 'Late')->count();
            $leaveCount = $records->where('status', 'Leave')->count();
            $attendanceRate = $totalEmployees > 0 ? round(($presentCount / $totalEmployees) * 100, 1) : 0;
        }

        $checkedInEmployeeIds = [];
        $notCheckedInEmployees = collect();
        $checkedInNotOutEmployees = collect();

        if ($createScope !== null) {
            $recordableEmployees = $this->scopedEmployeesQuery($user, $createScope)
                ->where('employment_status', 'active')
                ->orderBy('full_name')
                ->get();

            $todayRecordsByEmployee = AttendanceRecord::whereDate('date', $today)
                ->whereIn('employee_id', $recordableEmployees->pluck('id'))
                ->get()
                ->keyBy('employee_id');

            $notCheckedInEmployees = $recordableEmployees->filter(
                fn($emp) => ! $todayRecordsByEmployee->has($emp->id)
            )->values();

            $checkedInNotOutEmployees = $recordableEmployees->filter(function ($emp) use ($todayRecordsByEmployee) {
                $record = $todayRecordsByEmployee->get($emp->id);
                return $record && $record->check_in_time && ! $record->check_out_time;
            })->values();
        }

        $canCreate = $createScope !== null;
        $canUpdate = $user->hasPermission('attendance.update');

        return view('attendance.index', compact(
            'records',
            'totalEmployees',
            'presentCount',
            'onTimeCount',
            'lateCount',
            'leaveCount',
            'attendanceRate',
            'notCheckedInEmployees',
            'checkedInNotOutEmployees',
            'canCreate',
            'canUpdate',
            'viewScope',
        ));
    }

    /**
     * تسجيل حضور لموظف مُحدَّد - بواسطة المستخدم المخوّل بصلاحية attendance.create فقط.
     */
    public function checkIn(Request $request): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('attendance.create');

        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $this->assertWithinScope($user, $scope, $employee);

        $today = today();
        if (AttendanceRecord::where('employee_id', $employee->id)->whereDate('date', $today)->exists()) {
            return back()->with('error', "تم تسجيل حضور {$employee->full_name} اليوم مسبقاً.");
        }

        $now = now();
        $workStart = today()->setTimeFromTimeString(self::WORK_START_TIME);
        $isLate = $now->greaterThan($workStart);
        $lateMinutes = $isLate ? (int) round($now->diffInMinutes($workStart, true)) : 0;

        AttendanceRecord::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in_time' => $now,
            'status' => $isLate ? 'Late' : 'Present',
            'late_minutes' => $lateMinutes,
            'recorded_by' => $user->id,
        ]);

        return back()->with('success', "تم تسجيل حضور {$employee->full_name} الساعة {$now->format('h:i A')}"
            . ($isLate ? " (متأخر {$lateMinutes} دقيقة)" : ' - بالوقت المحدد'));
    }

    public function checkOut(Request $request): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('attendance.create');

        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $this->assertWithinScope($user, $scope, $employee);

        $record = AttendanceRecord::where('employee_id', $employee->id)->whereDate('date', today())->first();

        if (! $record || ! $record->check_in_time) {
            return back()->with('error', "لا يوجد تسجيل حضور للموظف {$employee->full_name} اليوم.");
        }
        if ($record->check_out_time) {
            return back()->with('error', "تم تسجيل انصراف {$employee->full_name} مسبقاً.");
        }

        $now = now();
        $record->update([
            'check_out_time' => $now,
            'work_minutes' => (int) round($record->check_in_time->diffInMinutes($now, true)),
        ]);

        $workEnd = today()->setTimeFromTimeString(self::WORK_END_TIME);
        $isEarly = $now->lessThan($workEnd);

        return back()->with('success', "تم تسجيل انصراف {$employee->full_name} الساعة {$now->format('h:i A')}"
            . ($isEarly ? ' (قبل نهاية الدوام الرسمي 04:00 م)' : ''));
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
        $this->assertWithinScope($user, $scope, $employee);

        $checkIn = $data['check_in_time'] ? $data['date'] . ' ' . $data['check_in_time'] : null;
        $checkOut = $data['check_out_time'] ? $data['date'] . ' ' . $data['check_out_time'] : null;

        AttendanceRecord::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $data['date']],
            [
                'status' => $data['status'],
                'check_in_time' => $checkIn,
                'check_out_time' => $checkOut,
                'work_minutes' => ($checkIn && $checkOut)
                    ? (int) round(\Carbon\Carbon::parse($checkIn)->diffInMinutes(\Carbon\Carbon::parse($checkOut), true))
                    : null,
                'recorded_by' => $user->id,
            ]
        );

        return back()->with('success', "تم حفظ القيد اليدوي للموظف {$employee->full_name} بنجاح");
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

    private function assertWithinScope($user, string $scope, Employee $employee): void
    {
        if ($scope === 'organization') {
            return;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            abort(403, 'حسابك غير مرتبط بسجل موظف.');
        }

        if ($scope === 'self') {
            if ($employee->id !== $ownEmployee->id) {
                abort(403, 'لا تملك صلاحية تسجيل الدوام سوى لحسابك الشخصي.');
            }
            return;
        }

        $boundaryField = $user->hasRole('SH') ? 'section_id' : 'department_id';
        if ($employee->{$boundaryField} !== $ownEmployee->{$boundaryField}) {
            abort(403, 'لا تملك صلاحية تسجيل الدوام إلا لموظفين ضمن نطاقك الإداري.');
        }
    }

    private function scopedQuery($user, string $scope)
    {
        $query = AttendanceRecord::query();

        if ($scope === 'organization') {
            return $query;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            return $query->whereRaw('1 = 0');
        }

        if ($scope === 'self') {
            return $query->where('employee_id', $ownEmployee->id);
        }

        if ($user->hasRole('SH')) {
            return $query->whereHas('employee', fn($q) => $q->where('section_id', $ownEmployee->section_id));
        }

        return $query->whereHas('employee', fn($q) => $q->where('department_id', $ownEmployee->department_id));
    }

    private function scopedEmployeesQuery($user, string $scope)
    {
        $query = Employee::query();

        if ($scope === 'organization') {
            return $query;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            return $query->whereRaw('1 = 0');
        }

        if ($scope === 'self') {
            return $query->where('id', $ownEmployee->id);
        }

        if ($user->hasRole('SH')) {
            return $query->where('section_id', $ownEmployee->section_id);
        }

        return $query->where('department_id', $ownEmployee->department_id);
    }
}
