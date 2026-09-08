<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClassroomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_classes_and_cannot_delete_an_occupied_class(): void
    {
        $admin = User::create([
            'name' => 'Admin Kelas',
            'role' => 'admin',
            'email' => 'admin-kelas@example.test',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.kelas.store'), [
                'name' => 'Kelas Pelangi',
                'level' => 'Level 1',
                'capacity' => 15,
            ])
            ->assertRedirect(route('admin.kelas.index'));

        $classroom = Classroom::where('name', 'Kelas Pelangi')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.kelas.update', $classroom), [
                'name' => 'Kelas Pelangi Besar',
                'level' => 'Level 2',
                'capacity' => 20,
            ])
            ->assertRedirect(route('admin.kelas.index'));

        $this->assertDatabaseHas('classrooms', [
            'id' => $classroom->id,
            'name' => 'Kelas Pelangi Besar',
            'level' => 'Level 2',
            'capacity' => 20,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.kelas.index'))
            ->assertOk()
            ->assertSee('Kelas Pelangi Besar');

        Student::create([
            'nis' => 'BM-KELAS-001',
            'name' => 'Anak Kelas',
            'gender' => 'P',
            'classroom_id' => $classroom->id,
            'join_date' => now()->toDateString(),
            'status' => 'aktif',
        ]);
        Student::create([
            'nis' => 'BM-KELAS-002',
            'name' => 'Anak Kelas Kedua',
            'gender' => 'L',
            'classroom_id' => $classroom->id,
            'join_date' => now()->toDateString(),
            'status' => 'aktif',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.kelas.update', $classroom), [
                'name' => 'Kelas Pelangi Besar',
                'level' => 'Level 2',
                'capacity' => 1,
            ])
            ->assertSessionHasErrors('capacity');

        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id, 'capacity' => 20]);

        $this->actingAs($admin)
            ->delete(route('admin.kelas.destroy', $classroom))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id]);

        $historicalClassroom = Classroom::create([
            'name' => 'Kelas Bersejarah',
            'level' => 'Level 1',
            'capacity' => 10,
        ]);
        $historicalStudent = Student::create([
            'nis' => 'BM-KELAS-003',
            'name' => 'Anak Pindahan',
            'gender' => 'P',
            'classroom_id' => $historicalClassroom->id,
            'join_date' => now()->toDateString(),
            'status' => 'aktif',
        ]);
        Attendance::create([
            'student_id' => $historicalStudent->id,
            'classroom_id' => $historicalClassroom->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'hadir',
        ]);
        $historicalStudent->update(['classroom_id' => $classroom->id]);

        $this->actingAs($admin)
            ->delete(route('admin.kelas.destroy', $historicalClassroom))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('classrooms', ['id' => $historicalClassroom->id]);

        $emptyClassroom = Classroom::create([
            'name' => 'Kelas Kosong',
            'level' => 'Level 1',
            'capacity' => 10,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.kelas.destroy', $emptyClassroom))
            ->assertRedirect(route('admin.kelas.index'));

        $this->assertDatabaseMissing('classrooms', ['id' => $emptyClassroom->id]);
    }
}
