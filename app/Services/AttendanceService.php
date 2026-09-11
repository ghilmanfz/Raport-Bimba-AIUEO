<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function forReport(Student $student, ?array $period = null): array
    {
        $period ??= app(ReportPeriodService::class)->current($student);
        $attendances = $student->attendances()
            ->whereDate('attendance_date', '>=', $period['start']->toDateString())
            ->whereDate('attendance_date', '<=', $period['cutoff']->toDateString())
            ->get();
        $byMonth = $attendances->groupBy(fn ($attendance) => $attendance->attendance_date->format('Y-m'));
        $months = [];

        // Calendar-month rows are clipped to the student's three-month reporting cycle.
        for ($month = $period['start']->startOfMonth(); $month->lte($period['end']); $month = $month->addMonth()) {
            $start = $month->max($period['start']);
            $end = $month->endOfMonth()->startOfDay()->min($period['end']);
            $months[] = [
                'label' => $month->locale('id')->translatedFormat('F Y'),
                'start' => $start,
                'end' => $end,
                'future' => $start->gt($period['cutoff']),
                'summary' => $this->summarize($byMonth->get($month->format('Y-m'), collect())),
            ];
        }

        return [
            'period' => $period,
            'months' => $months,
            'summary' => $this->summarize($attendances),
        ];
    }

    public function saveDaily(Collection $students, array $entries, string $date, int $recorderId): void
    {
        DB::transaction(function () use ($students, $entries, $date, $recorderId) {
            foreach ($entries as $entry) {
                $student = $students->get((int) $entry['student_id']);

                if (empty($entry['status'])) {
                    Attendance::query()
                        ->where('student_id', $student->id)
                        ->whereDate('attendance_date', $date)
                        ->delete();

                    continue;
                }

                $attendance = Attendance::query()
                    ->where('student_id', $student->id)
                    ->whereDate('attendance_date', $date)
                    ->first() ?? new Attendance([
                        'student_id' => $student->id,
                        'attendance_date' => $date,
                    ]);

                if (! $attendance->exists || ! $attendance->classroom_id) {
                    $attendance->classroom_id = $student->classroom_id;
                }

                $attendance->fill([
                    'recorded_by' => $recorderId,
                    'status' => $entry['status'],
                    'notes' => $entry['notes'] ?? null,
                ])->save();
            }
        });
    }

    public function summarize(Collection $attendances): array
    {
        $total = $attendances->count();
        $hadir = $attendances->where('status', 'hadir')->count();

        return [
            'hadir' => $hadir,
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'alpa' => $attendances->where('status', 'alpa')->count(),
            'total' => $total,
            'percentage' => $total > 0 ? round(($hadir / $total) * 100, 1) : null,
        ];
    }
}
