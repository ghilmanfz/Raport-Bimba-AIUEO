<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|integer',
            'month' => 'nullable|date_format:Y-m',
        ]);

        $children = $request->user()->students()->with('classroom')->orderBy('name')->get();
        $selectedChildId = (int) ($validated['student_id'] ?? $children->first()?->id);
        $student = $children->firstWhere('id', $selectedChildId) ?? $children->first();
        $month = $validated['month'] ?? now()->format('Y-m');
        $periodStart = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();

        $attendances = $student
            ? $student->attendances()
                ->inMonth($month)
                ->with(['classroom', 'recorder'])
                ->orderByDesc('attendance_date')
                ->get()
            : collect();

        $summary = $this->attendanceService->summarize($attendances);

        return view('wali.absensi', [
            'children' => $children,
            'student' => $student,
            'attendances' => $attendances,
            'summary' => $summary,
            'month' => $month,
            'monthLabel' => $periodStart->locale('id')->translatedFormat('F Y'),
        ]);
    }
}
