<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Student;
use App\Services\AttendanceService;
use App\Services\ProgressReportService;
use App\Services\ReportPeriodService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RaporDownloadController extends Controller
{
    public function download(Request $request, string $token)
    {
        $student = Student::where('report_token', $token)
            ->with(['classroom', 'parent', 'progress.material', 'progress.teacher.user'])
            ->firstOrFail();

        $period = app(ReportPeriodService::class)->select($student, $request->only('period_number', 'period_end'));
        $reportData = app(ProgressReportService::class)->reportData($student, $period['cutoff']);

        $institutionName = Setting::get('institution_name', 'BiMBA AIUEO');
        $institutionAddress = Setting::get('institution_address', '');
        $unitName = Setting::get('unit_name', '');

        $qrCodeBase64 = self::generateQrBase64($student, $period);

        // Find main teacher name from progress
        $teacherName = $student->progress
            ->pluck('teacher')
            ->filter()
            ->first()?->user?->name ?? '';

        $attendanceReport = app(AttendanceService::class)->forReport($student, $period);

        $pdf = Pdf::loadView('rapor.pdf', compact(
            'student', 'reportData', 'institutionName', 'institutionAddress', 'unitName',
            'qrCodeBase64', 'teacherName', 'attendanceReport'
        ))->setPaper('a4', 'portrait');

        $filename = 'Rapor_'.str_replace(' ', '_', $student->name).'_'.$period['start']->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate QR code as base64 SVG for a student.
     */
    public static function generateQrBase64(Student $student, ?array $period = null): string
    {
        $params = ['token' => $student->report_token];
        if ($period && $period['cutoff']->gte($period['start'])) {
            $params += ['period_number' => $period['number'], 'period_end' => $period['cutoff']->toDateString()];
        }
        $downloadUrl = route('rapor.download', $params);
        $qrCodeSvg = QrCode::format('svg')->size(120)->errorCorrection('M')->generate($downloadUrl);

        return base64_encode($qrCodeSvg);
    }
}
