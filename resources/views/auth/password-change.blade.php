@extends('layouts.portal')

@section('title', 'تغيير كلمة المرور')

@section('content')
    <div class="ems-page-header">
        <div>
            <h1 class="ems-page-title">تغيير كلمة المرور</h1>
            <p class="ems-page-subtitle">يجب تعيين كلمة مرور جديدة قبل متابعة استخدام النظام.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="ems-alert ems-alert-danger" role="alert">
            <ul style="margin: 0; padding-inline-start: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ems-card" style="max-width: 640px;">
        <form method="POST" action="{{ route('password.change.update') }}">
            @csrf
            @method('PUT')

            <div class="ems-form-group">
                <label class="ems-label" for="current_password">كلمة المرور الحالية</label>
                <input class="ems-input" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
            </div>

            <div class="ems-form-group">
                <label class="ems-label" for="password">كلمة المرور الجديدة</label>
                <input class="ems-input" id="password" name="password" type="password" autocomplete="new-password" minlength="12" required>
                <small>يجب ألا تقل عن 12 حرفاً وأن تحتوي على أحرف كبيرة وصغيرة وأرقام.</small>
            </div>

            <div class="ems-form-group">
                <label class="ems-label" for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                <input class="ems-input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" required>
            </div>

            <button class="ems-btn ems-btn-primary" type="submit">حفظ كلمة المرور الجديدة</button>
        </form>
    </div>
@endsection
