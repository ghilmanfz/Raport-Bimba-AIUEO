<?php

namespace App\Http\Controllers\Wali;

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
        $children = Auth::user()->students()->with('classroom')->get();
        $selectedChildId = $request->input('student_id', $children->first()?->id);

        $student = $selectedChildId
            ? Student::with(['classroom', 'progress.material', 'teacher.user'])->where('parent_id', Auth::id())->find($selectedChildId)
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

        return view('wali.rapor', compact('children', 'student', 'reportData', 'prevReportData', 'institutionName', 'institutionAddress', 'unitName', 'qrCodeBase64', 'attendanceReport', 'periodOptions'));
    }

    public function riwayat(Request $request)
    {
        $children = Auth::user()->students()->with('classroom')->get();
        $selectedChildId = $request->input('student_id', $children->first()?->id);

        $student = null;
        $riwayatData = [];

        if ($selectedChildId) {
            $student = Student::with(['classroom', 'progress.material', 'teacher.user'])
                ->where('parent_id', Auth::id())
                ->find($selectedChildId);

            if ($student && ($student->join_date ?? $student->created_at)->copy()->startOfDay()->lte(today())) {
                $periods = app(ReportPeriodService::class);
                $currentPeriod = $periods->current($student);

                for ($periodNumber = 1; $periodNumber <= $currentPeriod['number']; $periodNumber++) {
                    $period = $periods->forNumber($student, $periodNumber);
                    $currentDate = $period['start'];
                    $periodEnd = $period['cutoff'];

                    $periodData = [
                        'period' => $periodNumber,
                        'start_date' => $currentDate->format('d M Y'),
                        'end_date' => $periodEnd->format('d M Y'),
                        'end_date_raw' => $periodEnd->format('Y-m-d'),
                        'is_current' => $periodNumber === $currentPeriod['number'],
                    ];

                    $snapshot = app(ProgressReportService::class)->reportData($student, $periodEnd);
                    foreach ($snapshot as $skill => $data) {
                        $periodData['skills'][$skill] = [
                            'total' => $data['details']->count(),
                            'skilled' => $data['details']->where('display_status', 'T')->count(),
                            'percentage' => $data['percentage'],
                        ];
                    }

                    // Calculate average
                    $avgPercentage = 0;
                    if (count($periodData['skills']) > 0) {
                        $sum = array_sum(array_column($periodData['skills'], 'percentage'));
                        $avgPercentage = round($sum / count($periodData['skills']), 1);
                    }
                    $periodData['average'] = $avgPercentage;

                    $riwayatData[] = $periodData;

                }
            }
        }

        return view('wali.riwayat', compact('children', 'student', 'riwayatData'));
    }

    public function cetakPeriode(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'period_end' => 'required|date|before_or_equal:today',
            'period_number' => 'required|integer|min:1',
        ]);

        $student = Student::with(['classroom', 'progress.material', 'teacher.user'])
            ->where('parent_id', Auth::id())
            ->findOrFail($request->student_id);

        $period = app(ReportPeriodService::class)->select($student, $request->only('period_number', 'period_end'));
        $periodNumber = $period['number'];
        $periodStart = $period['start'];
        $periodEnd = $period['cutoff'];
        $attendanceReport = app(AttendanceService::class)->forReport($student, $period);
        $reportData = app(ProgressReportService::class)->reportData($student, $periodEnd);
        $qrCodeBase64 = RaporDownloadController::generateQrBase64($student, $period);

        $institutionName = Setting::get('institution_name', 'BiMBA AIUEO');
        $institutionAddress = Setting::get('institution_address', '');
        $unitName = Setting::get('unit_name', '');

        // Previous period data (not used in print, but required by view)
        $prevReportData = null;

        $periodInfo = [
            'number' => $periodNumber,
            'start' => $periodStart->format('d M Y'),
            'end' => $periodEnd->format('d M Y'),
        ];

        return view('wali.rapor-periode', compact(
            'student',
            'reportData',
            'prevReportData',
            'institutionName',
            'institutionAddress',
            'unitName',
            'qrCodeBase64',
            'periodInfo',
            'attendanceReport'
        ));
    }
}
