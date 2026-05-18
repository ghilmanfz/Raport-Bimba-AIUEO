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
        $guardians    = User::where('role', 'wali')->orderBy('name')->get();
        $nextNis      = Student::generateNextNis();
        $totalMurid   = Student::count();
        $muridAktif   = Student::where('status', 'aktif')->count();
        $muridLulus   = Student::where('status', 'lulus')->count();
        $muridPindah  = Student::where('status', 'pindah')->count();

        return view('admin.murid', compact('students', 'classrooms', 'teachers', 'guardians', 'nextNis', 'totalMurid', 'muridAktif', 'muridLulus', 'muridPindah'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nis' => $request->filled('nis') ? $request->nis : Student::generateNextNis(),
        ]);

        $request->validate([
            'name'         => 'required|string|max:255',
            'nis'          => 'required|string|unique:students,nis',
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id'   => 'nullable|exists:teachers,id',
            'join_date'    => 'required|date',
            'status'       => 'required|in:aktif,lulus,pindah',
            'parent_id'    => 'nullable|exists:users,id',
            'father_name'  => 'nullable|string|max:255',
            'mother_name'  => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'mother_phone' => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:1000',
            'parent_email' => 'nullable|email',
        ]);

        if ($request->filled('teacher_id')) {
            $assignedCount = Student::where('teacher_id', $request->teacher_id)->count();
            if ($assignedCount >= 25) {
                return redirect()->route('admin.murid')->withErrors(['teacher_id' => 'Guru pembimbing sudah mencapai maksimal 25 siswa.'])->withInput();
            }
        }

        if ($request->filled('parent_id') && !User::where('id', $request->parent_id)->where('role', 'wali')->exists()) {
            return redirect()->route('admin.murid')->withErrors(['parent_id' => 'Data wali murid tidak valid.'])->withInput();
        }

        // Choose existing wali, otherwise create a new wali from form.
        $parentId = null;
        $createdParentPassword = null;
        if ($request->filled('parent_id')) {
            $parentId = (int) $request->parent_id;
        } elseif ($request->filled('father_name') || $request->filled('mother_name')) {
            $defaultParentPassword = 'password123';
            $fallbackEmail = 'wali' . now()->timestamp . rand(100, 999) . '@raportbimba.local';
            $parent = User::create([
                'name'     => trim(($request->father_name ?? '') . ' & ' . ($request->mother_name ?? '')) ?: ('Wali ' . $request->name),
                'email'    => $request->parent_email ?: $fallbackEmail,
                'role'     => 'wali',
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'father_phone' => $request->father_phone,
                'mother_phone' => $request->mother_phone,
                'address'  => $request->address,
                'password' => Hash::make($defaultParentPassword),
                'plain_password' => $defaultParentPassword,
            ]);
            $parentId = $parent->id;
            $createdParentPassword = $defaultParentPassword;
        }

        Student::create([
            'name'         => $request->name,
            'nis'          => $request->nis,
            'classroom_id' => $request->classroom_id,
            'teacher_id'   => $request->teacher_id,
            'parent_id'    => $parentId,
            'join_date'    => $request->join_date,
            'status'       => $request->status,
        ]);

        Notification::notifyAdmins(
            'Murid Baru Ditambahkan',
            'Data murid ' . $request->name . ' berhasil ditambahkan ke sistem.',
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
            'nis'          => 'required|string|unique:students,nis,' . $student->id,
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id'   => 'nullable|exists:teachers,id',
            'join_date'    => 'required|date',
            'status'       => 'required|in:aktif,lulus,pindah',
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
            'nis'          => $request->nis,
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
