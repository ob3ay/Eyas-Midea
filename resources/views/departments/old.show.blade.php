@extends('layouts.portal')
@section('title', 'تفاصيل الدائرة  | مجموعة إياس الإعلامية')

@section('content')


    <!-- مسار التنقل وترويسة الصفحة -->
    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <a href="{{ route('departments.index') }}">الهيكلية المؤسسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color: var(--ems-text-main); font-weight: 700;">تفاصيل الدائرة</span>
            </div>
            <h1 class="ems-page-title">   دائرة الادارة العامة</h1>
            <p class="ems-page-subtitle">تفاصيل الدائرة وإدارة الأقسام والموظفين</p>
        </div>
    </div>


<section class="ems-welcome-banner">
    
        <h1 class="department-name">مرحبا بك في دائرة الادارة العامة </h1>

        <div class="department-content">
            <span class="manager">رئيس الدائرة</span>
            <h2 class="manager-name">م. صالح حسنين</h2>
            {{-- <span class="manager-status">على رأس العمل</span> --}}
        </div>
    


</section>


<style>
    
    .ems-welcome-banner {
        justify-content: space-around;
    }

    .ems-welcome-banner .department-name{
        font-size: 24px;
        font-weight: 700;
        color: #fff;
    }

    .ems-welcome-banner .manager{
        font-size: 12px;
        font-weight: 500;
        color: #fff;
    }

    .ems-welcome-banner .manager-name{
        font-size: 20px;
        font-weight: 700;
        color: #fff;
    }

    .ems-welcome-banner .manager-status{
        font-size: 10px;
        font-weight: 200;
        color: #fff;
    }

</style>
    <!-- 4 بطاقات إحصائية متناسقة مع هوية النظام -->
    <div class="ems-stats-grid">

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الأقسام التابعة</span>
                <span class="ems-stat-value" style="color: #0284c7;">14</span>
                <span class="ems-stat-subtext" style="color: #0284c7;">قسم تابع لهذه الدائرة</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #e0f2fe; border-color: #bae6fd; color: #0369a1;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي الموظفين</span>
                <span class="ems-stat-value" style="color: #10b981;">16</span>
                <span class="ems-stat-subtext" style="color: #10b981;">موظفاً ومسؤولاً نشطاً</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي المشاريع</span>
                <span class="ems-stat-value" style="color: #8b5cf6;">12</span>
                <span class="ems-stat-subtext" style="color: #8b5cf6;">8 من
                    12 مشاريع مكتملة</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
            </div>
        </div>
    </div>


      <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">أقسام دائرة الإدارة العامة</div>
            <button class="ems-btn ems-btn-primary" onclick="addDepartment()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                اضافة قسم
            </button>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="employeesTable" style="text-align: center;">
            <thead>
                <tr>
                    <th>اسم القسم</th>
                    <th>عدد الموظفين</th>
                    <th>المسؤول عن القسم</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>قسم الموارد البشرية</td>
                    <td>5</td>
                    <td>أ. أحمد علي</td>
                    <td>نشط</td>
                    <td style="text-align: center;">
                        <div style="display:flex; justify-content:center; gap:6px;">

                            <a href="{{ route('sections.show', ['section' => 1]) }}"
                                class="ems-btn ems-btn-icon bg-blue-500 text-white" title="عرض التفاصيل">
                                <i class="fa fa-eye"></i>
                            </a>
                                
                                <button type="button" class="ems-btn ems-btn-icon bg-green-500 text-white"
                                    title="تعديل"
                                    onclick="editProject()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>

                                <form action="" method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('هل تريد حذف هذا القسم؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ems-btn ems-btn-icon bg-red-500 text-white"
                                        title="حذف">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1-2-2v2" />
                                        </svg>
                                    </button>
                                </form>
                        </div>
                        
                </tr>
             
            </tbody>
        </table>
            
            </div>
        </div>
    </div>

    
    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">مشاريع دائرة الإدارة العامة</div>
            <button class="ems-btn ems-btn-primary" onclick="addProject()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                اضافة مشروع
            </button>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="projectsTable" style="text-align: center;">
            <thead>
                <tr>
                    <th>اسم المشروع</th>
                    <th>اسم المسؤول</th>
                    <th>نسبة الإنجاز</th>
                    <th>تاريخ البدء</th>
                    <th>تاريخ الانتهاء</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>مشروع تطوير النظام الداخلي</td>
                    <td>فريق B  </td>
                    {{-- Progress --}}

                    @php
                        $progress = 75; //مؤقتة لحد م تسحبها انت 
                    @endphp
                                    <td>

                                        <div
                                            style="
                                            display: flex;
                                            align-items: center;
                                            gap: 8px;
                                        ">

                                            <div class="ems-progress"
                                                style="
                                                height: 6px;
                                                flex-grow: 1;
                                            ">
                                                <div class="ems-progress-bar {{ $progress >= 100 ? 'success' : '' }}"
                                                    style="width: {{ min(100, $progress) }}%;"></div>
                                            </div>

                                            <span
                                                style="
                                                font-size: 11px;
                                                font-weight: 700;
                                                font-family: var(--font-mono);
                                                {{ $progress >= 100 ? 'color: #10b981;' : '' }}
                                            ">
                                                {{ number_format($progress, 0) }}%
                                            </span>

                                        </div>

                                    </td>

                    <td>2024-01-15</td>
                    <td>2024-06-30</td>
                    <td>مكتمل</td>
                    <td style="text-align: center;">
                            <div style="display:flex; justify-content:center; gap:6px;">

                                <a href="#"
                                    class="ems-btn ems-btn-icon bg-blue-500 text-white" title="عرض التفاصيل">
                                    <i class="fa fa-eye"></i>
                                </a>
                                    
                                    <button type="button" class="ems-btn ems-btn-icon bg-green-500 text-white"
                                        title="تعديل"
                                        onclick="editProject()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>

                                    <form action="" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('هل تريد حذف هذا الموظف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ems-btn ems-btn-icon bg-red-500 text-white"
                                            title="حذف">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                            </div>
                    </td>
                </tr>
            </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">موظفي دائرة الإدارة العامة</div>
            <button class="ems-btn ems-btn-primary" onclick="addEmployee()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                اضافة موظف 
            </button>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="employeesTable" style="text-align: center;">
            <thead>
                <tr>
                    <th>اسم الموظف</th>
                    <th>الوظيفة</th>
                    <th>تاريخ الانضمام</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>أ. أحمد علي</td>
                    <td>مدير الموارد البشرية</td>
                    <td>2022-05-10</td>
                    <td>نشط</td>
                     <td style="text-align: center;">
                            <div style="display:flex; justify-content:center; gap:6px;">

                                <a href="#"
                                    class="ems-btn ems-btn-icon bg-blue-500 text-white" title="عرض التفاصيل">
                                    <i class="fa fa-eye"></i>
                                </a>
                                    
                                    <button type="button" class="ems-btn ems-btn-icon bg-green-500 text-white"
                                        title="تعديل"
                                        onclick="editProject()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>

                                    <form action="" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('هل تريد حذف هذا الموظف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ems-btn ems-btn-icon bg-red-500 text-white"
                                            title="حذف">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                            </div>
                    </td>
                </tr>
            
            </tbody>
    <!-- شريط أدوات الفلترة والبحث -->
    {{-- <div class="ems-card" style="margin-bottom: 24px; padding: 14px 20px;">
        <div style="display: flex; flex-wrap: wrap; gap: 14px; align-items: center; justify-content: space-between;">
            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; flex: 1; min-width: 280px;">
                <label style="font-size: 13px; font-weight: 700; color: var(--ems-text-main); white-space: nowrap;">عرض
                    وتحديد دائرة:</label>
                <select id="deptFilterSelect" class="ems-select"
                    style="max-width: 280px; padding: 8px 12px; font-size: 13px;" onchange="focusDepartment(this.value)">
                    <option value="all">كل الدوائر التنظيمية (المخطط الكامل)</option>
                    {{-- @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}"
                            {{ (string) $selectedDeptId === (string) $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code ?? 'DEPT' }})
                        </option>
                    @endforeach --}}
                {{-- </select>

                <div style="position: relative; flex: 1; min-width: 220px; max-width: 360px;">
                    <input type="text" id="orgSearchInput" class="ems-input"
                        placeholder="بحث فوري (دائرة، قسم، موظف)..." style="padding: 8px 36px 8px 12px; font-size: 13px;"
                        oninput="searchOrgChart(this.value)">
                    <svg style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--ems-text-muted); pointer-events: none;"
                        width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

        </div>
        
    </div>  --}}




  


@endsection
