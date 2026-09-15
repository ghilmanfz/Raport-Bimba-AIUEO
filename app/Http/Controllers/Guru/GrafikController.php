<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ProgressReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrafikController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;

        if (! $teacher) {
            return redirect()->route('guru.dashboard')->with('error', 'Guru tidak ditemukan.');
        }

        $validated = $request->validate(['search' => 'nullable|string|max:100']);

        $query = Student::where('teacher_id', $teacher->id)
            ->where('status', 'aktif');

        $search = trim($validated['search'] ?? '');
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('nis', 'like', '%'.$search.'%');
            });
        }
        $students = $query->with(['classroom', 'progress'])->orderBy('name')->get();

        // Status distribution
        $reports = app(ProgressReportService::class);
        $summary = $reports->summarize($students->flatMap->progress);
        $statusCounts = $summary['counts'];
        $statusPercent = $summary['percentages'];
        $totalProgress = $summary['total'];

        // Calculate per-student stats
        $studentStats = [];
        foreach ($students as $s) {
            $prog = $reports->assessed($s->progress);
            $total = $prog->count();
            $tCount = $prog->where('display_status', 'T')->count();
            $latestStatus = $prog->sortByDesc('updated_at')->first()?->display_status;
            $latestUpdate = $prog->sortByDesc('updated_at')->first()?->updated_at;
            $pct = $total > 0 ? round($tCount / $total * 100) : null;
            $studentStats[] = [
                'student' => $s,
                'progress_pct' => $pct,
                'latest_status' => $latestStatus,
                'latest_update' => $latestUpdate,
            ];
        }

        return view('guru.grafik', compact('students', 'statusCounts', 'statusPercent', 'totalProgress', 'studentStats', 'search'));
    }
}
