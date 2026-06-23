<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = trim($request->email);

        // Cari akun otomatis: cocok dari email (admin/guru/wali)
        // atau dari NIP guru. Peran ditentukan sendiri oleh sistem.
        $user = User::where('email', $identifier)
            ->orWhereHas('teacher', function ($teacherQuery) use ($identifier) {
                $teacherQuery->where('nip', $identifier);
            })
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            Notification::send(
                Auth::id(),
                'Login Berhasil',
                'Anda berhasil login pada ' . now()->translatedFormat('d M Y, H:i') . '.',
                'info',
                'lucide:log-in'
            );

            if ($user->role === 'wali' && (bool) $user->show_password_change_alert) {
                $user->forceFill(['show_password_change_alert' => false])->save();

                return redirect()->route('wali.dashboard')->with(
                    'wali_password_notice',
                    'Demi keamanan akun, silakan segera ubah password bawaan Anda melalui menu Profil > Ganti Password.'
                );
            }

            return $this->redirectByRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email, NIP, atau kata sandi salah.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectByRole($user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru'  => redirect()->route('guru.dashboard'),
            'wali'  => redirect()->route('wali.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
