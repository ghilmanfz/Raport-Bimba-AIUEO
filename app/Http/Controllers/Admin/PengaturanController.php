<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('students')->orderBy('name')->get();
        $settings   = [
            'institution_name'    => Setting::get('institution_name', 'BiMBA AIUEO Smart Education Centre'),
            'institution_address' => Setting::get('institution_address', 'Jl. Pendidikan No. 45, Jakarta Selatan, DKI Jakarta 12345'),
            'institution_logo'    => Setting::get('institution_logo'),
        ];

        return view('admin.pengaturan', compact('classrooms', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'institution_name'    => 'required|string|max:255',
            'institution_address' => 'required|string|max:500',
            'institution_logo'    => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        Setting::set('institution_name', $request->institution_name);
        Setting::set('institution_address', $request->institution_address);

        if ($request->hasFile('institution_logo')) {
            $oldLogo = Setting::get('institution_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('institution_logo')->store('logos', 'public');
            Setting::set('institution_logo', $path);
        }

        Notification::notifyAdmins(
            'Pengaturan Diperbarui',
            'Pengaturan institusi telah diperbarui oleh ' . Auth::user()->name . '.',
            'info',
            'lucide:settings',
            route('admin.pengaturan')
        );

        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan institusi berhasil disimpan.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        Notification::send(
            $user->id,
            'Kata Sandi Diubah',
            'Kata sandi akun Anda berhasil diperbarui. Jika bukan Anda, segera hubungi admin.',
            'warning',
            'lucide:lock',
            route('admin.pengaturan')
        );

        return redirect()->route('admin.pengaturan')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function storeClassroom(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'level'    => 'required|string|max:50',
            'capacity' => 'required|integer|min:1|max:50',
        ]);

        Classroom::create($request->only('name', 'level', 'capacity'));

        Notification::notifyAdmins(
            'Kelas Baru Ditambahkan',
            'Kelas ' . $request->name . ' (' . $request->level . ') berhasil ditambahkan.',
            'success',
            'lucide:school',
            route('admin.pengaturan')
        );

        return redirect()->route('admin.pengaturan')->with('success', 'Kelas berhasil ditambahkan.');
    }
}
