<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ProgressReportService;
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
                'stats' => ['total_murid' => 0, 'avg_level' => 0, 'terampil' => 0, 'perlu_perhatian' => 0],
                'students' => collect(),
                'nextRaporSchedules' => collect(),
            ]);
        }

        $students = Student::where('teacher_id', $teacher->id)
            ->where('status', 'aktif')
            ->with(['classroom', 'progress'])
            ->get();

        $totalMurid = $students->count();

        $reports = app(ProgressReportService::class);
        // Calculate students with Terampil status in most assessed materials.
        $terampil = 0;
        $perluPerhatian = 0;
        foreach ($students as $student) {
            $progress = $reports->assessed($student->progress);
            $tCount = $progress->where('display_status', 'T')->count();
            $total = $progress->count();
            if ($total > 0 && ($tCount / $total) >= 0.7) {
                $terampil++;
            }
            $bkCount = $progress->where('display_status', 'K')->count();
            if ($total > 0 && ($bkCount / $total) >= 0.5) {
                $perluPerhatian++;
            }
        }

        $summary = $reports->summarize($students->flatMap->progress);
        $statusCounts = $summary['counts'];
        $statusPercent = $summary['percentages'];

        $stats = [
            'total_murid' => $totalMurid,
            'avg_level' => round($students->avg(fn ($s) => (float) filter_var($s->classroom?->level, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)), 1),
            'terampil' => $terampil,
            'perlu_perhatian' => $perluPerhatian,
            'status_counts' => $statusCounts,
            'status_percent' => $statusPercent,
            'total_assessed' => $summary['total'],
        ];

        $nextRaporSchedules = $this->buildNextRaporSchedules($students);

        // Search filter
        $search = request('search');
        if ($search) {
            $students = $students->filter(fn ($s) => str_contains(strtolower($s->name), strtolower($search)));
        }

        return view('guru.dashboard', compact('stats', 'students', 'nextRaporSchedules'));
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
