<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RaporDownloadController;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\AttendanceService;
use App\Services\ProgressReportService;
use App\Services\ReportPeriodService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class RaporController extends Controller
{
    public function index(Request $request, ReportPeriodService $periods, ProgressReportService $reports)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:1900|max:'.(now()->year + 1),
            'teacher_id' => 'nullable|exists:teachers,id',
            'student_status' => ['nullable', Rule::in([...array_keys(Student::STATUS_LABELS), 'semua'])],
            'search' => 'nullable|string|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $year = (int) ($validated['year'] ?? now()->year);
        $selectedTeacher = $validated['teacher_id'] ?? null;
        $studentStatus = $validated['student_status'] ?? 'semua';
        $search = trim($validated['search'] ?? '');
        $yearStart = CarbonImmutable::create($year, 1, 1)->startOfDay();
        $yearEnd = $yearStart->endOfYear();
        $query = Student::with([
            'teacher.user', 'progress.material',
            'attendances' => fn ($query) => $query
                ->where('attendance_date', '>=', $yearStart->subMonths(3)->toDateString())
                ->where('attendance_date', '<', $yearEnd->addDay()->toDateString()),
        ])->whereDate('join_date', '<=', $yearEnd->toDateString());

        if ($selectedTeacher) {
            $query->where('teacher_id', $selectedTeacher);
        }
        if ($studentStatus !== 'semua') {
            $query->where('status', $studentStatus);
        }
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $rows = collect();
        foreach ($query->orderBy('name')->get() as $student) {
            foreach ($periods->forYear($student, $year) as $period) {
                $data = $reports->reportData($student, $period['cutoff']);
                $assessmentCount = collect($data)->sum(fn ($skill) => $skill['details']->count());
                $attendanceCount = $student->attendances->filter(fn ($attendance) => $attendance->attendance_date->gte($period['start'])
                    && $attendance->attendance_date->toDateString() <= $period['cutoff']->toDateString()
                )->count();

                if ($assessmentCount === 0 && $attendanceCount === 0) {
                    continue;
                }

                $rows->push([
                    'student' => $student,
                    'period' => $period,
                    'assessmentCount' => $assessmentCount,
                    'attendanceCount' => $attendanceCount,
                ]);
            }
        }

        $rows = $rows->sortByDesc(fn ($row) => $row['period']['end']->toDateString())->values();
        $page = (int) ($validated['page'] ?? 1);
        $raporRows = new LengthAwarePaginator($rows->forPage($page, 15)->values(), $rows->count(), 15, $page, [
            'path' => $request->url(), 'query' => $request->except('page'),
        ]);
        $firstYear = min($year, CarbonImmutable::parse(Student::min('join_date') ?? today())->year, now()->year);
        $years = range(now()->year + 1, $firstYear);
        $teachers = Teacher::with('user')->get()->sortBy('user.name')->values();
        $studentStatusOptions = ['semua' => 'Semua Status'] + Student::STATUS_LABELS;

        return view('admin.rapor', compact('year', 'years', 'selectedTeacher', 'teachers', 'studentStatus', 'studentStatusOptions', 'search', 'raporRows'));
    }

    public function show(Request $request, Student $student, ReportPeriodService $periods)
    {
        $period = $this->selectedPeriod($request, $student, $periods);
        $student->load(['classroom', 'parent', 'teacher.user', 'progress.material']);
        $reportYear = $period['end']->year;

        return view('guru.rapor', [
            'reportLayout' => 'layouts.admin',
            'isAdminReport' => true,
            'reportAction' => route('admin.rapor.show', $student),
            'reportYear' => $reportYear,
            'student' => $student,
            'reportData' => app(ProgressReportService::class)->reportData($student, $period['cutoff']),
            'attendanceReport' => app(AttendanceService::class)->forReport($student, $period),
            'periodOptions' => $periods->forYear($student, $reportYear),
            'qrCodeBase64' => RaporDownloadController::generateQrBase64($student, $period),
            'institutionName' => Setting::get('institution_name', 'BiMBA AIUEO'),
            'institutionAddress' => Setting::get('institution_address', ''),
            'unitName' => Setting::get('unit_name', ''),
        ]);
    }

    public function pdf(Request $request, Student $student, ReportPeriodService $periods)
    {
        $this->selectedPeriod($request, $student, $periods);

        return app(RaporDownloadController::class)->download($request, $student->report_token);
    }

    private function selectedPeriod(Request $request, Student $student, ReportPeriodService $periods): array
    {
        $validated = $request->validate(['year' => 'nullable|integer|min:1900|max:'.(now()->year + 1)]);
        $period = $periods->select($student, $request->only('period_number', 'period_end'));
        abort_if(isset($validated['year']) && (int) $validated['year'] !== $period['end']->year, 422);

        return $period;
    }
}
