@extends('layouts.portal')
@section('title', 'التقارير التنفيذية')
@section('content')
<div class="ems-content">
  <div class="ems-page-header">
    <div class="ems-page-title-wrap">
      <div class="ems-breadcrumb">
        <a href="{{ route('dashboard') }}">الرئيسية</a>
        <span class="ems-breadcrumb-separator">/</span>
        <span style="color: var(--ems-text-main); font-weight: 700;">التقارير التنفيذية</span>
      </div>
      <h1 class="ems-page-title">التقارير التنفيذية الشاملة</h1>
      <p class="ems-page-subtitle">
        نظرة تحليلية على الكادر، المشاريع، المهام، والحضور
        @if ($scope !== 'organization')
          <span class="ems-badge badge-gold" style="margin-right:6px;">نطاق العرض: {{ $scope === 'department' ? 'دائرتك/قسمك بس' : 'بياناتك الشخصية بس' }}</span>
        @endif
      </p>
    </div>
  </div>

  <div class="ems-card">
    <div class="ems-card-body">
      <form method="GET" action="{{ route('reports.index') }}" style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap;">
        <div class="ems-form-group" style="margin:0;">
          <label class="ems-label">من تاريخ</label>
          <input type="date" name="from" class="ems-input" value="{{ $from->format('Y-m-d') }}">
        </div>
        <div class="ems-form-group" style="margin:0;">
          <label class="ems-label">إلى تاريخ</label>
          <input type="date" name="to" class="ems-input" value="{{ $to->format('Y-m-d') }}">
        </div>
        <button type="submit" class="ems-btn ems-btn-primary">تطبيق الفترة</button>
      </form>
    </div>
  </div>

  <div class="ems-two-columns">
    <div>
      <div class="ems-card">
        <div class="ems-card-header"><div class="ems-card-title">توزيع الكادر حسب الدوائر</div></div>
        <div class="ems-card-body">
          @forelse ($employeesByDepartment as $row)
          <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--ems-border);">
            <span>{{ $row->department_name }}</span>
            <strong style="font-family: var(--font-mono); color: var(--ems-gold-hover);">{{ $row->total }}</strong>
          </div>
          @empty
          <p style="color:var(--ems-text-muted); font-size:13px;">ما في بيانات ضمن نطاقك.</p>
          @endforelse
        </div>
      </div>

      <div class="ems-card">
        <div class="ems-card-header">
          <div class="ems-card-title">حالة المشاريع</div>
          <span class="ems-badge badge-gold">متوسط الإنجاز {{ $averageProjectCompletion }}%</span>
        </div>
        <div class="ems-card-body">
          @php
            $projectLabels = ['in_progress' => 'قيد التنفيذ', 'in_review' => 'قيد المراجعة', 'completed' => 'مكتمل', 'on_hold' => 'متوقف مؤقتاً'];
          @endphp
          <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; text-align:center;">
            @foreach ($projectLabels as $key => $label)
            <div style="background:var(--ems-surface-alt); padding:12px 6px; border-radius:var(--radius-md); border:1px solid var(--ems-border);">
              <div style="font-size:11px; font-weight:700; color:var(--ems-text-muted);">{{ $label }}</div>
              <div style="font-size:20px; font-weight:800; font-family: var(--font-mono); margin-top:4px;">{{ $projectsByStatus->get($key, 0) }}</div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="ems-card">
        <div class="ems-card-header">
          <div class="ems-card-title">حالة المهام</div>
          <span class="ems-badge" style="background:#fee2e2; color:#b91c1c;">{{ $overdueTasks }} مهمة متأخرة</span>
        </div>
        <div class="ems-card-body">
          @php
            $taskLabels = ['Draft' => 'مسودة', 'To-Do' => 'انتظار', 'In Progress' => 'تنفيذ', 'Under Review' => 'مراجعة', 'Completed' => 'مكتمل', 'Rejected' => 'مرفوضة'];
          @endphp
          <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; text-align:center;">
            @foreach ($taskLabels as $key => $label)
            <div style="background:var(--ems-surface-alt); padding:12px 6px; border-radius:var(--radius-md); border:1px solid var(--ems-border);">
              <div style="font-size:11px; font-weight:700; color:var(--ems-text-muted);">{{ $label }}</div>
              <div style="font-size:20px; font-weight:800; font-family: var(--font-mono); margin-top:4px;">{{ $tasksByStatus->get($key, 0) }}</div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div>
      <div class="ems-card">
        <div class="ems-card-header"><div class="ems-card-title">نسبة الالتزام بالدوام بالفترة</div></div>
        <div class="ems-card-body">
          <div style="text-align:center; padding:10px 0;">
            <div style="font-size:36px; font-weight:800; color:#10b981; font-family: var(--font-mono);">{{ $attendanceRate }}%</div>
            <div style="font-size:12px; color:var(--ems-text-muted);">{{ $from->format('Y-m-d') }} إلى {{ $to->format('Y-m-d') }}</div>
          </div>
          <div style="display:flex; flex-direction:column; gap:8px; margin-top:10px;">
            @php
              $attLabels = ['Present' => 'حضور بالوقت', 'Late' => 'تأخير', 'Absent' => 'غياب', 'Leave' => 'إجازة'];
            @endphp
            @foreach ($attLabels as $key => $label)
            <div style="display:flex; justify-content:space-between; font-size:13px;">
              <span>{{ $label }}</span>
              <strong>{{ $attendanceByStatus->get($key, 0) }}</strong>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      @if ($topLateEmployees->isNotEmpty())
      <div class="ems-card">
        <div class="ems-card-header"><div class="ems-card-title">الأكثر تأخيراً بالفترة</div></div>
        <div class="ems-card-body">
          @foreach ($topLateEmployees as $row)
          <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--ems-border); font-size:13px;">
            <span>{{ $row->employee->full_name ?? '-' }}</span>
            <span style="color:#b45309; font-weight:700;">{{ $row->late_count }} مرة ({{ $row->total_late_minutes }} د)</span>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

