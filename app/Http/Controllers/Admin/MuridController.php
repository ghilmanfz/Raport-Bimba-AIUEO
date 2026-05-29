<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MuridController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['classroom', 'parent', 'teacher']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($classFilter = $request->input('classroom_id')) {
            $query->where('classroom_id', $classFilter);
        }

        if ($statusFilter = $request->input('status')) {
            $query->where('status', $statusFilter);
        }

        if ($teacherFilter = $request->input('teacher_id')) {
            $query->where('teacher_id', $teacherFilter);
        }

        $students     = $query->latest()->paginate(10);
        $classrooms   = Classroom::orderBy('name')->get();
        $teachers     = Teacher::with('user')->where('status', 'aktif')->get();
        $guardians    = User::where('role', 'wali')->withCount('students')->orderBy('name')->get();
        $nextNis      = Student::generateNextNis();
        $totalMurid   = Student::count();
        $muridAktif   = Student::where('status', 'aktif')->count();
        $muridLulus   = Student::where('status', 'lulus')->count();
        $muridKeluar  = Student::where('status', 'keluar')->count();

        return view('admin.murid', compact('students', 'classrooms', 'teachers', 'guardians', 'nextNis', 'totalMurid', 'muridAktif', 'muridLulus', 'muridKeluar'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'existing_parent_id' => $request->input('existing_parent_id', $request->input('parent_id')),
        ]);

        if (!$request->filled('parent_mode')) {
            $waliOption = $request->input('wali_option');

            if ($waliOption === 'pilih') {
                $resolvedParentMode = $request->filled('existing_parent_id') ? 'existing' : 'none';
            } elseif ($waliOption === 'buat') {
                $resolvedParentMode = 'new';
            } else {
                $resolvedParentMode = $request->filled('existing_parent_id')
                    ? 'existing'
                    : (($request->filled('parent_name') || $request->filled('parent_email') || $request->filled('father_name') || $request->filled('mother_name')) ? 'new' : 'none');
            }

            $request->merge([
                'parent_mode' => $resolvedParentMode,
            ]);
        }

        $request->merge([
            'nis' => $request->filled('nis') ? $request->nis : Student::generateNextNis(),
        ]);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'nis'          => 'required|string|unique:students,nis',
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id'   => 'nullable|exists:teachers,id',
            'join_date'    => 'required|date',
            'status'       => 'required|in:aktif,lulus,keluar,cuti',
            'parent_mode'  => 'required|in:none,existing,new',
            'existing_parent_id' => [
                'nullable',
                'required_if:parent_mode,existing',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'wali')),
            ],
            'parent_id'    => 'nullable|exists:users,id',
            'parent_name'  => 'nullable|string|max:255',
            'parent_password' => 'nullable|string|min:6',
            'father_name'  => 'nullable|string|max:255',
            'mother_name'  => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'mother_phone' => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:1000',
            'parent_email' => 'nullable|email|unique:users,email',
        ], [
            'name.required' => 'Nama murid wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan.',
            'classroom_id.required' => 'Tahapan wajib dipilih.',
            'classroom_id.exists' => 'Tahapan yang dipilih tidak valid.',
            'teacher_id.exists' => 'Guru pembimbing yang dipilih tidak valid.',
            'join_date.required' => 'Tanggal bergabung wajib diisi.',
            'join_date.date' => 'Format tanggal bergabung tidak valid.',
            'status.required' => 'Status murid wajib dipilih.',
            'status.in' => 'Status murid yang dipilih tidak valid.',
            'parent_mode.required' => 'Pilihan data wali wajib dipilih.',
            'parent_mode.in' => 'Pilihan data wali tidak valid.',
            'existing_parent_id.required_if' => 'Wali murid wajib dipilih.',
            'existing_parent_id.exists' => 'Wali murid yang dipilih tidak valid.',
            'parent_email.email' => 'Format email wali tidak valid.',
            'parent_email.unique' => 'Email wali sudah digunakan.',
            'parent_password.min' => 'Password wali minimal 6 karakter.',
        ]);

        if (!empty($validated['teacher_id'])) {
            $assignedCount = Student::where('teacher_id', $validated['teacher_id'])->count();
            if ($assignedCount >= 25) {
                return redirect()->route('admin.murid')->withErrors(['teacher_id' => 'Guru pembimbing sudah mencapai maksimal 25 siswa.'])->withInput();
            }
        }

        // Backward compatible guardian flow:
        // - none: no guardian
        // - existing: link selected guardian account
        // - new: create new guardian account
        $parentId = null;
        $createdParentPassword = null;
        if ($validated['parent_mode'] === 'existing') {
            $parentId = (int) $validated['existing_parent_id'];
        } elseif ($validated['parent_mode'] === 'new') {
            $defaultParentPassword = $validated['parent_password'] ?? config('app.default_wali_password', 'password123');
            $fallbackEmail = 'wali' . now()->timestamp . rand(100, 999) . '@raportbimba.local';
            $parentName = $validated['parent_name']
                ?? trim(($validated['father_name'] ?? '') . ' ' . ($validated['mother_name'] ?? ''))
                ?: ('Wali ' . $validated['name']);

            $parent = User::create([
                'name'     => $parentName,
                'email'    => $validated['parent_email'] ?: $fallbackEmail,
                'role'     => 'wali',
                'father_name' => $validated['father_name'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'father_phone' => $validated['father_phone'] ?? null,
                'mother_phone' => $validated['mother_phone'] ?? null,
                'address'  => $validated['address'] ?? null,
                'password' => Hash::make($defaultParentPassword),
                'plain_password' => $defaultParentPassword,
                'show_password_change_alert' => true,
            ]);

            $parentId = $parent->id;
            $createdParentPassword = $defaultParentPassword;
        }

        $student = Student::create([
            'name'         => $validated['name'],
            'nis'          => $validated['nis'],
            'classroom_id' => $validated['classroom_id'],
            'teacher_id'   => $validated['teacher_id'] ?? null,
            'parent_id'    => $parentId,
            'join_date'    => $validated['join_date'],
            'status'       => $validated['status'],
        ]);

        Notification::notifyAdmins(
            'Murid Baru Ditambahkan',
            'Data murid ' . $validated['name'] . ' ('. $student->nis .') berhasil ditambahkan ke sistem.',
            'success',
            'lucide:user-plus',
            route('admin.murid')
        );

        $successMessage = 'Murid berhasil ditambahkan.';
        if ($createdParentPassword) {
            $successMessage .= ' Akun wali baru dibuat dengan password default: ' . $createdParentPassword;
        }

        return redirect()->route('admin.murid')->with('success', $successMessage);
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id'   => 'nullable|exists:teachers,id',
            'join_date'    => 'required|date',
            'status'       => 'required|in:aktif,lulus,keluar,cuti',
            'parent_id'    => 'nullable|exists:users,id',
        ]);

        if ($request->filled('teacher_id') && (int) $request->teacher_id !== (int) $student->teacher_id) {
            $assignedCount = Student::where('teacher_id', $request->teacher_id)->count();
            if ($assignedCount >= 25) {
                return redirect()->route('admin.murid')->withErrors(['teacher_id' => 'Guru pembimbing sudah mencapai maksimal 25 siswa.'])->withInput();
            }
        }

        if ($request->filled('parent_id') && !User::where('id', $request->parent_id)->where('role', 'wali')->exists()) {
            return redirect()->route('admin.murid')->withErrors(['parent_id' => 'Data wali murid tidak valid.'])->withInput();
        }

        $student->update([
            'name'         => $request->name,
            'classroom_id' => $request->classroom_id,
            'teacher_id'   => $request->teacher_id ?: null,
            'join_date'    => $request->join_date,
            'status'       => $request->status,
            'parent_id'    => $request->filled('parent_id') ? (int) $request->parent_id : null,
        ]);

        Notification::notifyAdmins(
            'Data Murid Diperbarui',
            'Data murid ' . $student->name . ' telah diperbarui.',
            'info',
            'lucide:pencil',
            route('admin.murid')
        );

        return redirect()->route('admin.murid')->with('success', 'Data murid berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $name = $student->name;
        $student->delete();

        Notification::notifyAdmins(
            'Data Murid Dihapus',
            'Data murid ' . $name . ' telah dihapus dari sistem.',
            'warning',
            'lucide:trash-2',
            route('admin.murid')
        );

        return redirect()->route('admin.murid')->with('success', 'Data murid berhasil dihapus.');
    }

    public function show(Student $student)
    {
        $student->load(['classroom', 'parent', 'teacher.user', 'teacher.classrooms']);
        $studentProgress = $student->progress()->orderBy('created_at', 'desc')->get();
        
        return view('admin.murid-detail', compact('student', 'studentProgress'));
    }

    public function export()
    {
        $students = Student::with(['classroom', 'parent', 'teacher.user'])->get();

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['NIS', 'Nama Murid', 'Tahapan', 'Guru Pembimbing', 'Wali Murid', 'Email Wali', 'Tgl. Bergabung', 'Status'], ';');

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->nis,
                    $student->name,
                    $student->classroom?->name ?? '-',
                    $student->teacher?->user->name ?? '-',
                    $student->parent?->name ?? '-',
                    $student->parent?->email ?? '-',
                    $student->join_date->format('d/m/Y'),
                    ucfirst($student->status),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data-murid-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
