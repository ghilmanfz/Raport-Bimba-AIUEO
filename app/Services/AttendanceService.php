<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
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
