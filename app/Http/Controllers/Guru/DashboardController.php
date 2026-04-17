<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return view('guru.dashboard', [
                'stats' => ['total_murid' => 0, 'avg_level' => 0, 'terampil' => 0, 'perlu_perhatian' => 0],
                'students' => collect(),
            ]);
        }

        $classroomIds = $teacher->classrooms()->pluck('classrooms.id');
        $students = Student::whereIn('classroom_id', $classroomIds)
            ->where('status', 'aktif')
            ->with('classroom')
            ->get();

        $totalMurid = $students->count();

        // Calculate students with Terampil status in most materials
        $terampil = 0;
        $perluPerhatian = 0;
        foreach ($students as $student) {
            $progress = $student->progress;
            $tCount = $progress->where('status', 'T')->count();
            $total = $progress->count();
            if ($total > 0 && ($tCount / $total) >= 0.7) {
                $terampil++;
            }
            $bkCount = $progress->whereIn('status', ['K', 'B'])->count();
            if ($total > 0 && ($bkCount / $total) >= 0.5) {
                $perluPerhatian++;
            }
        }

        // Count status distribution across all progress records
        $allProgress = \App\Models\StudentProgress::whereIn('student_id', $students->pluck('id'))->get();
        $statusCounts = [
            'T' => $allProgress->where('status', 'T')->count(),
            'P' => $allProgress->where('status', 'P')->count(),
            'B' => $allProgress->where('status', 'B')->count(),
            'K' => $allProgress->where('status', 'K')->count(),
        ];
        $totalProgress = array_sum($statusCounts);
        $statusPercent = [
            'T' => $totalProgress > 0 ? round($statusCounts['T'] / $totalProgress * 100) : 0,
            'P' => $totalProgress > 0 ? round($statusCounts['P'] / $totalProgress * 100) : 0,
            'B' => $totalProgress > 0 ? round($statusCounts['B'] / $totalProgress * 100) : 0,
            'K' => $totalProgress > 0 ? round($statusCounts['K'] / $totalProgress * 100) : 0,
        ];

        $stats = [
            'total_murid'     => $totalMurid,
            'avg_level'       => round($students->avg(fn($s) => (float) filter_var($s->classroom?->level, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)), 1),
            'terampil'        => $terampil,
            'perlu_perhatian' => $perluPerhatian,
            'status_counts'   => $statusCounts,
            'status_percent'  => $statusPercent,
        ];

        // Search filter
        $search = request('search');
        if ($search) {
            $students = $students->filter(fn($s) => str_contains(strtolower($s->name), strtolower($search)));
        }

        return view('guru.dashboard', compact('stats', 'students'));
    }
}
