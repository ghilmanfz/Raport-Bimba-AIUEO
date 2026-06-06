<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrafikController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return redirect()->route('guru.dashboard')->with('error', 'Guru tidak ditemukan.');
        }

        $guidedClassroomIds = Student::where('teacher_id', $teacher->id)
            ->whereNotNull('classroom_id')
            ->distinct()
            ->pluck('classroom_id');

        $classrooms = Classroom::whereIn('id', $guidedClassroomIds)
            ->orderBy('name')
            ->get();

        $selectedClassroom = $request->input('classroom_id');

        $query = Student::where('teacher_id', $teacher->id)
            ->where('status', 'aktif');

        if ($selectedClassroom) {
            $query->where('classroom_id', $selectedClassroom);
        }

        $students = $query->with(['classroom', 'progress'])->orderBy('name')->get();

        // Status distribution
        $allProgress = StudentProgress::whereIn('student_id', $students->pluck('id'))->get();
        $statusCounts = [
            'T' => $allProgress->where('status', 'T')->count(),
            'P' => $allProgress->where('status', 'P')->count(),
            'K' => $allProgress->where('status', 'K')->count(),
        ];
        $totalProgress = array_sum($statusCounts);
        $statusPercent = [
            'T' => $totalProgress > 0 ? round($statusCounts['T'] / $totalProgress * 100) : 0,
            'P' => $totalProgress > 0 ? round($statusCounts['P'] / $totalProgress * 100) : 0,
            'K' => $totalProgress > 0 ? round($statusCounts['K'] / $totalProgress * 100) : 0,
        ];

        // Calculate per-student stats
        $studentStats = [];
        foreach ($students as $s) {
            $prog = $s->progress;
            $total = $prog->count();
            $tCount = $prog->where('status', 'T')->count();
            $latestStatus = $prog->sortByDesc('updated_at')->first()?->status ?? 'K';
            $latestUpdate = $prog->sortByDesc('updated_at')->first()?->updated_at;
            $pct = $total > 0 ? round($tCount / $total * 100) : 0;
            $studentStats[] = [
                'student' => $s,
                'progress_pct' => $pct,
                'latest_status' => $latestStatus,
                'latest_update' => $latestUpdate,
            ];
        }

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $studentStats = collect($studentStats)
                ->filter(fn ($ss) => str_contains(strtolower($ss['student']->name), strtolower($search)))
                ->values()
                ->all();
        }

        return view('guru.grafik', compact('students', 'classrooms', 'selectedClassroom', 'statusCounts', 'statusPercent', 'studentStats', 'search'));
    }
}
