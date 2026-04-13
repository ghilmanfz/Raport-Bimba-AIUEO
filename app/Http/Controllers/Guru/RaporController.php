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

        return view('guru.rapor', compact('students', 'student', 'reportData', 'institutionName', 'institutionAddress', 'qrCodeBase64'));
    }
}
