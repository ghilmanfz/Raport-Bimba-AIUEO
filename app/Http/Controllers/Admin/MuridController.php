<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MuridController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['classroom', 'parent']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($classFilter = $request->input('classroom_id')) {
            $query->where('classroom_id', $classFilter);
        }

        $students     = $query->latest()->paginate(10);
        $classrooms   = Classroom::orderBy('name')->get();
        $totalMurid   = Student::count();
        $muridAktif   = Student::where('status', 'aktif')->count();
        $muridCuti    = Student::where('status', 'cuti')->count();

        return view('admin.murid', compact('students', 'classrooms', 'totalMurid', 'muridAktif', 'muridCuti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'join_date'    => 'required|date',
            'status'       => 'required|in:aktif,cuti,nonaktif',
            'parent_name'  => 'nullable|string|max:255',
            'parent_email' => 'nullable|email|unique:users,email',
        ]);

        // Create parent user if provided
        $parentId = null;
        if ($request->filled('parent_email')) {
            $parent = User::create([
                'name'     => $request->parent_name ?? 'Wali ' . $request->name,
                'email'    => $request->parent_email,
                'role'     => 'wali',
                'password' => Hash::make('password123'),
            ]);
            $parentId = $parent->id;
        }

        $student = Student::create([
            'name'         => $request->name,
            'classroom_id' => $request->classroom_id,
            'parent_id'    => $parentId,
            'join_date'    => $request->join_date,
            'status'       => $request->status,
        ]);

        Notification::notifyAdmins(
            'Murid Baru Ditambahkan',
            'Data murid ' . $request->name . ' ('. $student->nis .') berhasil ditambahkan ke sistem.',
            'success',
            'lucide:user-plus',
            route('admin.murid')
        );

        return redirect()->route('admin.murid')->with('success', 'Murid berhasil ditambahkan dengan NIS: ' . $student->nis);
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'join_date'    => 'required|date',
            'status'       => 'required|in:aktif,cuti,nonaktif',
        ]);

        $student->update($request->only('name', 'classroom_id', 'join_date', 'status'));

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

    public function export()
    {
        $students = Student::with(['classroom', 'parent'])->get();

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['NIS', 'Nama Murid', 'Tahapan', 'Wali Murid', 'Email Wali', 'Tgl. Bergabung', 'Status'], ';');

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->nis,
                    $student->name,
                    $student->classroom?->name ?? '-',
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
