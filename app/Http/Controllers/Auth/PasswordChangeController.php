<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    public function edit(): View
    {
        return view('auth.password-change');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(12)->mixedCase()->numbers(),
            ],
        ], [
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
            'password.different' => 'يجب أن تختلف كلمة المرور الجديدة عن الحالية.',
        ]);

        $user = $request->user();
        DB::transaction(function () use ($user, $data): void {
            $user->forceFill([
                'password' => $data['password'],
                'must_change_password' => false,
            ])->save();
            AuditLog::record('ChangePassword', 'User', $user->id);
        });

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }
}
