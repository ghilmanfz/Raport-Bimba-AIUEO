<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    public function test_admin_can_create_update_and_remove_one_attendance_per_student_per_day(): void
    {
        $admin = $this->makeUser('admin');
        $classroom = $this->makeClassroom();
        $student = $this->makeStudent($classroom);
        $date = now()->toDateString();

        $payload = [
            'attendance_date' => $date,
            'attendances' => [[
                'student_id' => $student->id,
                'status' => 'hadir',
                'notes' => 'Tepat waktu',
            ]],
        ];

        $this->actingAs($admin)->post(route('admin.absensi.store'), $payload)->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'hadir',
            'classroom_id' => $classroom->id,
            'recorded_by' => $admin->id,
        ]);
        $this->assertSame($date, Attendance::firstOrFail()->attendance_date->toDateString());

        $payload['attendances'][0]['status'] = 'sakit';
        $this->actingAs($admin)->post(route('admin.absensi.store'), $payload)->assertRedirect();

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'sakit',
        ]);
        $this->assertSame($date, Attendance::firstOrFail()->attendance_date->toDateString());

        $payload['attendances'][0]['status'] = null;
        $payload['attendances'][0]['notes'] = null;
        $this->actingAs($admin)->post(route('admin.absensi.store'), $payload)->assertRedirect();

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_guru_cannot_record_attendance_for_another_teachers_student(): void
    {
        $guruUser = $this->makeUser('guru');
        $otherGuruUser = $this->makeUser('guru');
        $teacher = $this->makeTeacher($guruUser);
        $otherTeacher = $this->makeTeacher($otherGuruUser);
        $classroom = $this->makeClassroom();
        $student = $this->makeStudent($classroom, $otherTeacher);

        $response = $this->actingAs($guruUser)->post(route('guru.absensi.store'), [
            'attendance_date' => now()->toDateString(),
            'attendances' => [[
                'student_id' => $student->id,
                'status' => 'hadir',
                'notes' => null,
            ]],
        ]);

        $response->assertSessionHasErrors('attendances');
        $this->assertDatabaseCount('attendances', 0);
        $this->assertNotNull($teacher);
    }

    public function test_guru_can_record_attendance_only_for_an_active_assigned_student(): void
    {
        $guru = $this->makeUser('guru');
        $teacher = $this->makeTeacher($guru);
        $classroom = $this->makeClassroom();
        $student = $this->makeStudent($classroom, $teacher);

        $this->actingAs($guru)
            ->post(route('guru.absensi.store'), [
                'attendance_date' => now()->toDateString(),
                'attendances' => [[
                    'student_id' => $student->id,
                    'status' => 'izin',
                    'notes' => 'Keperluan keluarga',
                ]],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'recorded_by' => $guru->id,
            'status' => 'izin',
            'notes' => 'Keperluan keluarga',
        ]);
    }

    public function test_wali_only_sees_attendance_for_their_own_children(): void
    {
        $wali = $this->makeUser('wali');
        $otherWali = $this->makeUser('wali');
        $classroom = $this->makeClassroom();
        $ownChild = $this->makeStudent($classroom, null, $wali, 'Anak Milik Wali');
        $otherChild = $this->makeStudent($classroom, null, $otherWali, 'Anak Milik Orang Lain');

        Attendance::create([
            'student_id' => $ownChild->id,
            'classroom_id' => $classroom->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'hadir',
        ]);
        Attendance::create([
            'student_id' => $otherChild->id,
            'classroom_id' => $classroom->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'alpa',
        ]);

        $this->actingAs($wali)
            ->get(route('wali.absensi.index', ['student_id' => $otherChild->id]))
            ->assertOk()
            ->assertSee('Anak Milik Wali')
            ->assertDontSee('Anak Milik Orang Lain');
    }

    public function test_wali_summary_uses_no_data_instead_of_zero_percent_when_history_is_empty(): void
    {
        $wali = $this->makeUser('wali');
        $classroom = $this->makeClassroom();
        $this->makeStudent($classroom, parent: $wali);

        $this->actingAs($wali)
            ->get(route('wali.absensi.index'))
            ->assertOk()
            ->assertViewHas('summary', fn (array $summary) => $summary['total'] === 0
                && $summary['percentage'] === null
            );
    }

    public function test_admin_report_contains_monthly_totals_and_percentage(): void
    {
        $admin = $this->makeUser('admin');
        $classroom = $this->makeClassroom();
        $student = $this->makeStudent($classroom);

        Attendance::create(['student_id' => $student->id, 'classroom_id' => $classroom->id, 'attendance_date' => now()->startOfMonth()->toDateString(), 'status' => 'hadir']);
        Attendance::create(['student_id' => $student->id, 'classroom_id' => $classroom->id, 'attendance_date' => now()->startOfMonth()->addDay()->toDateString(), 'status' => 'izin']);

        $this->actingAs($admin)
            ->get(route('admin.absensi.report', ['month' => now()->format('Y-m'), 'student_id' => $student->id]))
            ->assertOk()
            ->assertViewHas('overall', fn (array $overall) => $overall['total'] === 2
                && $overall['hadir'] === 1
                && $overall['izin'] === 1
                && $overall['percentage'] === 50.0
            );
    }

    public function test_admin_report_defaults_to_active_students_and_can_explicitly_show_all_statuses(): void
    {
        $admin = $this->makeUser('admin');
        $classroom = $this->makeClassroom();
        $activeStudent = $this->makeStudent($classroom, name: 'Murid Aktif');
        $inactiveStudent = $this->makeStudent($classroom, name: 'Murid Keluar', status: 'keluar');

        $defaultReport = $this->actingAs($admin)
            ->get(route('admin.absensi.report', ['month' => now()->format('Y-m')]))
            ->assertOk()
            ->assertViewHas('studentStatus', 'aktif');

        $this->assertSame([$activeStudent->id], $defaultReport->viewData('summaryRows')->pluck('student.id')->all());
        $this->assertSame([$activeStudent->id], $defaultReport->viewData('studentOptions')->pluck('id')->all());

        $allStatusesReport = $this->actingAs($admin)
            ->get(route('admin.absensi.report', [
                'month' => now()->format('Y-m'),
                'student_status' => 'semua',
            ]))
            ->assertOk()
            ->assertViewHas('studentStatus', 'semua');

        $this->assertEqualsCanonicalizing(
            [$activeStudent->id, $inactiveStudent->id],
            $allStatusesReport->viewData('summaryRows')->pluck('student.id')->all(),
        );
        $this->assertEqualsCanonicalizing(
            [$activeStudent->id, $inactiveStudent->id],
            $allStatusesReport->viewData('studentOptions')->pluck('id')->all(),
        );
    }

    public function test_report_marks_missing_attendance_as_no_data_instead_of_zero_percent(): void
    {
        $admin = $this->makeUser('admin');
        $classroom = $this->makeClassroom();
        $student = $this->makeStudent($classroom);

        $response = $this->actingAs($admin)
            ->get(route('admin.absensi.report', ['month' => now()->format('Y-m')]))
            ->assertOk()
            ->assertSee('Belum ada data');

        $row = $response->viewData('summaryRows')->first();
        $this->assertSame($student->id, $row['student']->id);
        $this->assertSame(0, $row['total']);
        $this->assertNull($row['percentage']);
    }

    public function test_admin_cannot_record_attendance_for_an_inactive_student(): void
    {
        $admin = $this->makeUser('admin');
        $classroom = $this->makeClassroom();
        $student = $this->makeStudent($classroom, status: 'lulus');

        $this->actingAs($admin)
            ->post(route('admin.absensi.store'), [
                'attendance_date' => now()->toDateString(),
                'attendances' => [[
                    'student_id' => $student->id,
                    'status' => 'hadir',
                ]],
            ])
            ->assertSessionHasErrors('attendances');

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_daily_page_distinguishes_recorded_and_unrecorded_students(): void
    {
        $admin = $this->makeUser('admin');
        $classroom = $this->makeClassroom();
        $recordedStudent = $this->makeStudent($classroom);
        $this->makeStudent($classroom);

        Attendance::create([
            'student_id' => $recordedStudent->id,
            'classroom_id' => $classroom->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.absensi.index'))
            ->assertOk()
            ->assertViewHas('dailyStats', fn (array $stats) => $stats['total'] === 1
                && $stats['hadir'] === 1
                && $stats['unrecorded'] === 1
            );
    }

    public function test_admin_and_guru_cannot_open_a_future_attendance_date(): void
    {
        $admin = $this->makeUser('admin');
        $guru = $this->makeUser('guru');
        $this->makeTeacher($guru);
        $futureDate = now()->addDay()->toDateString();

        $this->actingAs($admin)
            ->get(route('admin.absensi.index', ['attendance_date' => $futureDate]))
            ->assertRedirect()
            ->assertSessionHasErrors('attendance_date');

        $this->actingAs($guru)
            ->get(route('guru.absensi.index', ['attendance_date' => $futureDate]))
            ->assertRedirect()
            ->assertSessionHasErrors('attendance_date');
    }

    public function test_role_middleware_blocks_guru_from_admin_attendance(): void
    {
        $guru = $this->makeUser('guru');

        $this->actingAs($guru)->get(route('admin.absensi.index'))->assertForbidden();
    }

    public function test_admin_can_download_attendance_csv_and_pdf(): void
    {
        $admin = $this->makeUser('admin');
        $classroom = $this->makeClassroom();
        $student = $this->makeStudent($classroom);

        Attendance::create([
            'student_id' => $student->id,
            'classroom_id' => $classroom->id,
            'attendance_date' => now()->startOfMonth()->toDateString(),
            'status' => 'hadir',
        ]);

        $filters = ['month' => now()->format('Y-m')];
        $csv = $this->actingAs($admin)
            ->get(route('admin.absensi.export', $filters))
            ->assertOk();

        $this->assertStringContainsString('Rasio Hadir dari Data Tercatat', $csv->streamedContent());

        $this->actingAs($admin)
            ->get(route('admin.absensi.pdf', $filters))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    private function makeUser(string $role): User
    {
        $this->sequence++;

        return User::create([
            'name' => ucfirst($role).' Test '.$this->sequence,
            'role' => $role,
            'email' => $role.$this->sequence.'@example.test',
            'password' => Hash::make('password123'),
        ]);
    }

    private function makeClassroom(): Classroom
    {
        $this->sequence++;

        return Classroom::create([
            'name' => 'Kelas Test '.$this->sequence,
            'level' => 'Level 1',
            'capacity' => 20,
        ]);
    }

    private function makeTeacher(User $user): Teacher
    {
        $this->sequence++;

        return Teacher::create([
            'user_id' => $user->id,
            'nip' => 'T-'.str_pad((string) $this->sequence, 3, '0', STR_PAD_LEFT),
            'status' => 'aktif',
        ]);
    }

    private function makeStudent(
        Classroom $classroom,
        ?Teacher $teacher = null,
        ?User $parent = null,
        ?string $name = null,
        string $status = 'aktif',
    ): Student {
        $this->sequence++;

        return Student::create([
            'nis' => 'BM'.str_pad((string) $this->sequence, 3, '0', STR_PAD_LEFT),
            'name' => $name ?? 'Murid Test '.$this->sequence,
            'gender' => 'L',
            'classroom_id' => $classroom->id,
            'teacher_id' => $teacher?->id,
            'parent_id' => $parent?->id,
            'join_date' => now()->subMonth()->toDateString(),
            'status' => $status,
        ]);
    }
}
