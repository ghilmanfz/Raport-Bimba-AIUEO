<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentProgress;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class ProgressReportService
{
    public function assessed(Collection $progress): Collection
    {
        return $progress->filter(fn (StudentProgress $item) => $item->display_status !== '');
    }

    public function summarize(Collection $progress): array
    {
        $assessed = $this->assessed($progress);
        $counts = [];
        $percentages = [];
        foreach (['T', 'P', 'K'] as $status) {
            $counts[$status] = $assessed->where('display_status', $status)->count();
            $percentages[$status] = $assessed->isNotEmpty() ? round($counts[$status] / $assessed->count() * 100) : 0;
        }

        return ['counts' => $counts, 'percentages' => $percentages, 'total' => $assessed->count()];
    }

    public function reportData(Student $student, ?CarbonInterface $cutoff = null): array
    {
        $student->loadMissing('progress.material');
        $progress = $student->progress->map(function (StudentProgress $item) use ($cutoff) {
            $snapshot = clone $item;
            if ($cutoff) {
                foreach (['start_date', 'understand_date', 'skilled_date'] as $field) {
                    if ($snapshot->$field && $snapshot->$field->toDateString() > $cutoff->toDateString()) {
                        $snapshot->$field = null;
                    }
                }
            }

            return $snapshot;
        });
        $report = [];
        foreach (['baca', 'tulis', 'hitung'] as $skill) {
            $details = $this->assessed($progress)->filter(fn ($item) => $item->material?->skill_type === $skill)->values();
            $skilled = $details->where('display_status', 'T')->count();
            $report[$skill] = [
                'percentage' => $details->isNotEmpty() ? round($skilled / $details->count() * 100, 1) : 0,
                'details' => $details,
                'by_level' => $details->groupBy(fn ($item) => $item->material->level ?? 'Level 1'),
            ];
        }

        return $report;
    }
}
