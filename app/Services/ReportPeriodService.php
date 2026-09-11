<?php

namespace App\Services;

use App\Models\Student;
use Carbon\CarbonImmutable;

class ReportPeriodService
{
    public function current(Student $student): array
    {
        $anchor = $this->anchor($student);
        $today = CarbonImmutable::today();
        $months = max(0, ($today->year - $anchor->year) * 12 + $today->month - $anchor->month);
        $number = intdiv($months, 3) + 1;

        if ($number > 1 && $anchor->addMonthsNoOverflow(($number - 1) * 3)->gt($today)) {
            $number--;
        }

        return $this->forNumber($student, $number);
    }

    public function forNumber(Student $student, int $number): array
    {
        if ($number < 1) {
            throw new \InvalidArgumentException('Nomor periode harus positif.');
        }

        $anchor = $this->anchor($student);
        $start = $anchor->addMonthsNoOverflow(($number - 1) * 3);
        $end = $anchor->addMonthsNoOverflow($number * 3)->subDay();

        return [
            'number' => $number,
            'start' => $start,
            'end' => $end,
            'cutoff' => $end->min(CarbonImmutable::today())->locale('id'),
        ];
    }

    private function anchor(Student $student): CarbonImmutable
    {
        return CarbonImmutable::parse($student->join_date ?? $student->created_at ?? today())->startOfDay()->locale('id');
    }
}
