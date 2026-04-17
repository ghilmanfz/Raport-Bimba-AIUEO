<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RaporDownloadController extends Controller
{
    public function download(string $token)
    {
        $student = Student::where('report_token', $token)
            ->with(['classroom', 'parent', 'progress.material', 'progress.teacher.user'])
            ->firstOrFail();

        $reportData = [];
        foreach (['baca', 'tulis', 'hitung'] as $skill) {
            $details = $student->progressBySkill($skill);
            $grouped = $details->groupBy(fn ($p) => $p->material->level ?? 'Level 1');
            $reportData[$skill] = [
                'percentage' => $student->skillPercentage($skill),
                'details'    => $details,
                'by_level'   => $grouped,
            ];
        }

        $institutionName    = Setting::get('institution_name', 'BiMBA AIUEO');
        $institutionAddress = Setting::get('institution_address', '');
        $unitName           = Setting::get('unit_name', '');

        // Generate QR code as base64 SVG
        $downloadUrl = route('rapor.download', $token);
        $qrCodeSvg   = QrCode::format('svg')->size(100)->errorCorrection('M')->generate($downloadUrl);
        $qrCodeBase64 = base64_encode($qrCodeSvg);

        // Find main teacher name from progress
        $teacherName = $student->progress
            ->pluck('teacher')
            ->filter()
            ->first()?->user?->name ?? '';

        $pdf = Pdf::loadView('rapor.pdf', compact(
            'student', 'reportData', 'institutionName', 'institutionAddress', 'unitName',
            'qrCodeBase64', 'teacherName'
        ))->setPaper('a4', 'portrait');

        $filename = 'Rapor_' . str_replace(' ', '_', $student->name) . '_' . now()->format('Y-m') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate QR code as base64 SVG for a student.
     */
    public static function generateQrBase64(Student $student): string
    {
        $downloadUrl = route('rapor.download', $student->report_token);
        $qrCodeSvg   = QrCode::format('svg')->size(120)->errorCorrection('M')->generate($downloadUrl);
        return base64_encode($qrCodeSvg);
    }
}
