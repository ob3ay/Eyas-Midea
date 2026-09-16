@extends('layouts.portal')
@section('title', 'الحضور والانصراف')
@section('content')
    <div class="ems-content">
        <div class="ems-page-header">
            <div class="ems-page-title-wrap">
                <div class="ems-breadcrumb">
                    <a href="{{ route('dashboard') }}">الرئيسية</a>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span>الموارد والرقابة</span>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span style="color: var(--ems-text-main); font-weight: 700;">الحضور والانصراف</span>
                </div>
                <h1 class="ems-page-title">سجل الحضور والانصراف والبصمة الإلكترونية</h1>
                <p class="ems-page-subtitle">متابعة دوام كوادر مجموعة إياس الإعلامية وساعات العمل الفعلية والتأخيرات -
                    {{ today()->translatedFormat('l d F Y') }} (دوام رسمي: 10:00 ص - 04:00 م)</p>
            </div>
            @if ($canCreate)
                <div class="ems-page-actions">
                    <button class="ems-btn ems-btn-primary" onclick="openModal('modal-log-attendance')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <span>+ تسجيل يدوي / إذن دوام</span>
                    </button>
                </div>
            @endif
        </div>

        @if (session('success'))
            <div class="ems-badge badge-completed" style="display:block; padding:10px 14px; margin-bottom:14px;">
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="ems-badge"
                style="display:block; padding:10px 14px; margin-bottom:14px; background:#fee2e2; color:#b91c1c;">
                {{ session('error') }}</div>
        @endif

        @if ($canCreate)
            <!-- صندوق تسجيل حضور/انصراف الموظفين (للمستخدم المخوّل فقط) -->
            <div class="ems-attendance-widget-card">
                <div style="display: flex; align-items: center; gap: 18px;">
                    <div
                        style="width: 58px; height: 58px; border-radius: var(--radius-md); background: #ffffff; border: 1px solid var(--ems-gold-border); display: flex; align-items: center; justify-content: center; color: var(--ems-gold-hover); box-shadow: var(--shadow-sm);">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 700; color: var(--ems-text-muted);">التوقيت الفعلي المعتمد
                            (القدس / غزة GMT+3)</div>
                        <div class="ems-time-display ems-live-clock">--:--:--</div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <form action="{{ route('attendance.checkin') }}" method="POST"
                        style="display:flex; align-items:center; gap:8px;">
                        @csrf
                        <select name="employee_id" class="ems-input" required style="min-width: 220px;">
                            <option value="">-- اختر موظف لتسجيل حضوره --</option>
                            @foreach ($notCheckedInEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="ems-btn ems-btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                            <span>تسجيل حضور</span>
                        </button>
                    </form>

                    <form action="{{ route('attendance.checkout') }}" method="POST"
                        style="display:flex; align-items:center; gap:8px;">
                        @csrf
                        <select name="employee_id" class="ems-input" required style="min-width: 220px;">
                            <option value="">-- اختر موظف لتسجيل انصرافه --</option>
                            @foreach ($checkedInNotOutEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="ems-btn ems-btn-secondary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6" />
                            </svg>
                            <span>تسجيل انصراف</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- بطاقات إحصائيات الدوام (ديناميكية) -->
        <!-- بطاقات إحصائيات الدوام (ديناميكية) -->
        <div class="ems-stats-grid">
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">نسبة الالتزام بالدوام</span>
                    <span class="ems-stat-value" style="color: #10b981;">{{ $attendanceRate }}%</span>
                    <span class="ems-stat-subtext">{{ ($viewScope ?? '') === 'self' ? $presentCount . ' يوم حضور مسجل' : $presentCount . ' من أصل ' . $totalEmployees . ' موظفاً' }}</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">حضور في الوقت المحدد</span>
                    <span class="ems-stat-value">{{ $onTimeCount }}</span>
                    <span class="ems-stat-subtext">{{ ($viewScope ?? '') === 'self' ? 'أيام الالتزام بموعد الدوام' : 'قبل أو عند الساعة 10:00 ص' }}</span>
                </div>
                <div class="ems-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">{{ ($viewScope ?? '') === 'self' ? 'مرات التأخير' : 'حالات تأخير اليوم' }}</span>
                    <span class="ems-stat-value" style="color: #d97706;">{{ $lateCount }}</span>
                    <span class="ems-stat-subtext">{{ ($viewScope ?? '') === 'self' ? 'مرات التأخير المسجلة' : 'بعد الساعة 10:00 ص' }}</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">إجازات ومهمات عمل</span>
                    <span class="ems-stat-value" style="color: #6d28d9;">{{ $leaveCount }}</span>
                    <span class="ems-stat-subtext">{{ ($viewScope ?? '') === 'self' ? 'أيام الإجازات المسجلة' : 'مسجلة لليوم الحالي' }}</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 2v4" />
                        <path d="M8 2v4" />
                        <rect width="18" height="18" x="3" y="4" rx="2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- جدول الحضور والانصراف (ديناميكي) -->
        <div class="ems-card">
            <div class="ems-card-header">
                <div class="ems-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span>{{ ($viewScope ?? '') === 'self' ? 'سجل دوامي وحضوري (آخر 30 يوماً)' : 'سجل الحضور اليومي - اليوم: ' . today()->translatedFormat('l d F Y') }}</span>
                </div>
                <span class="ems-badge badge-gold">{{ $records->count() }} سجل</span>
            </div>

            <div class="ems-card-body" style="padding: 0;">
                <div class="ems-table-responsive">
                    <table class="ems-table" id="attendanceTable">
                        <thead>
                            <tr>
                                @if (($viewScope ?? '') === 'self')
                                    <th>اليوم والتاريخ</th>
                                @else
                                    <th>الموظف</th>
                                    <th>الدائرة</th>
                                @endif
                                <th>وقت الحضور (In)</th>
                                <th>وقت الانصراف (Out)</th>
                                <th>ساعات العمل</th>
                                <th>حالة الحضور</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $avatarColors = [
                                    '#c5a059',
                                    '#1e293b',
                                    '#334155',
                                    '#475569',
                                    '#0284c7',
                                    '#0369a1',
                                    '#3b82f6',
                                    '#d97706',
                                ];
                                $statusMeta = [
                                    'Present' => ['label' => 'في الوقت المحدد', 'class' => 'badge-completed'],
                                    'Late' => ['label' => 'متأخر', 'class' => 'badge-progress'],
                                    'Absent' => ['label' => 'غائب', 'class' => ''],
                                    'Leave' => ['label' => 'إجازة/مهمة خارجية', 'class' => 'badge-review'],
                                ];
                            @endphp
                            @forelse ($records as $index => $record)
                                @php $status = $statusMeta[$record->status] ?? ['label' => $record->status, 'class' => '']; @endphp
                                <tr>
                                    @if (($viewScope ?? '') === 'self')
                                        <td>
                                            <strong style="color: var(--ems-text-main);">
                                                {{ $record->date ? \Carbon\Carbon::parse($record->date)->translatedFormat('l, d F Y') : '-' }}
                                            </strong>
                                        </td>
                                    @else
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div class="ems-avatar"
                                                    style="width: 32px; height: 32px; font-size: 12px; background: {{ $avatarColors[$index % count($avatarColors)] }};">
                                                    {{ mb_substr($record->employee->full_name ?? '-', 0, 1) }}</div>
                                                <div>
                                                    <strong
                                                        style="color: var(--ems-text-main);">{{ $record->employee->full_name ?? '-' }}</strong>
                                                    <div style="font-size: 11px; color: var(--ems-text-muted);">
                                                        {{ $record->employee->position ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $record->employee->department->name ?? '-' }}</td>
                                    @endif
                                    <td><span
                                            style="font-family: var(--font-mono); font-weight: 700; color: {{ $record->status === 'Late' ? '#b45309' : '#15803d' }};">{{ $record->check_in_time?->format('h:i A') ?? '--:--' }}</span>
                                    </td>
                                    <td><span
                                            style="font-family: var(--font-mono); color: var(--ems-text-muted);">{{ $record->check_out_time?->format('h:i A') ?? '--:--' }}</span>
                                    </td>
                                    <td><span
                                            style="font-family: var(--font-mono);">{{ $record->work_minutes ? round($record->work_minutes / 60, 1) . ' س' : '--' }}</span>
                                    </td>
                                    <td><span class="ems-badge {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($viewScope ?? '') === 'self' ? 5 : 6 }}" style="text-align:center; color: var(--ems-text-muted);">
                                        ما في سجلات حضور مسجلة بعد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @if ($canCreate)
        <!-- نافذة القيد اليدوي / إذن الدوام -->
        <div class="ems-modal-overlay" id="modal-log-attendance">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">تسجيل يدوي / إذن دوام</div>
                    <button class="ems-modal-close" onclick="closeModal('modal-log-attendance')">✕</button>
                </div>
                <form action="{{ route('attendance.manual') }}" method="POST">
                    @csrf
                    <div class="ems-modal-body">
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الموظف</label>
                                <select name="employee_id" class="ems-select" required>
                                    @foreach ($notCheckedInEmployees->merge($checkedInNotOutEmployees) as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> التاريخ</label>
                                <input type="date" name="date" class="ems-input" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الحالة</label>
                                <select name="status" class="ems-select" required>
                                    <option value="Present">حاضر</option>
                                    <option value="Late">متأخر</option>
                                    <option value="Absent">غائب</option>
                                    <option value="Leave">إجازة / مهمة خارجية</option>
                                </select>
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label">وقت الحضور (اختياري)</label>
                                <input type="time" name="check_in_time" class="ems-input">
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label">وقت الانصراف (اختياري)</label>
                                <input type="time" name="check_out_time" class="ems-input">
                            </div>
                        </div>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary"
                            onclick="closeModal('modal-log-attendance')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ القيد</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
