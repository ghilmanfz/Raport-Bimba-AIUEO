<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WaliController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'wali')
            ->with(['students' => function ($q) {
                $q->orderBy('name');
            }])
            ->withCount('students');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('students', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'aktif') {
                $query->whereHas('students', fn ($q) => $q->where('status', 'aktif'));
            }

            if ($status === 'lulus') {
                $query->whereDoesntHave('students', fn ($q) => $q->where('status', 'aktif'))
                    ->whereHas('students')
                    ->whereDoesntHave('students', fn ($q) => $q->where('status', 'pindah'));
            }

            if ($status === 'pindah') {
                $query->whereDoesntHave('students', fn ($q) => $q->where('status', 'aktif'))
                    ->whereHas('students')
                    ->whereDoesntHave('students', fn ($q) => $q->where('status', 'lulus'));
            }
        }

        $wali = $query->latest()->paginate(10);

        $totalWali = User::where('role', 'wali')->count();

        return view('admin.wali', compact('wali', 'totalWali'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'wali') {
            return redirect()->route('admin.wali')->with('error', 'Data ini bukan wali murid.');
        }

        $request->validate([
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'mother_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8',
        ]);

        $combinedName = trim($request->father_name . ' & ' . $request->mother_name);
        $updateData = [
            'name' => $combinedName,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'father_phone' => $request->father_phone,
            'mother_phone' => $request->mother_phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['plain_password'] = $request->password;
        }

        $user->update($updateData);

        return redirect()->route('admin.wali')->with('success', 'Data wali murid berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'wali') {
            return redirect()->route('admin.wali')->with('error', 'Hanya wali murid yang bisa dihapus.');
        }

        if ($user->students()->exists()) {
            return redirect()->route('admin.wali')->with('error', 'Wali murid masih terhubung dengan data siswa. Lepaskan relasi siswa terlebih dahulu.');
        }

        $name = $user->name;
        $user->delete();

        Notification::notifyAdmins(
            'Data Wali Murid Dihapus',
            'Data wali murid ' . $name . ' telah dihapus dari sistem.',
            'warning',
            'lucide:trash-2',
            route('admin.wali')
        );

        return redirect()->route('admin.wali')->with('success', 'Data wali murid berhasil dihapus.');
    }

    public function show(User $user)
    {
        if ($user->role !== 'wali') {
            return redirect()->route('admin.wali')->with('error', 'Data ini bukan wali murid.');
        }
        
        $user->load(['students']);
        
        return view('admin.wali-detail', compact('user'));
    }

    public function export()
    {
        $wali = User::where('role', 'wali')
            ->with(['students' => function ($q) {
                $q->orderBy('name');
            }])
            ->get();

        $callback = function () use ($wali) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Nama Ayah', 'Nama Ibu', 'Kontak Ayah', 'Kontak Ibu', 'Alamat', 'Jumlah Anak', 'Nama Anak', 'Status Wali'], ';');

            foreach ($wali as $w) {
                if ($w->students->isEmpty()) {
                    fputcsv($file, [
                        $w->father_name,
                        $w->mother_name,
                        $w->father_phone,
                        $w->mother_phone,
                        $w->address,
                        0,
                        '-',
                        'Tidak Ada Anak',
                    ], ';');
                } else {
                    $statuses = $w->students->pluck('status')->unique()->toArray();
                    $waliStatus = in_array('aktif', $statuses) ? 'Aktif' : ((count($statuses) === 1 && $statuses[0] === 'lulus') ? 'Lulus' : 'Pindah');

                    foreach ($w->students as $index => $student) {
                        fputcsv($file, [
                            $index === 0 ? $w->father_name : '',
                            $index === 0 ? $w->mother_name : '',
                            $index === 0 ? $w->father_phone : '',
                            $index === 0 ? $w->mother_phone : '',
                            $index === 0 ? $w->address : '',
                            $index === 0 ? $w->students->count() : '',
                            $student->name . ' (' . $student->nis . ')',
                            $index === 0 ? $waliStatus : '',
                        ], ';');
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data-wali-murid-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
