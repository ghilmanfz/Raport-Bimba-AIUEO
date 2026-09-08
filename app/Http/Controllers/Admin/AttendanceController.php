<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Setting;
use App\Models\Student;
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
            'classroom_id' => 'nullable|exists:classrooms,id',
            'search' => 'nullable|string|max:100',
        ]);

        $date = Carbon::parse($validated['attendance_date'] ?? now())->toDateString();
        $selectedClassroom = $validated['classroom_id'] ?? null;
        $search = trim($validated['search'] ?? '');

        $studentsQuery = Student::with(['classroom', 'teacher.user'])
            ->active()
            ->orderBy('name');

        if ($selectedClassroom) {
            $studentsQuery->where('classroom_id', $selectedClassroom);
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
        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.absensi', compact(
            'students', 'existing', 'dailyStats', 'classrooms', 'date', 'selectedClassroom', 'search'
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
            'classroom_id' => $validated['classroom_id'] ?? null,
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
        $filename = 'laporan-absensi-'.$payload['month'].'.pdf';

        return Pdf::loadView('attendance.pdf', $payload)
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    public function export(Request $request)
    {
        $payload = $this->reportPayload($request);
        $filename = 'laporan-absensi-'.$payload['month'].'.csv';

        return response()->streamDownload(function () use ($payload) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['NIS', 'Nama Murid', 'Status Murid', 'Kelas Saat Ini', 'Hadir', 'Sakit', 'Izin', 'Alpa', 'Hari Tercatat', 'Rasio Hadir dari Data Tercatat'], ';');

            foreach ($payload['summaryRows'] as $row) {
                fputcsv($file, [
                    $row['student']->nis,
                    $row['student']->name,
                    $row['student']->status_label,
                    $row['student']->classroom?->name ?? '-',
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
            'month' => 'nullable|date_format:Y-m',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'student_id' => 'nullable|exists:students,id',
            'student_status' => ['nullable', Rule::in([...array_keys(Student::STATUS_LABELS), 'semua'])],
        ]);

        $month = $validated['month'] ?? now()->format('Y-m');
        $selectedClassroom = $validated['classroom_id'] ?? null;
        $selectedStudent = $validated['student_id'] ?? null;
        $studentStatus = $validated['student_status'] ?? ($selectedStudent ? 'semua' : 'aktif');
        $studentStatusOptions = Student::STATUS_LABELS + ['semua' => 'Semua Status'];
        $periodStart = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();

        $studentsQuery = Student::with([
            'classroom',
            'attendances' => fn ($query) => $query
                ->inMonth($month)
                ->with('recorder')
                ->orderBy('attendance_date'),
        ])->orderBy('name');

        if ($studentStatus !== 'semua') {
            $studentsQuery->where('status', $studentStatus);
        }

        if ($selectedClassroom) {
            $studentsQuery->where('classroom_id', $selectedClassroom);
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
        $studentOptionsQuery = Student::with('classroom')->orderBy('name');
        if ($studentStatus !== 'semua') {
            $studentOptionsQuery->where('status', $studentStatus);
        }

        return [
            'month' => $month,
            'monthLabel' => $periodStart->copy()->locale('id')->translatedFormat('F Y'),
            'selectedClassroom' => $selectedClassroom,
            'selectedStudent' => $selectedStudent,
            'studentStatus' => $studentStatus,
            'studentStatusLabel' => $studentStatusOptions[$studentStatus],
            'studentStatusOptions' => $studentStatusOptions,
            'classrooms' => Classroom::orderBy('name')->get(),
            'studentOptions' => $studentOptionsQuery->get(),
            'summaryRows' => $summaryRows,
            'dailySummary' => $dailySummary,
            'overall' => $overall,
            'institutionName' => $settings->get('institution_name', 'BiMBA AIUEO Smart Education Centre'),
            'institutionAddress' => $settings->get('institution_address', 'Jl. Pendidikan No. 45, Jakarta Selatan, DKI Jakarta 12345'),
        ];
    }
}
