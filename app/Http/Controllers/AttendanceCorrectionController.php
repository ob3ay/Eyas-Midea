<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AttendanceCorrectionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->hasPermission('attendance.view') || $user->hasPermission('attendance.update'), 403);
        $reviewScope = $user->highestScopeFor('attendance.update');
        $employeeIds = $user->employeesWithinScope(
            $reviewScope !== null ? 'attendance.update' : 'attendance.view',
        )->select('employees.id');

        $corrections = AttendanceCorrectionRequest::query()
            ->with(['attendanceRecord.employee.department', 'attendanceRecord.employee.section', 'reviewer'])
            ->whereHas('attendanceRecord', fn ($query) => $query->whereIn('employee_id', $employeeIds))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $ownEmployeeId = $user->employee_id;
        $ownRecords = $ownEmployeeId === null
            ? collect()
            : AttendanceRecord::query()->where('employee_id', $ownEmployeeId)->latest('date')->limit(60)->get();

        return view('attendance.corrections', [
            'corrections' => $corrections,
            'ownRecords' => $ownRecords,
            'canReview' => $reviewScope !== null,
        ]);
    }

    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->employee_id !== null, 403, 'الحساب غير مرتبط بموظف.');
        abort_unless($user->hasPermission('attendance.view'), 403, 'ليس لديك صلاحية طلب تصحيح الحضور.');

        $data = $request->validate([
            'attendance_record_id' => ['required', 'integer', 'exists:attendance_records,id'],
            'reason' => ['required', 'string', 'max:2000'],
            'requested_status' => ['required', Rule::in(['Present', 'Absent', 'Late', 'Leave'])],
            'requested_check_in_time' => ['nullable', 'date_format:H:i'],
            'requested_check_out_time' => ['nullable', 'date_format:H:i'],
        ]);

        DB::transaction(function () use ($user, $data, $notifications): void {
            $record = AttendanceRecord::query()->lockForUpdate()->findOrFail($data['attendance_record_id']);
            abort_unless((int) $record->employee_id === (int) $user->employee_id, 404);
            abort_if($record->correctionRequests()->where('status', 'pending')->exists(), 409,
                'يوجد طلب تصحيح قيد المراجعة لهذا السجل.');

            $this->assertTimesValid($record, $data['requested_check_in_time'] ?? null, $data['requested_check_out_time'] ?? null);
            $correction = AttendanceCorrectionRequest::create([
                'attendance_record_id' => $record->id,
                'reason' => $data['reason'],
                'requested_status' => $data['requested_status'],
                'requested_check_in_time' => $this->atRecordDate($record, $data['requested_check_in_time'] ?? null),
                'requested_check_out_time' => $this->atRecordDate($record, $data['requested_check_out_time'] ?? null),
                'status' => 'pending',
            ]);
            AuditLog::record('CreateCorrectionRequest', 'AttendanceCorrectionRequest', $correction->id);

            $reviewers = \App\Models\User::query()
                ->whereHas('roles.permissions', fn ($query) => $query->where('permissions.key', 'attendance.update'))
                ->get();
            foreach ($reviewers as $reviewer) {
                if ($reviewer->id !== $user->id
                    && $reviewer->allowsEmployeeWithinScope('attendance.update', $record->employee)) {
                    $notifications->createForUser($reviewer, 'attendance_correction_pending',
                        'طلب تصحيح حضور جديد', 'طلب '.$record->employee->full_name.' تصحيح سجل حضوره بتاريخ '.$record->date->format('Y-m-d').'.', $correction);
                }
            }
        });

        return back()->with('success', 'تم إرسال طلب التصحيح للمراجعة.');
    }

    public function approve(Request $request, AttendanceCorrectionRequest $correction, NotificationService $notifications): RedirectResponse
    {
        $this->decide($request, $correction, 'approved', $notifications);

        return back()->with('success', 'تم اعتماد تصحيح سجل الحضور.');
    }

    public function reject(Request $request, AttendanceCorrectionRequest $correction, NotificationService $notifications): RedirectResponse
    {
        $this->decide($request, $correction, 'rejected', $notifications);

        return back()->with('success', 'تم رفض طلب التصحيح.');
    }

    private function decide(Request $request, AttendanceCorrectionRequest $correction, string $status, NotificationService $notifications): void
    {
        $user = $request->user();
        abort_unless($user->employee_id !== null && $user->hasPermission('attendance.update'), 403);
        $notes = $request->validate(['admin_notes' => ['nullable', 'string', 'max:1000']])['admin_notes'] ?? null;

        DB::transaction(function () use ($user, $correction, $status, $notes, $notifications): void {
            $locked = AttendanceCorrectionRequest::query()
                ->lockForUpdate()
                ->with('attendanceRecord.employee')
                ->findOrFail($correction->id);
            $record = $locked->attendanceRecord;
            abort_unless($record !== null && $record->employee !== null
                && $user->allowsEmployeeWithinScope('attendance.update', $record->employee), 404);
            abort_unless($locked->status === 'pending', 409, 'تمت مراجعة هذا الطلب مسبقاً.');
            abort_if((int) $record->employee_id === (int) $user->employee_id, 403,
                'لا يمكنك مراجعة طلب تصحيح سجل حضورك.');

            if ($status === 'approved') {
                $lockedRecord = AttendanceRecord::query()->lockForUpdate()->findOrFail($record->id);
                $checkIn = $locked->requested_check_in_time?->format('H:i')
                    ?? $lockedRecord->check_in_time?->format('H:i');
                $checkOut = $locked->requested_check_out_time?->format('H:i')
                    ?? $lockedRecord->check_out_time?->format('H:i');
                $this->assertTimesValid($lockedRecord, $checkIn, $checkOut);
                $lockedRecord->update([
                    'status' => $locked->requested_status,
                    'check_in_time' => $this->atRecordDate($lockedRecord, $checkIn),
                    'check_out_time' => $this->atRecordDate($lockedRecord, $checkOut),
                    'work_minutes' => $checkIn !== null && $checkOut !== null
                        ? (int) round($lockedRecord->date->copy()->setTimeFromTimeString($checkIn)
                            ->diffInMinutes($lockedRecord->date->copy()->setTimeFromTimeString($checkOut), true))
                        : null,
                ]);
                AuditLog::record('ApplyCorrection', 'AttendanceRecord', $lockedRecord->id);
            }

            $locked->update([
                'status' => $status,
                'reviewed_by' => $user->employee_id,
                'reviewed_at' => now(),
                'admin_notes' => $notes,
            ]);
            AuditLog::record(ucfirst($status).'CorrectionRequest', 'AttendanceCorrectionRequest', $locked->id);
            $notifications->createForEmployee($record->employee_id, 'attendance_correction_'.$status,
                $status === 'approved' ? 'تم اعتماد تصحيح الحضور' : 'تم رفض طلب تصحيح الحضور',
                $status === 'approved' ? 'تم اعتماد طلب تصحيح سجل حضورك.' : 'تم رفض طلب تصحيح سجل حضورك.'.($notes ? ' الملاحظة: '.$notes : ''),
                $locked);
        });
    }

    private function assertTimesValid(AttendanceRecord $record, ?string $checkIn, ?string $checkOut): void
    {
        if ($checkIn !== null && $checkOut !== null) {
            $in = $record->date->copy()->setTimeFromTimeString($checkIn);
            $out = $record->date->copy()->setTimeFromTimeString($checkOut);
            if ($out->lessThanOrEqualTo($in)) {
                throw ValidationException::withMessages([
                    'requested_check_out_time' => 'يجب أن يكون وقت الانصراف بعد وقت الحضور.',
                ]);
            }
        }
    }

    private function atRecordDate(AttendanceRecord $record, ?string $time): ?string
    {
        return $time === null ? null : $record->date->toDateString().' '.$time;
    }
}
