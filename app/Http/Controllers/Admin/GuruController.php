<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'students.classroom']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($statusFilter = $request->input('status')) {
            $query->where('status', $statusFilter);
        }

        $teachers     = $query->latest()->paginate(10);
        $totalGuru    = Teacher::count();
        $guruAktif    = Teacher::where('status', 'aktif')->count();
        $guruNonaktif = Teacher::where('status', 'nonaktif')->count();
        $guruCuti     = Teacher::where('status', 'cuti')->count();

        return view('admin.guru', compact('teachers', 'totalGuru', 'guruAktif', 'guruNonaktif', 'guruCuti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'nullable|string|min:6',
            'specialization' => 'nullable|string|max:100',
            'status'         => 'required|in:aktif,cuti,nonaktif',
        ]);

        $plainPw = $request->filled('password') ? $request->input('password') : 'password123';

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'role'           => 'guru',
            'password'       => Hash::make($plainPw),
            'plain_password' => $plainPw,
        ]);

        $teacher = Teacher::create([
            'user_id'        => $user->id,
            'nip'            => Teacher::generateNip(),
            'specialization' => $request->specialization,
            'status'         => $request->status,
        ]);

        // Guru tidak lagi dipilihkan kelas dari menu Guru.
        // Murid yang dibimbing tetap ditentukan dari menu Admin -> Murid melalui kolom students.teacher_id.

        Notification::notifyAdmins(
            'Guru Baru Ditambahkan',
            'Guru ' . $request->name . ' ('. $teacher->nip .') berhasil ditambahkan ke sistem.',
            'success',
            'lucide:user-plus',
            route('admin.guru')
        );

        return redirect()->route('admin.guru')->with('success', 'Guru berhasil ditambahkan dengan NIP: ' . $teacher->nip);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $teacher->user_id,
            'specialization' => 'nullable|string|max:100',
            'status'         => 'required|in:aktif,cuti,nonaktif',
        ]);

        $teacher->user->update($request->only('name', 'email'));
        $teacher->update($request->only('specialization', 'status'));

        // Tidak ada sync kelas. Guru tidak terikat langsung ke kelas dari menu Guru.

        Notification::notifyAdmins(
            'Data Guru Diperbarui',
            'Data guru ' . $request->name . ' telah diperbarui.',
            'info',
            'lucide:pencil',
            route('admin.guru')
        );

        return redirect()->route('admin.guru')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        $name = $teacher->user->name;
        $teacher->user->delete(); // cascades to teacher

        Notification::notifyAdmins(
            'Data Guru Dihapus',
            'Data guru ' . $name . ' telah dihapus dari sistem.',
            'warning',
            'lucide:trash-2',
            route('admin.guru')
        );

        return redirect()->route('admin.guru')->with('success', 'Data guru berhasil dihapus.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'students.classroom']);

        return view('admin.guru-detail', compact('teacher'));
    }

    public function export()
    {
        $teachers = Teacher::with(['user', 'students'])->get();

        $callback = function () use ($teachers) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($file, "ï»¿");
            fputcsv($file, ['NIP', 'Nama', 'Email', 'Password', 'Status', 'Murid Dibimbing'], ';');

            foreach ($teachers as $teacher) {
                fputcsv($file, [
                    $teacher->nip,
                    $teacher->user->name,
                    $teacher->user->email,
                    $teacher->user->plain_password ?? '-',
                    ucfirst($teacher->status),
                    $teacher->students->count(),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data-guru-' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file   = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // skip header row
        $count  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;

            $name           = trim($row[0] ?? '');
            $email          = trim($row[1] ?? '');
            $nip            = trim($row[2] ?? '');
            $specialization = trim($row[3] ?? '');
            $status         = trim($row[4] ?? 'aktif');

            if (empty($name) || empty($email)) continue;
            if (User::where('email', $email)->exists()) continue;

            $user = User::create([
                'name'           => $name,
                'email'          => $email,
                'role'           => 'guru',
                'password'       => Hash::make('password123'),
                'plain_password' => 'password123',
            ]);

            Teacher::create([
                'user_id'        => $user->id,
                'nip'            => $nip ?: null,
                'specialization' => $specialization ?: null,
                'status'         => in_array($status, ['aktif', 'cuti', 'nonaktif']) ? $status : 'aktif',
            ]);

            $count++;
        }

        fclose($handle);

        Notification::notifyAdmins(
            'Import Data Guru',
            "Berhasil mengimpor {$count} data guru dari file CSV.",
            'success',
            'lucide:upload',
            route('admin.guru')
        );

        return redirect()->route('admin.guru')->with('success', "Berhasil mengimpor {$count} data guru.");
    }
}
