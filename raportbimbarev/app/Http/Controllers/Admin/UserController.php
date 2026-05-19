<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['guru', 'wali']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users     = $query->orderBy('role')->orderBy('name')->paginate(15);
        $totalUser = User::whereIn('role', ['guru', 'wali'])->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalWali = User::where('role', 'wali')->count();

        return view('admin.user', compact('users', 'totalUser', 'totalGuru', 'totalWali'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:guru,wali',
            'password' => 'nullable|string|min:6',
        ]);

        $oldRole = $user->role;
        $data = $request->only('name', 'email', 'role');

        if ($request->filled('password')) {
            $data['password']       = Hash::make($request->password);
            $data['plain_password'] = $request->password;
        }

        $user->update($data);

        // Sync Teacher record when role changes
        if ($request->role === 'guru' && $oldRole !== 'guru') {
            // Changed TO guru: create Teacher if not exists
            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                ['nip' => Teacher::generateNip(), 'status' => 'aktif']
            );
        } elseif ($request->role !== 'guru' && $oldRole === 'guru') {
            // Changed FROM guru: remove Teacher record
            Teacher::where('user_id', $user->id)->delete();
        }

        return redirect()->route('admin.user')->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        $newPassword = 'password123';
        $user->update([
            'password'       => Hash::make($newPassword),
            'plain_password' => $newPassword,
        ]);

        return redirect()->route('admin.user')->with('success', 'Password ' . $user->name . ' berhasil direset ke: ' . $newPassword);
    }
}
