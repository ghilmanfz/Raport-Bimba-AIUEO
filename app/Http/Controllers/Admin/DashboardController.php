<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentProgress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_murid'   => Student::count(),
            'total_guru'    => Teacher::count(),
            'total_kelas'   => Classroom::count(),
            'murid_aktif'   => Student::where('status', 'aktif')->count(),
            'murid_baru'    => Student::where('join_date', '>=', now()->startOfMonth())->count(),
            'pencapaian'    => $this->calculateAchievement(),
        ];

        $students = Student::with(['classroom', 'parent'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'students'));
    }

    protected function calculateAchievement(): float
    {
        $total = StudentProgress::count();
        if ($total === 0) return 0;
        $skilled = StudentProgress::where('status', 'T')->count();
        return round(($skilled / $total) * 100);
    }
}
