<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class MuridController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('guru.dashboard')->with('error', 'Guru tidak ditemukan.');
        }

        $query = $teacher->students()->with(['classroom', 'parent']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($statusFilter = $request->input('status')) {
            $query->where('status', $statusFilter);
        }

        $students     = $query->latest()->paginate(10);
        $totalMurid   = $teacher->students()->count();
        $muridAktif   = $teacher->students()->where('status', 'aktif')->count();
        $muridLulus   = $teacher->students()->where('status', 'lulus')->count();
        $muridKeluar  = $teacher->students()->where('status', 'keluar')->count();

        return view('guru.murid', compact('students', 'totalMurid', 'muridAktif', 'muridLulus', 'muridKeluar'));
    }
}
