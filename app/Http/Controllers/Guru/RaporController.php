<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RaporDownloadController;
use App\Models\Setting;
use App\Models\Student;
use App\Services\AttendanceService;
use App\Services\ProgressReportService;
use App\Services\ReportPeriodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaporController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;

        if (! $teacher) {
            return redirect()->route('guru.dashboard')->with('error', 'Guru tidak ditemukan.');
        }

        $students = Student::where('teacher_id', $teacher->id)
            ->where('status', 'aktif')
            ->with('classroom')
            ->orderBy('name')
            ->get();

        $selectedStudentId = $request->input('student_id', $students->first()?->id);
        $student = $selectedStudentId
            ? Student::where('teacher_id', $teacher->id)
                ->with(['classroom', 'parent', 'progress.material'])
                ->find($selectedStudentId)
            : null;

        $reportData = null;
        $qrCodeBase64 = null;
        $attendanceReport = null;
        $periodOptions = [];
        $prevReportData = null;
        if ($student) {
            $periods = app(ReportPeriodService::class);
            $period = $periods->select($student, $request->only('period_number', 'period_end'));
            $periodOptions = $periods->options($student);
            $reportData = app(ProgressReportService::class)->reportData($student, $period['cutoff']);
            $attendanceReport = app(AttendanceService::class)->forReport($student, $period);
            $qrCodeBase64 = RaporDownloadController::generateQrBase64($student, $period);
            if ($period['number'] === $periods->current($student)['number']) {
                $prevReportData = app(ProgressReportService::class)->reportData($student, now()->startOfMonth()->subDay());
            }
        }

        $institutionName = Setting::get('institution_name', 'BiMBA AIUEO');
        $institutionAddress = Setting::get('institution_address', '');
        $unitName = Setting::get('unit_name', '');

        return view('guru.rapor', compact('students', 'student', 'reportData', 'prevReportData', 'institutionName', 'institutionAddress', 'unitName', 'qrCodeBase64', 'attendanceReport', 'periodOptions'));
    }

    public function saveNotes(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'development_notes' => 'nullable|string|max:2000',
            'period_number' => 'nullable|integer|min:1',
        ]);

        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);
        $student = Student::where('teacher_id', $teacher->id)->find($request->student_id);
        if (! $student) {
            return redirect()->back()->withErrors(['student_id' => 'Murid tidak termasuk bimbingan Anda.']);
        }
        $period = app(ReportPeriodService::class)->select($student, $request->only('period_number'));
        $student->update(['development_notes' => $request->development_notes]);

        return redirect()->route('guru.rapor', ['student_id' => $student->id, 'period_number' => $period['number']])
            ->with('success', 'Catatan perkembangan berhasil disimpan.');
    }
}
