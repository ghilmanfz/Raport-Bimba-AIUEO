<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ReportPeriodService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;

        if (! $teacher) {
            return view('guru.dashboard', [
                'stats' => ['total_murid' => 0],
                'nextRaporSchedules' => collect(),
            ]);
        }

        $students = Student::where('teacher_id', $teacher->id)
            ->where('status', 'aktif')
            ->with('classroom')
            ->get();

        $stats = ['total_murid' => $students->count()];
        $nextRaporSchedules = $this->buildNextRaporSchedules($students);

        return view('guru.dashboard', compact('stats', 'nextRaporSchedules'));
    }

    protected function buildNextRaporSchedules($students)
    {
        $today = Carbon::today();

        $schedules = $students->filter(fn ($s) => ! empty($s->join_date))->map(function ($student) use ($today) {
            $joinDate = Carbon::parse($student->join_date)->startOfDay();

            $period = app(ReportPeriodService::class)->nextDistribution($student);
            $periodNumber = $period['number'];
            $nextDate = $period['due_date'];

            return [
                'student_name' => $student->name,
                'classroom' => $student->classroom?->name ?? '-',
                'join_date' => $joinDate->translatedFormat('d M Y'),
                'next_date' => $nextDate->translatedFormat('d M Y'),
                'period_number' => $periodNumber,
                'days_left' => $today->diffInDays($nextDate, false),
            ];
        })->sortBy('days_left')->values()->take(8);

        return $schedules;
    }
}
