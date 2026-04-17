<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
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

        return view('wali.dashboard', compact('childrenData'));
    }
}
