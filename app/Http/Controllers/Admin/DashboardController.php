<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGuru = Teacher::count();

        $stats = [
            'total_murid'   => Student::count(),
            'total_guru'    => $totalGuru,
            'murid_aktif'   => Student::where('status', 'aktif')->count(),
            'murid_baru'    => Student::where('join_date', '>=', now()->startOfMonth())->count(),
            'pencapaian'    => $this->calculateAchievement(),
        ];

        $students = Student::with(['classroom', 'parent'])
            ->latest()
            ->take(5)
            ->get();
        $recentActivities = Notification::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();
        $supportWhatsappUrl = Setting::supportWhatsappUrl();

        return view('admin.dashboard', compact('stats', 'students', 'recentActivities', 'supportWhatsappUrl'));
    }

    protected function calculateAchievement(): float
    {
        $total = StudentProgress::count();
        if ($total === 0) return 0;
        $skilled = StudentProgress::where('status', 'T')->count();
        return round(($skilled / $total) * 100);
    }
}
