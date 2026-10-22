<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ProgressReportService;
use App\Services\ReportPeriodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $children = Auth::user()->students()->with(['classroom', 'progress.material'])->get();
        $requestedChildId = (int) $request->input('student_id');
        $selectedChild = $children->firstWhere('id', $requestedChildId) ?? $children->first();

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
        $reportData = $selectedChild
            ? app(ProgressReportService::class)->reportData($selectedChild, $today)
            : null;
        $institutionName = Setting::get('institution_name', 'BiMBA AIUEO Smart Education Centre');

        return view('wali.dashboard', compact(
            'children',
            'selectedChild',
            'raporSchedules',
            'recentProgress',
            'reportData',
            'institutionName'
        ));
    }
}
