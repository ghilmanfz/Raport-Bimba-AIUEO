<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'classrooms']);

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('nip', 'like', "%{$search}%");
        }

        if ($spec = $request->input('specialization')) {
            $query->where('specialization', $spec);
        }

        $teachers     = $query->latest()->paginate(10);
        $classrooms   = Classroom::orderBy('name')->get();
        $totalGuru    = Teacher::count();
        $guruAktif    = Teacher::where('status', 'aktif')->count();
        $specBaca     = Teacher::where('specialization', 'like', '%Baca%')->count();
        $avgBeban     = round(Teacher::withCount('classrooms')->get()->avg('classrooms_count'), 1);

        return view('admin.guru', compact('teachers', 'classrooms', 'totalGuru', 'guruAktif', 'specBaca', 'avgBeban'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'nip'            => 'nullable|string|unique:teachers,nip',
            'specialization' => 'nullable|string|max:100',
            'status'         => 'required|in:aktif,cuti,nonaktif',
            'classroom_ids'  => 'nullable|array',
            'classroom_ids.*' => 'exists:classrooms,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => 'guru',
            'password' => Hash::make('password123'),
        ]);

        $teacher = Teacher::create([
            'user_id'        => $user->id,
            'nip'            => $request->nip,
            'specialization' => $request->specialization,
            'status'         => $request->status,
        ]);

        if ($request->filled('classroom_ids')) {
            $teacher->classrooms()->sync($request->classroom_ids);
        }

        Notification::notifyAdmins(
            'Guru Baru Ditambahkan',
            'Guru ' . $request->name . ' berhasil ditambahkan ke sistem.',
            'success',
            'lucide:user-plus',
            route('admin.guru')
        );

        return redirect()->route('admin.guru')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $teacher->user_id,
            'nip'            => 'nullable|string|unique:teachers,nip,' . $teacher->id,
            'specialization' => 'nullable|string|max:100',
            'status'         => 'required|in:aktif,cuti,nonaktif',
            'classroom_ids'  => 'nullable|array',
            'classroom_ids.*' => 'exists:classrooms,id',
        ]);

        $teacher->user->update($request->only('name', 'email'));
        $teacher->update($request->only('nip', 'specialization', 'status'));

        if ($request->has('classroom_ids')) {
            $teacher->classrooms()->sync($request->classroom_ids);
        }

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

    public function export()
    {
        $teachers = Teacher::with(['user', 'classrooms'])->get();

        $callback = function () use ($teachers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['NIP', 'Nama', 'Email', 'Spesialisasi', 'Status', 'Jumlah Kelas']);

            foreach ($teachers as $teacher) {
                fputcsv($file, [
                    $teacher->nip,
                    $teacher->user->name,
                    $teacher->user->email,
                    $teacher->specialization,
                    $teacher->status,
                    $teacher->classrooms->count(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
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
                'name'     => $name,
                'email'    => $email,
                'role'     => 'guru',
                'password' => Hash::make('password123'),
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
