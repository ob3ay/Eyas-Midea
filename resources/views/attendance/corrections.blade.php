@extends('layouts.portal')

@section('title', 'طلبات تصحيح الحضور')

@section('content')
    <div class="ems-content">
        <div class="ems-page-header">
            <div>
                <h1 class="ems-page-title">طلبات تصحيح الحضور</h1>
                <p class="ems-page-subtitle">إرسال طلبات التصحيح ومراجعتها ضمن نطاق الصلاحيات.</p>
            </div>
            <a class="ems-btn ems-btn-secondary" href="{{ route('attendance.index') }}">العودة للحضور</a>
        </div>

        @if (session('success'))
            <div class="ems-badge badge-completed" style="display:block;padding:10px 14px;margin-bottom:14px">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="ems-badge" style="display:block;padding:10px 14px;margin-bottom:14px;background:#fee2e2;color:#b91c1c">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        @if ($ownRecords->isNotEmpty())
            <div class="ems-card" style="margin-bottom:20px">
                <div class="ems-card-header"><strong>طلب تصحيح سجل يخصني</strong></div>
                <div class="ems-card-body">
                    <form action="{{ route('attendance.corrections.store') }}" method="POST" class="ems-form-grid">
                        @csrf
                        <div class="ems-form-group">
                            <label class="ems-label" for="attendance_record_id">السجل</label>
                            <select class="ems-select" id="attendance_record_id" name="attendance_record_id" required>
                                @foreach ($ownRecords as $record)
                                    <option value="{{ $record->id }}">{{ $record->date->format('Y-m-d') }} — {{ $record->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label class="ems-label" for="requested_status">الحالة المطلوبة</label>
                            <select class="ems-select" id="requested_status" name="requested_status" required>
                                <option value="Present">حاضر</option><option value="Late">متأخر</option>
                                <option value="Absent">غائب</option><option value="Leave">إجازة</option>
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label class="ems-label" for="requested_check_in_time">وقت الحضور المقترح</label>
                            <input class="ems-input" id="requested_check_in_time" type="time" name="requested_check_in_time">
                        </div>
                        <div class="ems-form-group">
                            <label class="ems-label" for="requested_check_out_time">وقت الانصراف المقترح</label>
                            <input class="ems-input" id="requested_check_out_time" type="time" name="requested_check_out_time">
                        </div>
                        <div class="ems-form-group" style="grid-column:1/-1">
                            <label class="ems-label" for="reason">سبب الطلب</label>
                            <textarea class="ems-input" id="reason" name="reason" rows="3" maxlength="2000" required>{{ old('reason') }}</textarea>
                        </div>
                        <div><button class="ems-btn ems-btn-primary" type="submit">إرسال للمراجعة</button></div>
                    </form>
                </div>
            </div>
        @endif

        <div class="ems-card">
            <div class="ems-card-header"><strong>{{ $canReview ? 'طلبات ضمن نطاق المراجعة' : 'طلباتي' }}</strong></div>
            <div class="ems-table-responsive">
                <table class="ems-table">
                    <thead><tr><th>الموظف</th><th>التاريخ</th><th>التصحيح المطلوب</th><th>السبب</th><th>الحالة</th>@if ($canReview)<th>الإجراء</th>@endif</tr></thead>
                    <tbody>
                        @forelse ($corrections as $correction)
                            @php($record = $correction->attendanceRecord)
                            <tr>
                                <td>{{ $record?->employee?->full_name ?? 'موظف غير محدد' }}</td>
                                <td>{{ $record?->date?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $correction->requested_status }}
                                    @if ($correction->requested_check_in_time) · {{ $correction->requested_check_in_time->format('H:i') }}@endif
                                    @if ($correction->requested_check_out_time) / {{ $correction->requested_check_out_time->format('H:i') }}@endif
                                </td>
                                <td>{{ $correction->reason }}</td>
                                <td>{{ ['pending' => 'قيد المراجعة', 'approved' => 'معتمد', 'rejected' => 'مرفوض'][$correction->status] ?? $correction->status }}</td>
                                @if ($canReview)
                                    <td>
                                        @if ($correction->status === 'pending')
                                            <form action="{{ route('attendance.corrections.approve', $correction) }}" method="POST" style="display:inline-flex;gap:4px">
                                                @csrf
                                                <input class="ems-input" type="text" name="admin_notes" maxlength="1000" placeholder="ملاحظة" aria-label="ملاحظة">
                                                <button class="ems-btn ems-btn-primary" type="submit">اعتماد</button>
                                            </form>
                                            <form action="{{ route('attendance.corrections.reject', $correction) }}" method="POST" style="display:inline-flex;gap:4px">
                                                @csrf
                                                <input class="ems-input" type="text" name="admin_notes" maxlength="1000" placeholder="سبب الرفض" aria-label="سبب الرفض">
                                                <button class="ems-btn ems-btn-secondary" type="submit">رفض</button>
                                            </form>
                                        @else
                                            {{ $correction->reviewer?->full_name ?? '' }}
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center;padding:24px">لا توجد طلبات تصحيح.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="ems-card-body">{{ $corrections->links() }}</div>
        </div>
    </div>
@endsection
