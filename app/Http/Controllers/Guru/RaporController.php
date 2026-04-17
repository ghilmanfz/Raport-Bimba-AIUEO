<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RaporDownloadController;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaporController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classroomIds = $teacher ? $teacher->classrooms()->pluck('classrooms.id') : collect();

        $students = Student::whereIn('classroom_id', $classroomIds)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $selectedStudentId = $request->input('student_id', $students->first()?->id);
        $student = $selectedStudentId
            ? Student::with(['classroom', 'parent', 'progress.material'])->find($selectedStudentId)
            : null;

        $reportData = null;
        $qrCodeBase64 = null;
        if ($student) {
            foreach (['baca', 'tulis', 'hitung'] as $skill) {
                $details = $student->progressBySkill($skill);
                // Group by level
                $grouped = $details->groupBy(fn ($p) => $p->material->level ?? 'Level 1');
                $reportData[$skill] = [
                    'percentage' => $student->skillPercentage($skill),
                    'details'    => $details,
                    'by_level'   => $grouped,
                ];
            }

            // Generate unique QR code for this student's report
            $qrCodeBase64 = RaporDownloadController::generateQrBase64($student);
        }

        $institutionName = Setting::get('institution_name', 'BiMBA AIUEO');
        $institutionAddress = Setting::get('institution_address', '');
        $unitName = Setting::get('unit_name', '');

        // Previous period report data (last month snapshot)
        $prevReportData = null;
        if ($student) {
            $lastMonthEnd = now()->subMonth()->endOfMonth();
            foreach (['baca', 'tulis', 'hitung'] as $skill) {
                $progress = $student->progress()
                    ->whereHas('material', fn ($q) => $q->where('skill_type', $skill));
                $total = $progress->count();
                $skilled = $total > 0
                    ? (clone $progress)->where('status', 'T')->where('skilled_date', '<=', $lastMonthEnd)->count()
                    : 0;
                $prevReportData[$skill] = [
                    'percentage' => $total > 0 ? round(($skilled / $total) * 100, 1) : 0,
                ];
            }
        }

        return view('guru.rapor', compact('students', 'student', 'reportData', 'prevReportData', 'institutionName', 'institutionAddress', 'unitName', 'qrCodeBase64'));
    }

    public function saveNotes(Request $request)
    {
        $request->validate([
            'student_id'        => 'required|exists:students,id',
            'development_notes' => 'nullable|string|max:2000',
        ]);

        $student = Student::findOrFail($request->student_id);
        $student->update(['development_notes' => $request->development_notes]);

        return redirect()->route('guru.rapor', ['student_id' => $student->id])
            ->with('success', 'Catatan perkembangan berhasil disimpan.');
    }
}
