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
            'role'     => 'required|in:admin,guru,wali',
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = trim($request->email);
        $userQuery = User::where('role', $request->role);

        if ($request->role === 'guru') {
            $userQuery->where(function ($query) use ($identifier) {
                $query->where('email', $identifier)
                    ->orWhereHas('teacher', function ($teacherQuery) use ($identifier) {
                        $teacherQuery->where('nip', $identifier);
                    });
            });
        } else {
            $userQuery->where('email', $identifier);
        }

        $user = $userQuery->first();

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

            return $this->redirectByRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email, NIP, atau kata sandi salah.',
        ])->withInput($request->only('email', 'role'));
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
