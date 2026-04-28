<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $children = Auth::user()->students()->with(['classroom', 'progress.material'])->get();

        $childrenData = $children->map(function ($child) {
            return [
                'student'    => $child,
                'baca'       => $child->skillPercentage('baca'),
                'tulis'      => $child->skillPercentage('tulis'),
                'hitung'     => $child->skillPercentage('hitung'),
                'total_stars' => $child->progress->where('status', 'T')->count() * 10,
            ];
        });

        $today = Carbon::today();
        $raporSchedules = $children->filter(fn ($child) => !empty($child->join_date))->map(function ($child) use ($today) {
            $joinDate = Carbon::parse($child->join_date)->startOfDay();
            $monthsDiff = max(0, $joinDate->diffInMonths($today, false));
            $periodNumber = (int) floor($monthsDiff / 3) + 1;
            $nextDate = $joinDate->copy()->addMonths($periodNumber * 3);

            while ($nextDate->lt($today)) {
                $periodNumber++;
                $nextDate = $joinDate->copy()->addMonths($periodNumber * 3);
            }

            return [
                'student_name' => $child->name,
                'next_date' => $nextDate->translatedFormat('d M Y'),
                'period_number' => $periodNumber,
                'days_left' => $today->diffInDays($nextDate, false),
            ];
        })->sortBy('days_left')->values();

        if ($raporSchedules->isEmpty()) {
            $raporSchedules = collect([
                ['student_name' => 'Anak 1', 'next_date' => '12 Jul 2026', 'period_number' => 2, 'days_left' => 75],
                ['student_name' => 'Anak 2', 'next_date' => '05 Aug 2026', 'period_number' => 2, 'days_left' => 99],
            ]);
        }

        return view('wali.dashboard', compact('childrenData', 'raporSchedules'));
    }
}
