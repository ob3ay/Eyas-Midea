<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * عدد المحاولات الفاشلة المسموحة قبل قفل الحساب.
     * (لو حبيت تخليها قابلة للتعديل من system_settings لاحقاً، اسحبها من هناك بدل الثابت).
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * مدة القفل بالدقائق بعد تجاوز عدد المحاولات المسموح.
     */
    private const LOCKOUT_MINUTES = 15;

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        // ملاحظة: فورم الفرونت (resources/views/auth/login.blade.php) عم يبعت
        // الحقل باسم "email" مش "username"، فمنقبله هيك ومنبحث فيه بعمود email.
        // لو حبيت لاحقاً تدعم تسجيل الدخول بالـ username كمان، ضيف حقل بالفورم.
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        // المستخدم مش موجود أصلاً - ما منفرّق بالرسالة حتى ما نعطي معلومة لمهاجم محتمل
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة.',
            ]);
        }

        // الحساب مقفول مؤقتاً بسبب محاولات فاشلة متكررة
        if ($user->isLocked()) {
            $minutesLeft = now()->diffInMinutes($user->locked_until, false);

            throw ValidationException::withMessages([
                'email' => "الحساب مقفل مؤقتاً بسبب محاولات دخول فاشلة متكررة. حاول بعد {$minutesLeft} دقيقة تقريباً.",
            ]);
        }

        // الحساب موقوف من الإدارة (is_active = false)
        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'هذا الحساب موقوف حالياً. تواصل مع الإدارة.',
            ]);
        }

        $credentials = ['email' => $data['email'], 'password' => $data['password']];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $this->handleFailedAttempt($user, $request);

            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة.',
            ]);
        }

        // تسجيل دخول ناجح: تصفير العداد وتحديث آخر دخول
        $request->session()->regenerate();

        $user->forceFill([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
        ])->save();

        AuditLog::record('Login', 'User', $user->id);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            AuditLog::record('Logout', 'User', $user->id);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function handleFailedAttempt(User $user, Request $request): void
    {
        $user->increment('failed_login_attempts');

        if ($user->failed_login_attempts >= self::MAX_ATTEMPTS) {
            $user->forceFill([
                'locked_until' => now()->addMinutes(self::LOCKOUT_MINUTES),
            ])->save();

            AuditLog::record('AccountLocked', 'User', $user->id, null, [
                'failed_login_attempts' => $user->failed_login_attempts,
                'locked_until' => $user->locked_until->toDateTimeString(),
            ]);
        }
    }
}
