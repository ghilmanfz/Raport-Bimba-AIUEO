<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $teacher = $request->user()->teacher;
        if (! $teacher) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan.');
        }

        $date = Carbon::parse($validated['attendance_date'] ?? now())->toDateString();
        $selectedClassroom = $validated['classroom_id'] ?? null;
        $search = trim($validated['search'] ?? '');

        $studentsQuery = Student::where('teacher_id', $teacher->id)
            ->active()
            ->with('classroom')
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
        $classrooms = $teacher->students()
            ->active()
            ->whereNotNull('classroom_id')
            ->with('classroom')
            ->get()
            ->pluck('classroom')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $dailyStats = $this->attendanceService->summarize($existing->values());
        $dailyStats['unrecorded'] = $students->count() - $dailyStats['total'];

        return view('guru.absensi', compact(
            'students', 'existing', 'classrooms', 'dailyStats', 'date', 'selectedClassroom', 'search'
        ));
    }

    public function store(StoreAttendanceRequest $request)
    {
        $validated = $request->validated();

        $teacher = $request->user()->teacher;
        if (! $teacher) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan.');
        }

        $studentIds = collect($validated['attendances'])->pluck('student_id')->map(fn ($id) => (int) $id);
        $students = Student::where('teacher_id', $teacher->id)
            ->active()
            ->whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        if ($students->count() !== $studentIds->unique()->count()) {
            return back()->withErrors(['attendances' => 'Terdapat murid yang bukan bagian dari bimbingan Anda.'])->withInput();
        }

        $this->attendanceService->saveDaily(
            $students,
            $validated['attendances'],
            $validated['attendance_date'],
            $request->user()->id,
        );

        return redirect()->route('guru.absensi.index', array_filter([
            'attendance_date' => $validated['attendance_date'],
            'classroom_id' => $validated['classroom_id'] ?? null,
            'search' => $validated['search'] ?? null,
        ]))->with('success', 'Absensi murid berhasil disimpan.');
    }
}
