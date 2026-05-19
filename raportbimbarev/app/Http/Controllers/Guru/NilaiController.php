<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Notification;
use App\Models\Student;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classroomIds = $teacher ? $teacher->classrooms()->pluck('classrooms.id') : collect();

        $students = Student::whereIn('classroom_id', $classroomIds)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $selectedStudentId = $request->input('student_id', $students->first()?->id);
        $selectedSkill     = $request->input('skill', 'baca');
        $selectedLevel     = $request->input('level', 'Level 1');

        $materials = Material::where('skill_type', $selectedSkill)
            ->where('level', $selectedLevel)
            ->orderBy('sort_order')
            ->get();

        $progress = [];
        if ($selectedStudentId) {
            $existing = StudentProgress::where('student_id', $selectedStudentId)
                ->whereIn('material_id', $materials->pluck('id'))
                ->get()
                ->keyBy('material_id');

            foreach ($materials as $material) {
                $p = $existing->get($material->id);
                $progress[] = [
                    'material'        => $material,
                    'start_date'      => $p?->start_date?->format('Y-m-d'),
                    'understand_date' => $p?->understand_date?->format('Y-m-d'),
                    'skilled_date'    => $p?->skilled_date?->format('Y-m-d'),
                    'status'          => $p?->status ?? 'K',
                ];
            }
        }

        $selectedStudent = $selectedStudentId ? Student::find($selectedStudentId) : null;

        return view('guru.nilai', compact(
            'students', 'selectedStudent', 'selectedSkill', 'selectedLevel', 'progress'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'                => 'required|exists:students,id',
            'progress'                  => 'required|array',
            'progress.*.material_id'    => 'required|exists:materials,id',
            'progress.*.start_date'     => 'nullable|date',
            'progress.*.understand_date'=> 'nullable|date',
            'progress.*.skilled_date'   => 'nullable|date',
        ]);

        $teacher = Auth::user()->teacher;

        foreach ($request->progress as $item) {
            $status = 'K';
            if (!empty($item['skilled_date'])) $status = 'T';
            elseif (!empty($item['understand_date'])) $status = 'P';

            StudentProgress::updateOrCreate(
                [
                    'student_id'  => $request->student_id,
                    'material_id' => $item['material_id'],
                ],
                [
                    'teacher_id'      => $teacher?->id,
                    'start_date'      => $item['start_date'] ?: null,
                    'understand_date' => $item['understand_date'] ?: null,
                    'skilled_date'    => $item['skilled_date'] ?: null,
                    'status'          => $status,
                ]
            );
        }

        $student = Student::find($request->student_id);
        $guruName = Auth::user()->name;

        // Notify the teacher
        Notification::send(
            Auth::id(),
            'Nilai Disimpan',
            'Nilai untuk murid ' . ($student->name ?? '') . ' berhasil disimpan.',
            'success',
            'lucide:check-circle',
            route('guru.nilai')
        );

        // Notify the parent if exists
        if ($student && $student->parent_id) {
            Notification::send(
                $student->parent_id,
                'Nilai Anak Diperbarui',
                'Guru ' . $guruName . ' telah memperbarui nilai ' . $student->name . '.',
                'info',
                'lucide:file-text',
                route('wali.dashboard')
            );
        }

        // Notify admins
        Notification::notifyAdmins(
            'Input Nilai Baru',
            'Guru ' . $guruName . ' menginput nilai untuk murid ' . ($student->name ?? '') . '.',
            'info',
            'lucide:file-text',
            route('admin.murid')
        );

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }
}
