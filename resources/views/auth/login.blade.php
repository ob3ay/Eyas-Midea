<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول | مجموعة إياس الإعلامية - EMS</title>
  <link rel="stylesheet" href="{{ asset('assets/css/portal.css') }}">
  <style>
    .ems-auth-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      background: radial-gradient(circle at 50% 20%, rgba(197, 160, 89, 0.08) 0%, #f8fafc 70%);
    }
    .ems-auth-card {
      width: 100%;
      max-width: 440px;
      background-color: var(--ems-surface);
      border: 1px solid var(--ems-border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-md);
      padding: 36px 32px;
      position: relative;
    }
    .ems-auth-header {
      text-align: center;
      margin-bottom: 28px;
    }
    .ems-auth-logo-box {
      width: 68px;
      height: 68px;
      margin: 0 auto 16px;
      background: #ffffff;
      border: 1px solid var(--ems-gold-border);
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 8px;
      box-shadow: var(--shadow-sm);
    }
    .ems-auth-logo {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    .ems-auth-title {
      font-size: 20px;
      font-weight: 800;
      color: var(--ems-text-main);
      margin-bottom: 4px;
    }
    .ems-auth-desc {
      font-size: 13px;
      color: var(--ems-text-muted);
    }
    .ems-auth-footer {
      text-align: center;
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid var(--ems-border-light);
      font-size: 12px;
      color: var(--ems-text-light);
    }

    .password-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    .password-wrapper .ems-input {
      padding-left: 42px;
    }
    .password-toggle-btn {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      padding: 6px;
      cursor: pointer;
      color: var(--ems-text-light);
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: var(--radius-sm);
      transition: color 0.15s ease, background-color 0.15s ease;
    }
    .password-toggle-btn:hover {
      color: var(--ems-gold-hover);
      background-color: rgba(197, 160, 89, 0.08);
    }
    .password-toggle-btn:focus {
      outline: none;
    }
  </style>
</head>
<body class="ems-auth-page">

  <div class="ems-auth-card">
    <div class="ems-auth-header">
      <div class="ems-auth-logo-box">
        <img src="{{ asset('assets/images/logo-gold.png') }}" alt="شعار مجموعة إياس الإعلامية" class="ems-auth-logo" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%23C5A059\'><circle cx=\'12\' cy=\'12\' r=\'10\'/></svg>'">
      </div>
      <h1 class="ems-auth-title">مجموعة إياس الإعلامية</h1>
      <p class="ems-auth-desc">بوابة الإدارة الرقمية الموحدة (EMS)</p>
    </div>

    <!-- نموذج تسجيل الدخول الرسمي لـ Laravel -->
    <form action="{{ route('login.post') }}" method="POST" id="loginForm">
      @csrf

      @if($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 10px 14px; border-radius: var(--radius-md); font-size: 12.5px; margin-bottom: 18px;">
          {{ $errors->first() }}
        </div>
      @endif

      <div class="ems-form-group">
        <label for="email" class="ems-label"><span class="required">*</span> البريد الإلكتروني المؤسسي</label>
        <input type="email" id="email" name="email" class="ems-input" value="{{ old('email') }}" required autofocus placeholder="name@eyasmedia.com">
      </div>

      <div class="ems-form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px;">
          <label for="password" class="ems-label" style="margin-bottom: 0;"><span class="required">*</span> كلمة المرور</label>
          <a href="#" style="font-size: 11.5px; color: var(--ems-gold-hover); font-weight: 600;">نسيت كلمة المرور؟</a>
        </div>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" class="ems-input" required placeholder="••••••••">
          <button type="button" class="password-toggle-btn" id="togglePasswordBtn" onclick="togglePasswordVisibility()" aria-label="عرض أو إخفاء كلمة المرور" title="إظهار / إخفاء كلمة المرور">
            <svg id="eyeIcon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <svg id="eyeOffIcon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
              <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
          </button>
        </div>
      </div>

      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--ems-text-muted); cursor: pointer;">
          <input type="checkbox" name="remember" id="remember" style="accent-color: var(--ems-gold); width: 16px; height: 16px;">
          <span>تذكر بيانات دخولي</span>
        </label>
        <span class="ems-badge badge-gold" style="font-size: 11px;">اتصال مشفر SSL</span>
      </div>

      <button type="submit" class="ems-btn ems-btn-primary" style="width: 100%; padding: 12px; font-size: 14.5px;">
        <span>تسجيل الدخول إلى البوابة</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </button>
    </form>

    <div class="ems-auth-footer">
      نظام إدارة مجموعة إياس الإعلامية • جميع الحقوق محفوظة © 2026
    </div>
  </div>

  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      const eyeOffIcon = document.getElementById('eyeOffIcon');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.style.display = 'none';
        eyeOffIcon.style.display = 'block';
      } else {
        passwordInput.type = 'password';
        eyeIcon.style.display = 'block';
        eyeOffIcon.style.display = 'none';
      }
    }
  </script>
</body>
</html>
