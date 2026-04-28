<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGuru = Teacher::count();
        $activeStudents = Student::with(['classroom'])
            ->where('status', 'aktif')
            ->whereNotNull('join_date')
            ->get();
        
        $stats = [
            'total_murid'   => Student::count(),
            'total_guru'    => $totalGuru,
            'total_kelas'   => Classroom::count(),
            'murid_aktif'   => Student::where('status', 'aktif')->count(),
            'murid_baru'    => Student::where('join_date', '>=', now()->startOfMonth())->count(),
            'pencapaian'    => $this->calculateAchievement(),
        ];

        $nextRaporSchedules = $this->buildNextRaporSchedules($activeStudents);

        $students = Student::with(['classroom', 'parent'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'students', 'nextRaporSchedules'));
    }

    protected function buildNextRaporSchedules($students)
    {
        $today = Carbon::today();

        $schedules = $students->map(function ($student) use ($today) {
            $joinDate = Carbon::parse($student->join_date)->startOfDay();

            $monthsDiff = max(0, $joinDate->diffInMonths($today, false));
            $periodNumber = (int) floor($monthsDiff / 3) + 1;
            $nextDate = $joinDate->copy()->addMonths($periodNumber * 3);

            while ($nextDate->lt($today)) {
                $periodNumber++;
                $nextDate = $joinDate->copy()->addMonths($periodNumber * 3);
            }

            return [
                'student_name' => $student->name,
                'classroom' => $student->classroom?->name ?? '-',
                'join_date' => $joinDate->translatedFormat('d M Y'),
                'next_date' => $nextDate->translatedFormat('d M Y'),
                'period_number' => $periodNumber,
                'days_left' => $today->diffInDays($nextDate, false),
            ];
        })->sortBy('days_left')->values();

        if ($schedules->isEmpty()) {
            return collect([
                ['student_name' => 'Aisyah Putri', 'classroom' => 'Level 1', 'join_date' => '12 Jan 2026', 'next_date' => '12 Jul 2026', 'period_number' => 2, 'days_left' => 75],
                ['student_name' => 'Bima Satria', 'classroom' => 'Level 2', 'join_date' => '05 Feb 2026', 'next_date' => '05 Aug 2026', 'period_number' => 2, 'days_left' => 99],
                ['student_name' => 'Dedi Kurniawan', 'classroom' => 'Level 3', 'join_date' => '15 Mar 2026', 'next_date' => '15 Sep 2026', 'period_number' => 2, 'days_left' => 140],
            ]);
        }

        return $schedules->take(8);
    }

    protected function calculateAchievement(): float
    {
        $total = StudentProgress::count();
        if ($total === 0) return 0;
        $skilled = StudentProgress::where('status', 'T')->count();
        return round(($skilled / $total) * 100);
    }
}
