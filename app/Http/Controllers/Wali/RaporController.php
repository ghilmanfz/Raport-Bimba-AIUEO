<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RaporDownloadController;
use App\Models\Material;
use App\Models\Setting;
use App\Models\Student;
use App\Services\AttendanceService;
use App\Services\ReportPeriodService;
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
            $student = Student::with(['classroom', 'progress.material', 'teacher.user'])
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
                    ->whereHas('material', fn ($q) => $q->where('skill_type', $skill))
                    ->get()
                    ->filter(fn ($item) => $item->display_status !== '');

                $total = $progress->count();
                $skilled = $total > 0
                    ? $progress->where('status', 'T')->where('skilled_date', '<=', $lastMonthEnd)->count()
                    : 0;
                $prevReportData[$skill] = [
                    'percentage' => $total > 0 ? round(($skilled / $total) * 100, 1) : 0,
                ];
            }
        }

        $attendanceReport = $student ? app(AttendanceService::class)->forReport($student) : null;

        return view('wali.rapor', compact('children', 'student', 'reportData', 'prevReportData', 'institutionName', 'institutionAddress', 'unitName', 'qrCodeBase64', 'attendanceReport'));
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

                    // Calculate progress for each skill at this period
                    foreach (['baca', 'tulis', 'hitung'] as $skill) {
                        $progress = $student->progress()
                            ->whereHas('material', fn ($q) => $q->where('skill_type', $skill))
                            ->get()
                            ->filter(fn ($item) => $item->display_status !== '');
                        
                        $total = $progress->count();
                        $skilled = $total > 0
                            ? $progress->where('status', 'T')
                                ->where('skilled_date', '<=', $periodEnd)
                                ->count()
                            : 0;
                        
                        $periodData['skills'][$skill] = [
                            'total' => $total,
                            'skilled' => $skilled,
                            'percentage' => $total > 0 ? round(($skilled / $total) * 100, 1) : 0,
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

        $periods = app(ReportPeriodService::class);
        $periodNumber = (int) $request->period_number;
        abort_if($periodNumber > $periods->current($student)['number'], 404);
        $period = $periods->forNumber($student, $periodNumber);
        $periodStart = $period['start'];
        $requestedEnd = \Carbon\CarbonImmutable::parse($request->period_end)->startOfDay();
        abort_if($requestedEnd->lt($periodStart) || $requestedEnd->gt($period['cutoff']), 422);
        $periodEnd = $requestedEnd;
        $period['cutoff'] = $periodEnd;
        $attendanceReport = app(AttendanceService::class)->forReport($student, $period);

        $reportData = [];
        $qrCodeBase64 = null;

        // Generate report data snapshot at period_end
        foreach (['baca', 'tulis', 'hitung'] as $skill) {
            $materials = Material::where('skill_type', $skill)
                ->orderBy('sort_order')
                ->get();

            $progressByMaterial = $student->progress()
                ->whereHas('material', fn ($q) => $q->where('skill_type', $skill))
                ->with('material')
                ->get()
                ->keyBy('material_id');

            $details = $materials->map(function ($material) use ($progressByMaterial, $periodEnd) {
                $prog = $progressByMaterial->get($material->id);
                $status = '';
                $startDate = '';
                $understandDate = '';
                $skilledDate = '';

                if ($prog) {
                    if ($prog->skilled_date) {
                        $date = \Carbon\Carbon::parse($prog->skilled_date);
                        if ($date->lte($periodEnd)) {
                            $status = 'T';
                            $skilledDate = $date->format('Y-m-d');
                        }
                    }
                    if (!$status && $prog->understand_date) {
                        $date = \Carbon\Carbon::parse($prog->understand_date);
                        if ($date->lte($periodEnd)) {
                            $status = 'P';
                            $understandDate = $date->format('Y-m-d');
                        }
                    }
                    if (!$status && $prog->start_date) {
                        $date = \Carbon\Carbon::parse($prog->start_date);
                        if ($date->lte($periodEnd)) {
                            $status = 'K';
                            $startDate = $date->format('Y-m-d');
                        }
                    }

                    // If a later milestone exists before the period but an earlier milestone also exists, preserve the earlier date for raport snapshot
                    if ($prog->start_date) {
                        $date = \Carbon\Carbon::parse($prog->start_date);
                        if ($date->lte($periodEnd)) {
                            $startDate = $date->format('Y-m-d');
                        }
                    }
                    if ($prog->understand_date) {
                        $date = \Carbon\Carbon::parse($prog->understand_date);
                        if ($date->lte($periodEnd)) {
                            $understandDate = $date->format('Y-m-d');
                        }
                    }
                    if ($prog->skilled_date) {
                        $date = \Carbon\Carbon::parse($prog->skilled_date);
                        if ($date->lte($periodEnd)) {
                            $skilledDate = $date->format('Y-m-d');
                        }
                    }
                }

                return [
                    'material' => $material,
                    'start_date' => $startDate,
                    'understand_date' => $understandDate,
                    'skilled_date' => $skilledDate,
                    'status' => $status,
                    'display_status' => $status,
                ];
            });

            // Only show materials that already have a status (K/P/T) at this period.
            // Materials without any status yet ("kosong") are hidden from the report.
            $details = $details->filter(fn ($row) => $row['display_status'] !== '')->values();

            $grouped = $details->groupBy(fn ($row) => $row['material']->level ?? 'Level 1');

            $total = $details->count();
            $skilled = $details->where('status', 'T')->count();

            $reportData[$skill] = [
                'percentage' => $total > 0 ? round(($skilled / $total) * 100, 1) : 0,
                'details' => $details,
                'by_level' => $grouped,
            ];
        }

        // Generate QR code
        $qrCodeBase64 = RaporDownloadController::generateQrBase64($student);

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
