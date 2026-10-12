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
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة.',
            ]);
        }

        if ($user->isLocked()) {
            $minutesLeft = now()->diffInMinutes($user->locked_until, false);

            throw ValidationException::withMessages([
                'email' => "الحساب مقفل مؤقتاً بسبب محاولات دخول فاشلة متكررة. يرجى المحاولة بعد {$minutesLeft} دقيقة.",
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'هذا الحساب موقوف حالياً، يرجى التواصل مع الإدارة.',
            ]);
        }

        $credentials = ['email' => $data['email'], 'password' => $data['password']];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $this->handleFailedAttempt($user);

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

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

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

    private function handleFailedAttempt(User $user): void
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
