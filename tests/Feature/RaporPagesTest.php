<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Material;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaporPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_open_and_print_student_report(): void
    {
        $data = $this->createRaporData();

        $this->actingAs($data['teacherUser'])
            ->get(route('guru.rapor', ['student_id' => $data['student']->id]))
            ->assertOk()
            ->assertSee('Ringkasan Laporan Hasil Belajar Murid')
            ->assertSee('Materi Paham');
    }

    public function test_wali_can_open_student_report(): void
    {
        $data = $this->createRaporData();

        $this->actingAs($data['waliUser'])
            ->get(route('wali.rapor', ['student_id' => $data['student']->id]))
            ->assertOk()
            ->assertSee('Ringkasan Laporan Hasil Belajar Murid')
            ->assertSee('Materi Paham');
    }

    public function test_wali_period_report_includes_paham_progress_by_understand_date(): void
    {
        $data = $this->createRaporData();

        $this->actingAs($data['waliUser'])
            ->get(route('wali.rapor.periode', [
                'student_id' => $data['student']->id,
                'period_end' => '2026-03-31',
                'period_number' => 1,
            ]))
            ->assertOk()
            ->assertSee('Materi Paham');
    }

    public function test_wali_dashboard_can_switch_selected_child_with_dropdown(): void
    {
        $data = $this->createRaporData();
        $secondChild = Student::create([
            'nis' => 'BM002',
            'name' => 'Bima Test',
            'classroom_id' => $data['student']->classroom_id,
            'parent_id' => $data['waliUser']->id,
            'join_date' => '2026-02-01',
            'status' => 'aktif',
        ]);

        $this->actingAs($data['waliUser'])
            ->get(route('wali.dashboard', ['student_id' => $secondChild->id]))
            ->assertOk()
            ->assertSee('Pilih Anak')
            ->assertSee('Aisyah Test')
            ->assertSee('Bima Test')
            ->assertSee('Semangat Belajar, Bima Test!')
            ->assertDontSee('Semangat Belajar, Aisyah Test!');
    }

    /**
     * @return array{teacherUser: User, waliUser: User, student: Student}
     */
    private function createRaporData(): array
    {
        $teacherUser = User::factory()->create(['role' => 'guru', 'name' => 'Guru Test']);
        $waliUser = User::factory()->create(['role' => 'wali', 'name' => 'Wali Test']);

        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'nip' => 'T-001',
            'status' => 'aktif',
        ]);

        $classroom = Classroom::create([
            'name' => 'Level 1 - A',
            'level' => 'Level 1',
            'capacity' => 20,
        ]);

        $teacher->classrooms()->attach($classroom);

        $student = Student::create([
            'nis' => 'BM001',
            'name' => 'Aisyah Test',
            'classroom_id' => $classroom->id,
            'parent_id' => $waliUser->id,
            'join_date' => '2026-01-01',
            'status' => 'aktif',
        ]);

        $material = Material::create([
            'name' => 'Materi Paham',
            'skill_type' => 'baca',
            'level' => 'Level 1',
            'sort_order' => 1,
        ]);

        StudentProgress::create([
            'student_id' => $student->id,
            'material_id' => $material->id,
            'teacher_id' => $teacher->id,
            'start_date' => '2026-01-10',
            'understand_date' => '2026-02-01',
            'status' => 'P',
        ]);

        return [
            'teacherUser' => $teacherUser,
            'waliUser' => $waliUser,
            'student' => $student,
        ];
    }
}
