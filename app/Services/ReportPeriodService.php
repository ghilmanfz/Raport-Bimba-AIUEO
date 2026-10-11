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
        $months = [];
        for ($index = 0; $index < 3; $index++) {
            $offset = ($number - 1) * 3 + $index;
            $months[] = [
                'label' => 'Bulan ke-'.($index + 1),
                'start' => $anchor->addMonthsNoOverflow($offset),
                'end' => $anchor->addMonthsNoOverflow($offset + 1)->subDay(),
            ];
        }

        return [
            'number' => $number,
            'start' => $start,
            'end' => $end,
            'due_date' => $end->addDay(),
            'months' => $months,
            'cutoff' => $end->min(CarbonImmutable::today())->locale('id'),
        ];
    }

    public function nextDistribution(Student $student): array
    {
        $period = $this->current($student);
        // On distribution day, keep today's completed period visible until tomorrow.
        if ($period['number'] > 1 && $period['start']->isSameDay(CarbonImmutable::today())) {
            return $this->forNumber($student, $period['number'] - 1);
        }

        return $period;
    }

    public function select(Student $student, array $input): array
    {
        $validated = validator($input, [
            'period_number' => 'nullable|integer|min:1',
            'period_end' => 'nullable|date_format:Y-m-d|before_or_equal:today',
        ])->validate();
        $current = $this->current($student);
        $number = (int) ($validated['period_number'] ?? $current['number']);
        abort_if($number > $current['number'], 404);
        $period = $this->forNumber($student, $number);

        if (! empty($validated['period_end'])) {
            $cutoff = CarbonImmutable::parse($validated['period_end'])->startOfDay()->locale('id');
            abort_if($cutoff->lt($period['start']) || $cutoff->gt($period['cutoff']), 422);
            $period['cutoff'] = $cutoff;
        }

        return $period;
    }

    public function options(Student $student): array
    {
        return array_map(fn ($number) => $this->forNumber($student, $number), range(1, $this->current($student)['number']));
    }

    public function forYear(Student $student, int $year): array
    {
        $anchor = $this->anchor($student);
        $months = ($year - $anchor->year) * 12 + 1 - $anchor->month;
        $firstNumber = max(1, intdiv(max(0, $months), 3));
        $lastNumber = $this->current($student)['number'];
        $periods = [];

        for ($number = $firstNumber; $number <= $lastNumber; $number++) {
            $period = $this->forNumber($student, $number);
            if ($period['end']->year > $year) {
                break;
            }
            if ($period['end']->year === $year) {
                $periods[] = $period;
            }
        }

        return $periods;
    }

    private function anchor(Student $student): CarbonImmutable
    {
        return CarbonImmutable::parse($student->join_date ?? $student->created_at ?? today())->startOfDay()->locale('id');
    }
}
