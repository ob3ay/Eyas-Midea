@extends('layouts.portal')
@section('title', 'الحضور والانصراف')

@push('styles')
<style>
    /* الفلتر المضغوط الموفر للمساحة */
    .ems-compact-filter-card {
        background: #ffffff;
        border: 1px solid var(--ems-border);
        border-radius: var(--radius-md);
        padding: 10px 16px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .ems-filter-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--ems-border);
        flex-wrap: wrap;
    }
    .ems-filter-chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 14px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid var(--ems-border);
        background: #ffffff;
        color: var(--ems-text-muted);
        transition: all 0.15s ease;
        text-decoration: none;
        line-height: 1.2;
    }
    .ems-filter-chip:hover {
        border-color: var(--ems-gold);
        color: var(--ems-gold-hover);
    }
    .ems-filter-chip.active {
        background: #faf7f0;
        border-color: var(--ems-gold);
        color: var(--ems-gold-hover);
        box-shadow: 0 1px 2px rgba(197, 160, 89, 0.2);
    }
    .ems-compact-form {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }
    .ems-filter-field {
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1 1 125px;
        min-width: 115px;
    }
    .ems-filter-field label {
        font-size: 11px;
        font-weight: 700;
        color: var(--ems-text-muted);
        white-space: nowrap;
    }
    .ems-filter-field .ems-input,
    .ems-filter-field .ems-select {
        height: 34px !important;
        padding: 4px 8px !important;
        font-size: 12px !important;
        border-radius: 6px !important;
        background-color: var(--ems-surface) !important;
        border: 1px solid var(--ems-border) !important;
    }
    .ems-filter-btn-group {
        display: flex;
        align-items: center;
        gap: 6px;
        height: 34px;
        margin-top: auto;
    }
    .ems-filter-btn-group .ems-btn {
        height: 34px !important;
        padding: 0 12px !important;
        font-size: 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        white-space: nowrap !important;
    }

    /* تحسينات الكروت والتبويبات */
    .ems-attendance-widget-card {
        padding: 10px 16px !important;
        margin-bottom: 16px !important;
    }
    .ems-stats-grid {
        gap: 12px !important;
        margin-bottom: 16px !important;
    }
    .ems-stat-card {
        padding: 12px 14px !important;
    }
    .ems-tabs-nav {
        display: flex;
        gap: 8px;
        border-bottom: 2px solid var(--ems-border);
        margin-bottom: 16px;
    }
    .ems-tab-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border: none;
        background: none;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        color: var(--ems-text-muted);
        cursor: pointer;
        position: relative;
        bottom: -2px;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .ems-tab-btn:hover {
        color: var(--ems-gold-hover);
    }
    .ems-tab-btn.active {
        color: var(--ems-gold-hover);
        border-bottom-color: var(--ems-gold);
    }
    .action-icon-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--ems-border);
        background: #ffffff;
        cursor: pointer;
        transition: all 0.15s;
    }
    .action-icon-btn:hover {
        background: var(--ems-surface-alt);
    }
    .action-icon-btn.edit:hover {
        border-color: var(--ems-gold);
        color: var(--ems-gold-hover);
    }
    .action-icon-btn.delete:hover {
        border-color: #f87171;
        color: #ef4444;
    }
</style>
@endpush

@section('content')
    <div class="ems-content">
        <!-- ترويسة الصفحة والعمليات الرئيسية -->
        <div class="ems-page-header" style="margin-bottom: 16px;">
            <div class="ems-page-title-wrap">
                <div class="ems-breadcrumb">
                    <a href="{{ route('dashboard') }}">الرئيسية</a>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span>الموارد والرقابة</span>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span style="color: var(--ems-text-main); font-weight: 700;">الحضور والانصراف</span>
                </div>
                <h1 class="ems-page-title">سجل الحضور والانصراف والبصمة الإلكترونية</h1>
                <p class="ems-page-subtitle">
                    متابعة دوام كوادر مجموعة إياس الإعلامية وساعات العمل الفعلية والتأخيرات - 
                    <span style="color: var(--ems-gold-hover); font-weight: 700;">دوام رسمي معتمد: 10:00 ص إلى 04:00 م</span>
                </p>
            </div>
            
            <div class="ems-page-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
                <!-- زر طباعة كشف الحضور -->
                <a href="{{ route('attendance.print', request()->query()) }}" target="_blank" class="ems-btn ems-btn-secondary" title="معاينة وطباعة كشف الحضور الرسمي A4">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>طباعة كشف الحضور</span>
                </a>

                <!-- زر تصدير إكسل CSV -->
                <a href="{{ route('attendance.export', request()->query()) }}" class="ems-btn ems-btn-secondary" title="تصدير السجلات المعروضة بتنسيق CSV يدعم Excel">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>تصدير Excel (CSV)</span>
                </a>

                @if ($canCreate)
                    <!-- زر القيد اليدوي -->
                    <button class="ems-btn ems-btn-primary" onclick="openModal('modal-log-attendance')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <span>+ تسجيل يدوي / إذن دوام</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- التنبيهات والرسائل -->
        @if (session('success'))
            <div class="ems-badge badge-completed" style="display:block; padding:10px 14px; margin-bottom:14px; font-size:13px; font-weight:700;">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="ems-badge" style="display:block; padding:10px 14px; margin-bottom:14px; background:#fee2e2; color:#b91c1c; font-size:13px; font-weight:700;">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="ems-badge" style="display:block; padding:10px 14px; margin-bottom:14px; background:#fee2e2; color:#b91c1c;">
                @foreach ($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif

        <!-- شريط الفلاتر المضغوط والذكي (موفر للمساحة الرأسية) -->
        <div class="ems-compact-filter-card">
            <!-- الشريط العلوي: الفترات السريعة + عدد السجلات -->
            <div class="ems-filter-toolbar">
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                    <span style="font-size: 11px; font-weight: 800; color: var(--ems-text-main); margin-left: 4px;">الفترات السريعة:</span>
                    <a href="{{ route('attendance.index', array_merge(request()->except('quick_date', 'date_from', 'date_to'), ['quick_date' => 'today'])) }}" 
                       class="ems-filter-chip {{ ($quickDate === 'today' || (empty($quickDate) && !request()->has('date_from') && $viewScope !== 'self')) ? 'active' : '' }}">
                        اليوم
                    </a>
                    <a href="{{ route('attendance.index', array_merge(request()->except('quick_date', 'date_from', 'date_to'), ['quick_date' => 'yesterday'])) }}" 
                       class="ems-filter-chip {{ $quickDate === 'yesterday' ? 'active' : '' }}">
                        أمس
                    </a>
                    <a href="{{ route('attendance.index', array_merge(request()->except('quick_date', 'date_from', 'date_to'), ['quick_date' => 'this_week'])) }}" 
                       class="ems-filter-chip {{ $quickDate === 'this_week' ? 'active' : '' }}">
                        هذا الأسبوع
                    </a>
                    <a href="{{ route('attendance.index', array_merge(request()->except('quick_date', 'date_from', 'date_to'), ['quick_date' => 'this_month'])) }}" 
                       class="ems-filter-chip {{ $quickDate === 'this_month' ? 'active' : '' }}">
                        هذا الشهر
                    </a>
                    <a href="{{ route('attendance.index', array_merge(request()->except('quick_date', 'date_from', 'date_to'), ['quick_date' => 'all'])) }}" 
                       class="ems-filter-chip {{ $quickDate === 'all' ? 'active' : '' }}">
                        كافة السجلات
                    </a>
                </div>
                <div>
                    <span class="ems-badge badge-gold" style="font-size: 11px; padding: 2px 8px;">{{ $totalRecorded }} قيد معروض</span>
                </div>
            </div>

            <!-- استمارة الفلاتر في صف مضغوط متناسق -->
            <form method="GET" action="{{ route('attendance.index') }}" class="ems-compact-form">
                <!-- البحث النصي -->
                <div class="ems-filter-field" style="flex: 1.4; min-width: 130px;">
                    <label>بحث سريع</label>
                    <input type="search" name="search" class="ems-input" value="{{ $search }}" placeholder="الاسم، الرقم أو المسمى...">
                </div>

                <!-- الدائرة الإدارية -->
                @if ($viewScope === 'organization' && $departments->isNotEmpty())
                    <div class="ems-filter-field" style="flex: 1.3; min-width: 140px;">
                        <label>الدائرة الإدارية</label>
                        <select name="department_id" id="filter_department_id" class="ems-select" onchange="filterEmployeesByDepartment(this.value)">
                            <option value="">كافة الدوائر</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" @selected((string)$departmentId === (string)$dept->id)>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- الموظف (يرتبط تلقائياً بالدائرة المحددة) -->
                @if ($viewScope !== 'self' && $allFilterEmployees->isNotEmpty())
                    <div class="ems-filter-field" style="flex: 1.5; min-width: 150px;">
                        <label>الموظف</label>
                        <select name="employee_id" id="filter_employee_id" class="ems-select">
                            <option value="">كافة الموظفين</option>
                            @foreach ($allFilterEmployees as $emp)
                                <option value="{{ $emp->id }}" 
                                        data-department="{{ $emp->department_id }}" 
                                        @selected((string)$employeeId === (string)$emp->id)>
                                    {{ $emp->full_name }} ({{ $emp->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- من تاريخ -->
                <div class="ems-filter-field" style="flex: 1; min-width: 115px;">
                    <label>من تاريخ</label>
                    <input type="date" name="date_from" class="ems-input" value="{{ $dateFrom }}">
                </div>

                <!-- إلى تاريخ -->
                <div class="ems-filter-field" style="flex: 1; min-width: 115px;">
                    <label>إلى تاريخ</label>
                    <input type="date" name="date_to" class="ems-input" value="{{ $dateTo }}">
                </div>

                <!-- حالة الحضور -->
                <div class="ems-filter-field" style="flex: 1; min-width: 115px;">
                    <label>حالة الحضور</label>
                    <select name="status" class="ems-select">
                        <option value="">كافة الحالات</option>
                        <option value="Present" @selected($status === 'Present')>في الوقت المحدد</option>
                        <option value="Late" @selected($status === 'Late')>متأخر</option>
                        <option value="Absent" @selected($status === 'Absent')>غائب</option>
                        <option value="Leave" @selected($status === 'Leave')>إجازة / مهمة</option>
                    </select>
                </div>

                <!-- أزرار الفلترة في نفس السطر مباشرة -->
                <div class="ems-filter-btn-group">
                    <button type="submit" class="ems-btn ems-btn-primary" title="تطبيق الفلترة">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        <span>تطبيق</span>
                    </button>
                    <a href="{{ route('attendance.index') }}" class="ems-btn ems-btn-secondary" title="إعادة ضبط الفلاتر">
                        <span>إعادة ضبط</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- بطاقات الإحصائيات الحية والمتفاعلة مع الفلاتر -->
        <div class="ems-stats-grid">
            <!-- نسبة الالتزام -->
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">نسبة الالتزام بالدوام</span>
                    <span class="ems-stat-value" style="color: #10b981;">{{ $attendanceRate }}%</span>
                    <span class="ems-stat-subtext">
                        {{ $presentCount }} قيد حضور من أصل {{ $totalTargetEmployees }}
                    </span>
                </div>
                <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </div>
            </div>

            <!-- الحضور في الوقت المحدد -->
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">حضور في الوقت المحدد</span>
                    <span class="ems-stat-value">{{ $onTimeCount }}</span>
                    <span class="ems-stat-subtext">قبل أو عند الساعة 10:00 ص</span>
                </div>
                <div class="ems-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
            </div>

            <!-- حالات التأخير -->
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">حالات التأخير المسجلة</span>
                    <span class="ems-stat-value" style="color: #d97706;">{{ $lateCount }}</span>
                    <span class="ems-stat-subtext">
                        {{ $totalLateMinutes > 0 ? "إجمالي {$totalLateMinutes} دقيقة تأخير" : 'بعد الساعة 10:00 ص' }}
                    </span>
                </div>
                <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
            </div>

            <!-- إجازات ومهمات وغياب -->
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">إجازات وغياب</span>
                    <span class="ems-stat-value" style="color: #6d28d9;">{{ $leaveCount + $absentCount }}</span>
                    <span class="ems-stat-subtext">{{ $leaveCount }} إجازة/مهمة • {{ $absentCount }} غياب</span>
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

        @if ($canCreate)
            <!-- كارد تسجيل البصمة والحضور السريع الفعلي اليومي -->
            <div class="ems-attendance-widget-card">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: #ffffff; border: 1px solid var(--ems-gold-border); display: flex; align-items: center; justify-content: center; color: var(--ems-gold-hover); box-shadow: var(--shadow-sm); flex-shrink: 0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--ems-text-muted);">التوقيت الفعلي المعتمد (القدس / غزة GMT+3)</div>
                        <div class="ems-time-display ems-live-clock" style="font-size: 20px; font-weight: 800;">--:--:--</div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <!-- تسجيل حضور سريع -->
                    <form action="{{ route('attendance.checkin') }}" method="POST" style="display:flex; align-items:center; gap:6px;">
                        @csrf
                        <select name="employee_id" class="ems-input" required style="min-width: 200px; font-size: 12px; height: 34px;">
                            <option value="">-- تسجيل حضور موظف ({{ $notCheckedInEmployees->count() }}) --</option>
                            @foreach ($notCheckedInEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="ems-btn ems-btn-primary" style="height: 34px; padding: 0 12px; font-size: 12px;" @disabled($notCheckedInEmployees->isEmpty())>
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                            <span>تسجيل حضور</span>
                        </button>
                    </form>

                    <!-- تسجيل انصراف سريع -->
                    <form action="{{ route('attendance.checkout') }}" method="POST" style="display:flex; align-items:center; gap:6px;">
                        @csrf
                        <select name="employee_id" class="ems-input" required style="min-width: 200px; font-size: 12px; height: 34px;">
                            <option value="">-- تسجيل انصراف موظف ({{ $checkedInNotOutEmployees->count() }}) --</option>
                            @foreach ($checkedInNotOutEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="ems-btn ems-btn-secondary" style="height: 34px; padding: 0 12px; font-size: 12px;" @disabled($checkedInNotOutEmployees->isEmpty())>
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6" />
                            </svg>
                            <span>تسجيل انصراف</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- تبويبات تنظيم العرض -->
        <div class="ems-tabs-nav">
            <button type="button" class="ems-tab-btn active" id="tab-btn-records" onclick="switchAttendanceTab('records')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                <span>كشف الحضور والانصراف المفصل</span>
                <span class="ems-badge badge-gold" style="padding: 2px 8px; font-size: 11px;">{{ $records->count() }}</span>
            </button>

            @if ($canCreate && $viewScope !== 'self')
                <button type="button" class="ems-tab-btn" id="tab-btn-pending" onclick="switchAttendanceTab('pending')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <line x1="17" y1="11" x2="23" y2="11" />
                    </svg>
                    <span>كوادر لم تسجل اليوم</span>
                    <span class="ems-badge" style="background:#fee2e2; color:#b91c1c; padding: 2px 8px; font-size: 11px;">{{ $notCheckedInEmployees->count() }}</span>
                </button>
            @endif
        </div>

        <!-- التبويب الأول: جدول سجلات الحضور والانصراف -->
        <div class="ems-card" id="tab-content-records">
            <div class="ems-card-header">
                <div class="ems-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                    <span>
                        @if ($viewScope === 'self')
                            سجل دوامي وحضوري الشخصي
                        @else
                            بيانات كشف الدوام ({{ $dateFrom && $dateTo && $dateFrom !== $dateTo ? "من {$dateFrom} إلى {$dateTo}" : "تاريخ {$dateFrom}" }})
                        @endif
                    </span>
                </div>
                <div style="font-size: 12px; color: var(--ems-text-muted);">
                    إجمالي الساعات المسجلة: <strong style="color: var(--ems-gold-hover); font-family: var(--font-mono);">{{ round($records->sum('work_minutes') / 60, 1) }} ساعة</strong>
                </div>
            </div>

            <div class="ems-card-body" style="padding: 0;">
                <div class="ems-table-responsive">
                    <table class="ems-table" id="attendanceTable">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">#</th>
                                @if ($viewScope === 'self')
                                    <th>اليوم والتاريخ</th>
                                @else
                                    <th>الموظف</th>
                                    <th>الدائرة</th>
                                    <th>التاريخ</th>
                                @endif
                                <th>وقت الحضور (In)</th>
                                <th>وقت الانصراف (Out)</th>
                                <th>ساعات العمل</th>
                                <th>حالة الحضور وملاحظات</th>
                                @if ($canUpdate || $canDelete)
                                    <th style="width: 90px; text-align: center;">الإجراءات</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $avatarColors = ['#c5a059', '#1e293b', '#334155', '#475569', '#0284c7', '#0369a1', '#3b82f6', '#d97706'];
                                $statusMeta = [
                                    'Present' => ['label' => 'في الوقت المحدد', 'class' => 'badge-completed'],
                                    'Late' => ['label' => 'متأخر', 'class' => 'badge-progress'],
                                    'Absent' => ['label' => 'غائب', 'class' => ''],
                                    'Leave' => ['label' => 'إجازة / مهمة عمل', 'class' => 'badge-review'],
                                ];
                            @endphp
                            @forelse ($records as $index => $record)
                                @php $status = $statusMeta[$record->status] ?? ['label' => $record->status, 'class' => '']; @endphp
                                <tr>
                                    <td style="text-align: center; font-weight: bold; color: var(--ems-text-muted); font-size: 12px;">{{ $index + 1 }}</td>
                                    @if ($viewScope === 'self')
                                        <td>
                                            <strong style="color: var(--ems-text-main);">
                                                {{ $record->date ? \Carbon\Carbon::parse($record->date)->translatedFormat('l, d F Y') : '-' }}
                                            </strong>
                                        </td>
                                    @else
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background: {{ $avatarColors[$index % count($avatarColors)] }};">
                                                    {{ mb_substr($record->employee->full_name ?? '-', 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong style="color: var(--ems-text-main);">{{ $record->employee->full_name ?? '-' }}</strong>
                                                    <div style="font-size: 11px; color: var(--ems-text-muted);">
                                                        <span style="font-family: var(--font-mono); font-weight: bold;">{{ $record->employee->employee_code ?? '' }}</span>
                                                        {{ $record->employee->position ? ' • ' . $record->employee->position : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $record->employee->department->name ?? '-' }}</td>
                                        <td>
                                            <span style="font-size: 12px; font-weight: 600;">
                                                {{ $record->date ? \Carbon\Carbon::parse($record->date)->format('Y-m-d') : '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td>
                                        <span style="font-family: var(--font-mono); font-weight: 700; color: {{ $record->status === 'Late' ? '#b45309' : '#15803d' }};">
                                            {{ $record->check_in_time?->format('h:i A') ?? '--:--' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-family: var(--font-mono); color: var(--ems-text-muted);">
                                            {{ $record->check_out_time?->format('h:i A') ?? '--:--' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-family: var(--font-mono); font-weight: 700;">
                                            {{ $record->work_minutes ? round($record->work_minutes / 60, 1) . ' س' : '--' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span class="ems-badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                            @if ($record->late_minutes > 0)
                                                <span class="ems-badge" style="background:#fef3c7; color:#b45309; font-size:11px;">
                                                    +{{ $record->late_minutes }} د
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    @if ($canUpdate || $canDelete)
                                        <td style="text-align: center;">
                                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                                @if ($canUpdate)
                                                    <button type="button" class="action-icon-btn edit" title="تعديل السجل"
                                                            onclick="openEditModal({
                                                                id: {{ $record->id }},
                                                                name: '{{ addslashes($record->employee->full_name ?? '') }}',
                                                                date: '{{ $record->date ? $record->date->format('Y-m-d') : '' }}',
                                                                checkIn: '{{ $record->check_in_time ? $record->check_in_time->format('H:i') : '' }}',
                                                                checkOut: '{{ $record->check_out_time ? $record->check_out_time->format('H:i') : '' }}',
                                                                status: '{{ $record->status }}',
                                                                lateMinutes: {{ $record->late_minutes ?? 0 }}
                                                            })">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if ($canDelete)
                                                    <button type="button" class="action-icon-btn delete" title="حذف السجل"
                                                            onclick="openDeleteModal({{ $record->id }}, '{{ addslashes($record->employee->full_name ?? '') }}', '{{ $record->date ? $record->date->format('Y-m-d') : '' }}')">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($canUpdate || $canDelete) ? 9 : 8 }}" style="text-align:center; padding: 30px; color: var(--ems-text-muted);">
                                        لا توجد سجلات حضور مطابقة لمعايير البحث المحددة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($canCreate && $viewScope !== 'self')
            <!-- التبويب الثاني: كوادر لم تسجل اليوم -->
            <div class="ems-card" id="tab-content-pending" style="display: none;">
                <div class="ems-card-header">
                    <div class="ems-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>قائمة الكوادر النشطة غير المسجلة لحضور اليوم ({{ today()->translatedFormat('l, d F Y') }})</span>
                    </div>
                    <span class="ems-badge" style="background:#fee2e2; color:#b91c1c;">{{ $notCheckedInEmployees->count() }} موظف معلق</span>
                </div>
                <div class="ems-card-body" style="padding: 0;">
                    <div class="ems-table-responsive">
                        <table class="ems-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px; text-align: center;">#</th>
                                    <th>الموظف</th>
                                    <th>الدائرة الإدارية</th>
                                    <th>المسمى الوظيفي</th>
                                    <th>حالة الحساب</th>
                                    <th style="text-align: center;">تسجيل فوري</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($notCheckedInEmployees as $index => $emp)
                                    <tr>
                                        <td style="text-align: center; font-weight: bold; color: var(--ems-text-muted);">{{ $index + 1 }}</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background: var(--ems-gold-hover); color: #fff;">
                                                    {{ mb_substr($emp->full_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong style="color: var(--ems-text-main);">{{ $emp->full_name }}</strong>
                                                    <div style="font-size: 11px; color: var(--ems-text-muted); font-family: var(--font-mono);">
                                                        {{ $emp->employee_code }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $emp->department->name ?? '-' }}</td>
                                        <td>{{ $emp->position ?? '-' }}</td>
                                        <td>
                                            <span class="ems-badge badge-completed">على رأس عمله</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: inline-flex; gap: 6px;">
                                                <form action="{{ route('attendance.checkin') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                    <button type="submit" class="ems-btn ems-btn-primary" style="padding: 4px 10px; font-size: 12px;">
                                                        تسجيل حضور الآن
                                                    </button>
                                                </form>
                                                <button type="button" class="ems-btn ems-btn-secondary" style="padding: 4px 10px; font-size: 12px;"
                                                        onclick="openManualForEmployee({{ $emp->id }})">
                                                    إجازة / عذر
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 30px; color: #15803d; font-weight: 700;">
                                            رائع! تم تسجيل حضور أو إثبات حالة كافة الموظفين لليوم الحالي بنجاح.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @if ($canCreate)
        <!-- Modal تسجيل يدوي / إذن دوام -->
        <div class="ems-modal-overlay" id="modal-log-attendance">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">تسجيل يدوي / إذن دوام / إجازة</div>
                    <button type="button" class="ems-modal-close" onclick="closeModal('modal-log-attendance')">✕</button>
                </div>
                <form action="{{ route('attendance.manual') }}" method="POST">
                    @csrf
                    <div class="ems-modal-body">
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الموظف</label>
                                <select name="employee_id" id="manual_log_employee_id" class="ems-select" required>
                                    @foreach ($allFilterEmployees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> التاريخ</label>
                                <input type="date" name="date" class="ems-input" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الحالة</label>
                                <select name="status" class="ems-select" required>
                                    <option value="Present">حاضر في الوقت المحدد</option>
                                    <option value="Late">متأخر</option>
                                    <option value="Absent">غائب</option>
                                    <option value="Leave">إجازة / مهمة عمل رسمية</option>
                                </select>
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label">وقت الحضور (In)</label>
                                <input type="time" name="check_in_time" class="ems-input">
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label">وقت الانصراف (Out)</label>
                                <input type="time" name="check_out_time" class="ems-input">
                            </div>
                        </div>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-log-attendance')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ القيد</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($canUpdate)
        <!-- Modal تعديل سجل دوام قائم -->
        <div class="ems-modal-overlay" id="modal-edit-attendance">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">تعديل سجل حضور الموظف: <span id="edit-emp-name" style="color: var(--ems-gold-hover);"></span></div>
                    <button type="button" class="ems-modal-close" onclick="closeModal('modal-edit-attendance')">✕</button>
                </div>
                <form id="editAttendanceForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="ems-modal-body">
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label">تاريخ السجل</label>
                                <input type="text" id="edit-date" class="ems-input" readonly style="background-color: var(--ems-surface-alt); font-weight: bold;">
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الحالة</label>
                                <select name="status" id="edit-status" class="ems-select" required>
                                    <option value="Present">حاضر في الوقت المحدد</option>
                                    <option value="Late">متأخر</option>
                                    <option value="Absent">غائب</option>
                                    <option value="Leave">إجازة / مهمة خارجية</option>
                                </select>
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label">وقت الحضور الفعلي (In)</label>
                                <input type="time" name="check_in_time" id="edit-check-in" class="ems-input">
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label">وقت الانصراف الفعلي (Out)</label>
                                <input type="time" name="check_out_time" id="edit-check-out" class="ems-input">
                            </div>
                        </div>
                        <div class="ems-form-group">
                            <label class="ems-label">دقائق التأخير المحتسبة</label>
                            <input type="number" name="late_minutes" id="edit-late-minutes" class="ems-input" min="0" placeholder="تُحسب تلقائياً إن تركت فارغة">
                        </div>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-edit-attendance')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($canDelete)
        <!-- Modal تأكيد حذف سجل الدوام -->
        <div class="ems-modal-overlay" id="modal-delete-attendance">
            <div class="ems-modal" style="max-width: 440px;">
                <div class="ems-modal-header">
                    <div class="ems-modal-title" style="color: #b91c1c;">تأكيد حذف سجل الدوام</div>
                    <button type="button" class="ems-modal-close" onclick="closeModal('modal-delete-attendance')">✕</button>
                </div>
                <form id="deleteAttendanceForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="ems-modal-body">
                        <p style="font-size: 14px; color: var(--ems-text-main); margin-bottom: 12px;">
                            هل أنت متأكد من رغبتك بحذف سجل دوام الموظف <strong id="delete-emp-name" style="color: var(--ems-navy);"></strong> لتاريخ <strong id="delete-record-date"></strong>؟
                        </p>
                        <p style="font-size: 12px; color: var(--ems-text-muted);">
                            سيتم حذف السجل من الحسابات والإحصائيات، مع إمكانية استرجاعه بواسطة مسؤول النظام عند الحاجة.
                        </p>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-delete-attendance')">إلغاء</button>
                        <button type="submit" class="ems-btn" style="background:#dc2626; color:#ffffff; border-color:#b91c1c;">تأكيد الحذف</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    // ربط اختيار الدائرة بقائمة الموظفين فورياً
    function filterEmployeesByDepartment(deptId) {
        const empSelect = document.getElementById('filter_employee_id');
        if (!empSelect) return;

        const currentEmpVal = empSelect.value;
        let matchFound = false;

        Array.from(empSelect.options).forEach((opt, idx) => {
            if (idx === 0) {
                opt.text = deptId ? '-- كافة موظفي الدائرة --' : '-- كافة الموظفين --';
                return;
            }
            const empDept = opt.getAttribute('data-department');
            if (!deptId || empDept === deptId) {
                opt.hidden = false;
                opt.disabled = false;
                opt.style.display = '';
                if (opt.value === currentEmpVal) {
                    matchFound = true;
                }
            } else {
                opt.hidden = true;
                opt.disabled = true;
                opt.style.display = 'none';
            }
        });

        // إذا كان الموظف المختار لا ينتمي للدائرة الجديدة، نصفر الاختيار
        if (deptId && !matchFound && currentEmpVal) {
            empSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deptSelect = document.getElementById('filter_department_id');
        if (deptSelect && deptSelect.value) {
            filterEmployeesByDepartment(deptSelect.value);
        }
    });

    // التبديل بين تبويبات كشف الحضور وقائمة الكوادر المعلقة
    function switchAttendanceTab(tab) {
        const recordsTab = document.getElementById('tab-content-records');
        const pendingTab = document.getElementById('tab-content-pending');
        const btnRecords = document.getElementById('tab-btn-records');
        const btnPending = document.getElementById('tab-btn-pending');

        if (tab === 'records') {
            if (recordsTab) recordsTab.style.display = 'block';
            if (pendingTab) pendingTab.style.display = 'none';
            if (btnRecords) btnRecords.classList.add('active');
            if (btnPending) btnPending.classList.remove('active');
        } else {
            if (recordsTab) recordsTab.style.display = 'none';
            if (pendingTab) pendingTab.style.display = 'block';
            if (btnRecords) btnRecords.classList.remove('active');
            if (btnPending) btnPending.classList.add('active');
        }
    }

    // فتح نافذة القيد اليدوي لموظف محدد مسبقاً
    function openManualForEmployee(empId) {
        const select = document.getElementById('manual_log_employee_id');
        if (select) {
            select.value = empId;
        }
        openModal('modal-log-attendance');
    }

    // فتح نافذة التعديل وتعبئة البيانات
    function openEditModal(record) {
        document.getElementById('edit-emp-name').innerText = record.name;
        document.getElementById('edit-date').value = record.date;
        document.getElementById('edit-status').value = record.status;
        document.getElementById('edit-check-in').value = record.checkIn;
        document.getElementById('edit-check-out').value = record.checkOut;
        document.getElementById('edit-late-minutes').value = record.lateMinutes || 0;

        const form = document.getElementById('editAttendanceForm');
        form.action = `/attendance/${record.id}`;

        openModal('modal-edit-attendance');
    }

    // فتح نافذة الحذف وتجهيز الرابط
    function openDeleteModal(recordId, empName, date) {
        document.getElementById('delete-emp-name').innerText = empName;
        document.getElementById('delete-record-date').innerText = date;

        const form = document.getElementById('deleteAttendanceForm');
        form.action = `/attendance/${recordId}`;

        openModal('modal-delete-attendance');
    }

    // الساعة الحية للموقع
    function updateLiveClock() {
        const clockEls = document.querySelectorAll('.ems-live-clock');
        if (!clockEls.length) return;

        const now = new Date();
        const options = { timeZone: 'Asia/Gaza', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const timeString = new Intl.DateTimeFormat('ar-PS', options).format(now);

        clockEls.forEach(el => el.textContent = timeString);
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();
</script>
@endpush
