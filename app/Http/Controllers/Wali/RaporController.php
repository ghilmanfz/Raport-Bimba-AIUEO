<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RaporDownloadController;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaporController extends Controller
{
    public function index(Request $request)
    {
        $children = Auth::user()->students()->with('classroom')->get();
        $selectedChildId = $request->input('student_id', $children->first()?->id);

        $student = null;
        $reportData = null;
        $qrCodeBase64 = null;

        if ($selectedChildId) {
            $student = Student::with(['classroom', 'progress.material'])
                ->where('parent_id', Auth::id())
                ->find($selectedChildId);

            if ($student) {
                foreach (['baca', 'tulis', 'hitung'] as $skill) {
                    $details = $student->progressBySkill($skill);
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

        return view('wali.rapor', compact('children', 'student', 'reportData', 'prevReportData', 'institutionName', 'institutionAddress', 'unitName', 'qrCodeBase64'));
    }
}
