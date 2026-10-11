<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\AttendanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'attendance_date' => 'nullable|date|before_or_equal:today',
            'teacher_id' => 'nullable|exists:teachers,id',
            'search' => 'nullable|string|max:100',
        ]);

        $date = Carbon::parse($validated['attendance_date'] ?? now())->toDateString();
        $selectedTeacher = $validated['teacher_id'] ?? null;
        $search = trim($validated['search'] ?? '');

        $studentsQuery = Student::with('teacher.user')
            ->active()
            ->orderBy('name');

        if ($selectedTeacher) {
            $studentsQuery->where('teacher_id', $selectedTeacher);
        }

        if ($search !== '') {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $studentsQuery->get();
        $existing = Attendance::whereDate('attendance_date', $date)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $dailyStats = $this->attendanceService->summarize($existing->values());
        $dailyStats['unrecorded'] = $students->count() - $dailyStats['total'];
        $teachers = Teacher::with('user')->get()->sortBy('user.name')->values();

        return view('admin.absensi', compact(
            'students', 'existing', 'dailyStats', 'teachers', 'date', 'selectedTeacher', 'search'
        ));
    }

    public function store(StoreAttendanceRequest $request)
    {
        $validated = $request->validated();
        $studentIds = collect($validated['attendances'])
            ->pluck('student_id')
            ->map(fn ($id) => (int) $id)
            ->unique();
        $students = Student::query()
            ->active()
            ->whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        if ($students->count() !== $studentIds->count()) {
            return back()
                ->withErrors(['attendances' => 'Absensi hanya dapat dicatat untuk murid yang berstatus aktif.'])
                ->withInput();
        }

        $this->attendanceService->saveDaily(
            $students,
            $validated['attendances'],
            $validated['attendance_date'],
            $request->user()->id,
        );

        return redirect()->route('admin.absensi.index', array_filter([
            'attendance_date' => $validated['attendance_date'],
            'teacher_id' => $validated['teacher_id'] ?? null,
            'search' => $validated['search'] ?? null,
        ]))->with('success', 'Absensi harian berhasil disimpan.');
    }

    public function report(Request $request)
    {
        return view('admin.laporan-absensi', $this->reportPayload($request));
    }

    public function pdf(Request $request)
    {
        $payload = $this->reportPayload($request);
        $filename = 'laporan-absensi-'.$payload['periodKey'].'.pdf';

        return Pdf::loadView('attendance.pdf', $payload)
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    public function export(Request $request)
    {
        $payload = $this->reportPayload($request);
        $filename = 'laporan-absensi-'.$payload['periodKey'].'.csv';

        return response()->streamDownload(function () use ($payload) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['NIS', 'Nama Murid', 'Status Murid', 'Guru Pembimbing Saat Ini', 'Periode', 'Hadir', 'Sakit', 'Izin', 'Alpa', 'Hari Tercatat', 'Rasio Hadir dari Data Tercatat'], ';');

            foreach ($payload['summaryRows'] as $row) {
                fputcsv($file, [
                    $row['student']->nis,
                    $row['student']->name,
                    $row['student']->status_label,
                    $row['student']->teacher?->user?->name ?? '-',
                    $payload['periodLabel'],
                    $row['hadir'],
                    $row['sakit'],
                    $row['izin'],
                    $row['alpa'],
                    $row['total'],
                    $row['percentage'] === null ? 'Belum ada data' : $row['percentage'].'%',
                ], ';');
            }

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function reportPayload(Request $request): array
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:1900|max:'.now()->year,
            'month' => 'nullable|date_format:Y-m',
            'report_month' => 'nullable|integer|min:1|max:12',
            'teacher_id' => 'nullable|exists:teachers,id',
            'student_id' => 'nullable|exists:students,id',
            'student_status' => ['nullable', Rule::in([...array_keys(Student::STATUS_LABELS), 'semua'])],
        ]);

        // Keep older month links working; new filters use year and an optional month.
        $legacyMonth = isset($validated['month']) ? Carbon::parse($validated['month'].'-01') : null;
        $year = (int) ($validated['year'] ?? $legacyMonth?->year ?? now()->year);
        $selectedMonth = isset($validated['report_month'])
            ? (int) $validated['report_month']
            : ($request->has('year') ? null : $legacyMonth?->month);
        $periodStart = Carbon::create($year, $selectedMonth ?? 1, 1)->startOfDay();
        $periodEnd = $selectedMonth ? $periodStart->copy()->endOfMonth() : $periodStart->copy()->endOfYear();
        $periodKey = $selectedMonth ? $periodStart->format('Y-m') : (string) $year;
        $periodLabel = $selectedMonth ? $periodStart->copy()->locale('id')->translatedFormat('F Y') : 'Tahun '.$year;
        $selectedTeacher = $validated['teacher_id'] ?? null;
        $selectedStudent = $validated['student_id'] ?? null;
        $studentStatus = $validated['student_status'] ?? ($selectedStudent || $year < now()->year ? 'semua' : 'aktif');
        $studentStatusOptions = Student::STATUS_LABELS + ['semua' => 'Semua Status'];

        $studentsQuery = Student::with([
            'teacher.user',
            'attendances' => fn ($query) => $query
                ->where('attendance_date', '>=', $periodStart->toDateString())
                ->where('attendance_date', '<', $periodEnd->copy()->addDay()->toDateString())
                ->with('recorder')
                ->orderBy('attendance_date'),
        ])->whereDate('join_date', '<=', $periodEnd->toDateString())->orderBy('name');

        if ($studentStatus !== 'semua') {
            $studentsQuery->where('status', $studentStatus);
        }

        if ($selectedTeacher) {
            $studentsQuery->where('teacher_id', $selectedTeacher);
        }

        if ($selectedStudent) {
            $studentsQuery->whereKey($selectedStudent);
        }

        $students = $studentsQuery->get();
        $summaryRows = $students->map(function (Student $student) {
            return ['student' => $student] + $this->attendanceService->summarize($student->attendances);
        });

        $allAttendances = $students->flatMap->attendances;
        $overall = $this->attendanceService->summarize($allAttendances);
        $settings = Setting::query()->pluck('value', 'key');
        $dailySummary = $allAttendances
            ->groupBy(fn (Attendance $attendance) => $attendance->attendance_date->format('Y-m-d'))
            ->map(fn (Collection $items, string $date) => ['date' => $date] + $this->attendanceService->summarize($items))
            ->sortKeysDesc()
            ->values();
        $studentOptionsQuery = Student::query()->whereDate('join_date', '<=', $periodEnd->toDateString())->orderBy('name');
        if ($studentStatus !== 'semua') {
            $studentOptionsQuery->where('status', $studentStatus);
        }
        if ($selectedTeacher) {
            $studentOptionsQuery->where('teacher_id', $selectedTeacher);
        }
        $firstYear = min($year, Carbon::parse(Attendance::min('attendance_date') ?? now())->year, now()->year);

        return [
            'year' => $year,
            'years' => range(now()->year, $firstYear),
            'selectedMonth' => $selectedMonth,
            'periodKey' => $periodKey,
            'periodLabel' => $periodLabel,
            'selectedTeacher' => $selectedTeacher,
            'selectedStudent' => $selectedStudent,
            'studentStatus' => $studentStatus,
            'studentStatusLabel' => $studentStatusOptions[$studentStatus],
            'studentStatusOptions' => $studentStatusOptions,
            'teachers' => Teacher::with('user')->get()->sortBy('user.name')->values(),
            'studentOptions' => $studentOptionsQuery->get(),
            'summaryRows' => $summaryRows,
            'dailySummary' => $dailySummary,
            'overall' => $overall,
            'institutionName' => $settings->get('institution_name', 'BiMBA AIUEO Smart Education Centre'),
            'institutionAddress' => $settings->get('institution_address', 'Jl. Pendidikan No. 45, Jakarta Selatan, DKI Jakarta 12345'),
        ];
    }
}
