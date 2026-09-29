<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف الحضور والانصراف الرسمي | مجموعة إياس الإعلامية</title>

    <!-- Google Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --ems-gold: #c5a059;
            --ems-gold-dark: #9a7836;
            --ems-navy: #0f172a;
            --ems-border: #e2e8f0;
            --ems-bg-light: #f8fafc;
            --ems-text: #1e293b;
            --ems-text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f1f5f9;
            color: var(--ems-text);
            direction: rtl;
            font-size: 13px;
            line-height: 1.5;
        }

        /* شريط التحكم بالشاشة */
        .screen-toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #ffffff;
            border-bottom: 2px solid var(--ems-gold);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .toolbar-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--ems-navy);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-gold {
            background: var(--ems-gold);
            color: #ffffff;
            border-color: var(--ems-gold-dark);
        }

        .btn-gold:hover {
            background: var(--ems-gold-dark);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--ems-text);
            border-color: var(--ems-border);
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        /* ورقة الكشف الرسمية للطباعة */
        .sheet-container {
            max-width: 1100px;
            margin: 24px auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        /* ترويسة الكشف */
        .sheet-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--ems-gold);
            padding-bottom: 20px;
            margin-bottom: 24px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            background: #faf8f3;
            border: 1.5px solid var(--ems-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ems-gold);
            font-weight: 900;
            font-size: 22px;
        }

        .brand-text h1 {
            font-size: 20px;
            font-weight: 800;
            color: var(--ems-navy);
            margin-bottom: 2px;
        }

        .brand-text p {
            font-size: 12px;
            font-weight: 600;
            color: var(--ems-gold-dark);
            letter-spacing: 0.5px;
        }

        .header-title-box {
            text-align: left;
        }

        .header-title-box .doc-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--ems-navy);
            background: #faf8f3;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid #f2e7d3;
            display: inline-block;
        }

        .header-title-box .doc-meta {
            font-size: 11px;
            color: var(--ems-text-muted);
            margin-top: 6px;
        }

        /* صندوق معلومات الكشف */
        .meta-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            background: var(--ems-bg-light);
            border: 1px solid var(--ems-border);
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--ems-text-muted);
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: 13px;
            font-weight: 700;
            color: var(--ems-navy);
        }

        /* ملخص الإحصائيات السريع */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 24px;
        }

        .summary-card {
            border: 1px solid var(--ems-border);
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
            background: #ffffff;
        }

        .summary-card .num {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .summary-card .lbl {
            font-size: 11px;
            font-weight: 700;
            color: var(--ems-text-muted);
        }

        /* جدول الكشف */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .report-table th,
        .report-table td {
            padding: 10px 12px;
            text-align: right;
            border: 1px solid #cbd5e1;
        }

        .report-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
        }

        .report-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-present {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .status-late {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .status-absent {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .status-leave {
            background-color: #ede9fe;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        /* مساحة التوقيعات والاعتماد */
        .signatures-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1.5px dashed var(--ems-border);
        }

        .sign-box {
            text-align: center;
            border: 1px solid var(--ems-border);
            border-radius: 6px;
            padding: 16px;
            background: #fafafa;
        }

        .sign-role {
            font-size: 13px;
            font-weight: 800;
            color: var(--ems-navy);
            margin-bottom: 36px;
        }

        .sign-line {
            border-top: 1px solid #94a3b8;
            margin: 0 20px 8px;
        }

        .sign-name {
            font-size: 11px;
            color: var(--ems-text-muted);
            font-weight: 600;
        }

        /* تذييل الصفحة */
        .sheet-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 11px;
            color: var(--ems-text-muted);
            border-top: 1px solid var(--ems-border);
            padding-top: 12px;
        }

        /* قواعد الطباعة */
        @media print {
            body {
                background: #ffffff;
                font-size: 11px;
            }

            .screen-toolbar {
                display: none !important;
            }

            .sheet-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .report-table th {
                background-color: #0f172a !important;
                color: #ffffff !important;
            }

            .signatures-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- شريط التحكم بالشاشة -->
    <div class="screen-toolbar">
        <div class="toolbar-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>معاينة كشف الحضور والانصراف للطباعة</span>
        </div>
        <div class="toolbar-actions">
            <button onclick="window.print()" class="btn btn-gold">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>طباعة الكشف الآن (A4)</span>
            </button>
            <button onclick="window.close()" class="btn btn-secondary">إغلاق النافذة</button>
        </div>
    </div>

    <!-- ورقة الكشف الرسمية -->
    <div class="sheet-container">

        <!-- الترويسة الرسمية -->
        <header class="sheet-header">
            <div class="header-brand">
                <div class="brand-logo">
                    EYAS
                </div>
                <div class="brand-text">
                    <h1>مجموعة إياس الإعلامية</h1>
                    <p>EYAS MEDIA GROUP - نظام الموارد البشرية والرقابة</p>
                </div>
            </div>
            <div class="header-title-box">
                <div class="doc-title">كشف الحضور والانصراف المعتمد</div>
                <div class="doc-meta">تاريخ الاستخراج: {{ now()->translatedFormat('d F Y - h:i A') }}</div>
            </div>
        </header>

        <!-- شريط تفاصيل المعايير والفترة -->
        <div class="meta-strip">
            <div class="meta-item">
                <span class="meta-label">الفترة المستخرجة:</span>
                <span class="meta-value">
                    @if ($dateFrom && $dateTo && $dateFrom !== $dateTo)
                        من {{ $dateFrom }} إلى {{ $dateTo }}
                    @elseif ($dateFrom)
                        يوم {{ $dateFrom }}
                    @else
                        كافة السجلات المسجلة
                    @endif
                </span>
            </div>
            <div class="meta-item">
                <span class="meta-label">الدائرة الإدارية:</span>
                <span class="meta-value">{{ $selectedDepartment ? $selectedDepartment->name : 'كافة دوائر المجموعة' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">الموظف المستهدف:</span>
                <span class="meta-value">{{ $selectedEmployee ? $selectedEmployee->full_name : 'كافة الكوادر' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">حالة التصفية:</span>
                <span class="meta-value">
                    @if ($status === 'Present') حاضر بالوقت المحدد
                    @elseif ($status === 'Late') حالات متأخرة فقط
                    @elseif ($status === 'Absent') غياب
                    @elseif ($status === 'Leave') إجازات ومهمات
                    @else شامل كافة الحالات
                    @endif
                </span>
            </div>
        </div>

        <!-- بطاقات ملخص الإحصائيات -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="num" style="color: var(--ems-navy);">{{ $totalCount }}</div>
                <div class="lbl">إجمالي القيود المسجلة</div>
            </div>
            <div class="summary-card">
                <div class="num" style="color: #15803d;">{{ $presentCount }}</div>
                <div class="lbl">حضور في الوقت</div>
            </div>
            <div class="summary-card">
                <div class="num" style="color: #b45309;">{{ $lateCount }} ({{ $totalLateMinutes }} د)</div>
                <div class="lbl">تأخير (إجمالي الدقائق)</div>
            </div>
            <div class="summary-card">
                <div class="num" style="color: #6d28d9;">{{ $leaveCount }}</div>
                <div class="lbl">إجازات ومهمات</div>
            </div>
            <div class="summary-card">
                <div class="num" style="color: var(--ems-gold-dark);">{{ $totalWorkHours }} س</div>
                <div class="lbl">إجمالي ساعات العمل</div>
            </div>
        </div>

        <!-- جدول الكشف -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">#</th>
                    <th>الرقم الوظيفي</th>
                    <th>اسم الموظف</th>
                    <th>الدائرة الإدارية</th>
                    <th>التاريخ واليوم</th>
                    <th>وقت الحضور</th>
                    <th>وقت الانصراف</th>
                    <th>ساعات العمل</th>
                    <th>الحالة</th>
                    <th>ملاحظات التأخير</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $index => $record)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                        <td><strong style="font-family: monospace;">{{ $record->employee->employee_code ?? '-' }}</strong></td>
                        <td>
                            <strong>{{ $record->employee->full_name ?? '-' }}</strong>
                            <div style="font-size: 10px; color: var(--ems-text-muted);">{{ $record->employee->position ?? '' }}</div>
                        </td>
                        <td>{{ $record->employee->department->name ?? '-' }}</td>
                        <td>
                            {{ $record->date ? \Carbon\Carbon::parse($record->date)->translatedFormat('Y-m-d (l)') : '-' }}
                        </td>
                        <td style="font-family: monospace; font-weight: bold; color: {{ $record->status === 'Late' ? '#b45309' : '#15803d' }};">
                            {{ $record->check_in_time ? $record->check_in_time->format('h:i A') : '--:--' }}
                        </td>
                        <td style="font-family: monospace; color: var(--ems-text-muted);">
                            {{ $record->check_out_time ? $record->check_out_time->format('h:i A') : '--:--' }}
                        </td>
                        <td style="font-family: monospace; font-weight: bold;">
                            {{ $record->work_minutes ? round($record->work_minutes / 60, 1) . ' س' : '--' }}
                        </td>
                        <td>
                            @if ($record->status === 'Present')
                                <span class="badge-status status-present">حاضر بالوقت</span>
                            @elseif ($record->status === 'Late')
                                <span class="badge-status status-late">متأخر</span>
                            @elseif ($record->status === 'Absent')
                                <span class="badge-status status-absent">غائب</span>
                            @elseif ($record->status === 'Leave')
                                <span class="badge-status status-leave">إجازة / مهمة</span>
                            @else
                                {{ $record->status }}
                            @endif
                        </td>
                        <td>
                            @if ($record->late_minutes > 0)
                                <span style="color: #b45309; font-weight: bold;">متأخر {{ $record->late_minutes }} دقيقة</span>
                            @else
                                <span style="color: var(--ems-text-muted); font-size: 11px;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 24px; color: var(--ems-text-muted);">
                            لا توجد قيود حضور مطابقة لمعايير البحث المحددة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- مساحة التوقيع والاعتماد الرسمي -->
        <div class="signatures-section">
            <div class="sign-box">
                <div class="sign-role">إعداد: مسؤول شؤون الموظفين</div>
                <div class="sign-line"></div>
                <div class="sign-name">التوقيع والختم: ........................</div>
            </div>
            <div class="sign-box">
                <div class="sign-role">تدقيق: المدير الإداري والمالي</div>
                <div class="sign-line"></div>
                <div class="sign-name">التوقيع والختم: ........................</div>
            </div>
            <div class="sign-box">
                <div class="sign-role">اعتماد: المدير التنفيذي للمجموعة</div>
                <div class="sign-line"></div>
                <div class="sign-name">التوقيع والختم: ........................</div>
            </div>
        </div>

        <!-- تذييل الصفحة -->
        <div class="sheet-footer">
            مجموعة إياس الإعلامية | كشف نظام الدوام والرقابة الإلكترونية الداخلي | تم التوليد آلياً بواسطة المستخدم: {{ auth()->user()->name }}
        </div>

    </div>

</body>
</html>
