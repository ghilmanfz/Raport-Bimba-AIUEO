<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::query()
            ->withCount([
                'students',
                'students as active_students_count' => fn ($query) => $query->active(),
                'attendances',
            ])
            ->orderBy('name')
            ->get();

        return view('admin.kelas', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        Classroom::create($validated);

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate($this->rules($classroom));

        $activeStudentsCount = $classroom->students()->active()->count();
        if ($validated['capacity'] < $activeStudentsCount) {
            return back()
                ->withErrors(['capacity' => "Kapasitas tidak boleh kurang dari {$activeStudentsCount} murid aktif."])
                ->withInput();
        }

        $classroom->update($validated);

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->students()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data murid.');
        }

        if ($classroom->attendances()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih digunakan pada riwayat absensi.');
        }

        $classroom->teachers()->detach();
        $classroom->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil dihapus.');
    }

    private function rules(?Classroom $classroom = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('classrooms', 'name')->ignore($classroom),
            ],
            'level' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
