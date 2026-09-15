<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\StudentProgress;
use App\Services\ProgressReportService;
use App\Services\ReportPeriodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $children = Auth::user()->students()->with(['classroom', 'progress.material'])->get();
        $requestedChildId = (int) $request->input('student_id');
        $selectedChild = $children->firstWhere('id', $requestedChildId) ?? $children->first();

        $childrenData = $children->map(function ($child) {
            return [
                'student' => $child,
                'baca' => $child->skillPercentage('baca'),
                'tulis' => $child->skillPercentage('tulis'),
                'hitung' => $child->skillPercentage('hitung'),
                'total_stars' => $child->progress->where('display_status', 'T')->count() * 10,
            ];
        });
        $selectedChildData = $selectedChild
            ? $childrenData->first(fn ($item) => $item['student']->id === $selectedChild->id)
            : null;

        $today = Carbon::today();
        $raporSchedules = $children->filter(fn ($child) => ! empty($child->join_date))->map(function ($child) use ($today) {
            $period = app(ReportPeriodService::class)->nextDistribution($child);
            $periodNumber = $period['number'];
            $nextDate = $period['due_date'];

            return [
                'student_name' => $child->name,
                'next_date' => $nextDate->translatedFormat('d M Y'),
                'period_number' => $periodNumber,
                'days_left' => $today->diffInDays($nextDate, false),
            ];
        })->sortBy('days_left')->values();

        $recentProgress = $selectedChild
            ? app(ProgressReportService::class)->assessed($selectedChild->progress)->sortByDesc('updated_at')->take(3)->values()
            : collect();
        $skillCards = $selectedChild
            ? $this->buildSkillCards($selectedChild->progress)
            : $this->emptySkillCards();
        $skillTrend = $selectedChild
            ? $this->buildSkillTrend($selectedChild->progress)
            : $this->emptySkillTrend();
        $institutionName = Setting::get('institution_name', 'BiMBA AIUEO Smart Education Centre');

        return view('wali.dashboard', compact(
            'children',
            'childrenData',
            'selectedChild',
            'selectedChildData',
            'raporSchedules',
            'recentProgress',
            'skillCards',
            'skillTrend',
            'institutionName'
        ));
    }

    private function buildSkillCards(Collection $progress): array
    {
        $progress = app(ProgressReportService::class)->assessed($progress);
        $definitions = [
            'baca' => ['label' => 'Membaca (Baca)', 'empty' => 'Belum ada materi membaca yang dinilai.'],
            'tulis' => ['label' => 'Menulis (Tulis)', 'empty' => 'Belum ada materi menulis yang dinilai.'],
            'hitung' => ['label' => 'Berhitung (Hitung)', 'empty' => 'Belum ada materi berhitung yang dinilai.'],
        ];

        return collect($definitions)->mapWithKeys(function (array $definition, string $skill) use ($progress) {
            $items = $progress->filter(fn (StudentProgress $item) => $item->material?->skill_type === $skill);
            $total = $items->count();
            $terampil = $items->where('display_status', 'T')->count();
            $paham = $items->whereIn('display_status', ['P', 'T'])->count();
            $score = $total > 0
                ? (int) round($items->avg(fn (StudentProgress $item) => $this->statusScore($item->display_status)))
                : 0;

            if ($score >= 80) {
                $status = 'T';
                $description = "{$terampil} dari {$total} materi sudah terampil.";
            } elseif ($score >= 50) {
                $status = 'P';
                $description = "{$paham} dari {$total} materi sudah paham.";
            } else {
                $status = $total > 0 ? 'K' : null;
                $description = $total > 0
                    ? "{$paham} dari {$total} materi sudah paham, perlu pendampingan lanjutan."
                    : $definition['empty'];
            }

            return [$skill => [
                'label' => $definition['label'],
                'status' => $status,
                'percentage' => $score,
                'description' => $description,
            ]];
        })->all();
    }

    private function buildSkillTrend(Collection $progress): array
    {
        $progress = app(ProgressReportService::class)->assessed($progress);
        $months = collect(range(4, 0))->map(fn (int $offset) => Carbon::today()->startOfMonth()->subMonths($offset));
        $skills = [
            'baca' => 'Membaca',
            'tulis' => 'Menulis',
            'hitung' => 'Berhitung',
        ];

        return [
            'labels' => $months->map(fn (Carbon $month) => $month->translatedFormat('M'))->values()->all(),
            'series' => collect($skills)->mapWithKeys(function (string $label, string $skill) use ($progress, $months) {
                $items = $progress->filter(fn (StudentProgress $item) => $item->material?->skill_type === $skill);
                $total = $items->count();

                $data = $months->map(function (Carbon $month) use ($items, $total) {
                    if ($total === 0) {
                        return 0;
                    }

                    $endOfMonth = $month->copy()->endOfMonth();
                    $score = $items->map(fn (StudentProgress $item) => $this->progressScoreAt($item, $endOfMonth))->filter(fn ($score) => $score > 0)->avg() ?? 0;

                    return (int) round($score);
                })->values()->all();

                return [$skill => [
                    'label' => $label,
                    'data' => $data,
                ]];
            })->all(),
        ];
    }

    private function progressScoreAt(StudentProgress $progress, Carbon $date): int
    {
        if ($progress->skilled_date && $progress->skilled_date->lte($date)) {
            return 100;
        }

        if ($progress->understand_date && $progress->understand_date->lte($date)) {
            return 66;
        }

        if ($progress->start_date && $progress->start_date->lte($date)) {
            return 33;
        }

        return 0;
    }

    private function statusScore(string $status): int
    {
        return match ($status) {
            'T' => 100,
            'P' => 66,
            'K', 'B' => 33,
            default => 0,
        };
    }

    private function emptySkillCards(): array
    {
        return [
            'baca' => ['label' => 'Membaca (Baca)', 'status' => null, 'percentage' => 0, 'description' => 'Belum ada materi membaca yang dinilai.'],
            'tulis' => ['label' => 'Menulis (Tulis)', 'status' => null, 'percentage' => 0, 'description' => 'Belum ada materi menulis yang dinilai.'],
            'hitung' => ['label' => 'Berhitung (Hitung)', 'status' => null, 'percentage' => 0, 'description' => 'Belum ada materi berhitung yang dinilai.'],
        ];
    }

    private function emptySkillTrend(): array
    {
        $months = collect(range(4, 0))->map(fn (int $offset) => Carbon::today()->startOfMonth()->subMonths($offset)->translatedFormat('M'))->values()->all();

        return [
            'labels' => $months,
            'series' => [
                'baca' => ['label' => 'Membaca', 'data' => [0, 0, 0, 0, 0]],
                'tulis' => ['label' => 'Menulis', 'data' => [0, 0, 0, 0, 0]],
                'hitung' => ['label' => 'Berhitung', 'data' => [0, 0, 0, 0, 0]],
            ],
        ];
    }
}
