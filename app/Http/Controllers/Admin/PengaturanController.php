<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
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
            'unit_name'           => Setting::get('unit_name', ''),
            'institution_logo'    => Setting::get('institution_logo'),
            'support_whatsapp'    => Setting::get('support_whatsapp', ''),
            'support_email'       => Setting::get('support_email', 'info@bimba-aiueo.com'),
            'landing_badge'       => Setting::get('landing_badge', 'Masa Depan Belajar Anak'),
            'landing_title'       => Setting::get('landing_title', 'E-Rapor'),
            'landing_highlight'   => Setting::get('landing_highlight', 'BiMBA AIUEO'),
            'landing_description' => Setting::get('landing_description', 'Pantau Perkembangan Belajar Anak Secara Digital. Solusi cerdas untuk pendidikan masa kini yang lebih transparan dan efisien.'),
            'landing_cta_title'   => Setting::get('landing_cta_title', 'Siap Mencoba Era Baru Pelaporan Pendidikan?'),
            'landing_cta_description' => Setting::get('landing_cta_description', 'Bergabunglah dengan orang tua dan guru yang telah menggunakan E-Rapor BiMBA AIUEO untuk masa depan pendidikan yang lebih baik.'),
            'hero_image'          => Setting::get('hero_image'),
            'institution_banner'  => Setting::get('institution_banner'),
        ];

        return view('admin.pengaturan', compact('classrooms', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'institution_name'    => 'required|string|max:255',
            'institution_address' => 'required|string|max:500',
            'unit_name'           => 'nullable|string|max:255',
            'support_whatsapp'    => 'nullable|string|max:30',
            'support_email'       => 'nullable|email|max:255',
            'landing_badge'       => 'nullable|string|max:120',
            'landing_title'       => 'nullable|string|max:120',
            'landing_highlight'   => 'nullable|string|max:120',
            'landing_description' => 'nullable|string|max:500',
            'landing_cta_title'   => 'nullable|string|max:180',
            'landing_cta_description' => 'nullable|string|max:500',
            'institution_logo'    => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'hero_image'          => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'institution_banner'  => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
        ]);

        Setting::set('institution_name', $request->institution_name);
        Setting::set('institution_address', $request->institution_address);
        Setting::set('unit_name', $request->unit_name ?? '');
        Setting::set('support_whatsapp', preg_replace('/\D+/', '', $request->support_whatsapp ?? ''));
        Setting::set('support_email', $request->support_email ?? '');
        Setting::set('landing_badge', $request->landing_badge ?? '');
        Setting::set('landing_title', $request->landing_title ?? '');
        Setting::set('landing_highlight', $request->landing_highlight ?? '');
        Setting::set('landing_description', $request->landing_description ?? '');
        Setting::set('landing_cta_title', $request->landing_cta_title ?? '');
        Setting::set('landing_cta_description', $request->landing_cta_description ?? '');

        if ($request->hasFile('institution_logo')) {
            $oldLogo = Setting::get('institution_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('institution_logo')->store('logos', 'public');
            Setting::set('institution_logo', $path);
        }

        if ($request->hasFile('hero_image')) {
            $oldHero = Setting::get('hero_image');
            if ($oldHero && Storage::disk('public')->exists($oldHero)) {
                Storage::disk('public')->delete($oldHero);
            }

            $path = $request->file('hero_image')->store('heroes', 'public');
            Setting::set('hero_image', $path);
        }

        if ($request->hasFile('institution_banner')) {
            $oldBanner = Setting::get('institution_banner');
            if ($oldBanner && Storage::disk('public')->exists($oldBanner)) {
                Storage::disk('public')->delete($oldBanner);
            }

            $path = $request->file('institution_banner')->store('banners', 'public');
            Setting::set('institution_banner', $path);
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

        User::query()
            ->whereKey($user->id)
            ->update(['password' => Hash::make($request->password)]);

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
