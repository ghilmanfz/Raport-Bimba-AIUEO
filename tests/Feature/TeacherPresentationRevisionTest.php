<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Material;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TeacherPresentationRevisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 11, 1)->setTime(10, 0));
    }

    public function test_dashboard_keeps_own_active_student_total_and_schedules_without_charts(): void
    {
        $teacher = $this->teacher();
        $level = Classroom::create(['name' => 'Level 1', 'level' => 'Level 1', 'capacity' => 25]);
        $first = $this->student($teacher, ['name' => 'Alif Putra', 'classroom_id' => $level->id]);
        $second = $this->student($teacher, ['name' => 'Nabila Rahma', 'join_date' => '2026-08-11', 'classroom_id' => $level->id]);
        $this->student($teacher, ['name' => 'Siswa Sudah Lulus', 'status' => 'lulus']);
        $this->student($this->teacher(), ['name' => 'Siswa Guru Lain']);
        $this->progress($first);

        $response = $this->actingAs($teacher->user)->get(route('guru.dashboard'))->assertOk()
            ->assertSee('Total Siswa')->assertSee('Jadwal Otomatis Pembagian Rapor')->assertSee('Input Nilai Baru')
            ->assertSee($first->name)->assertSee($second->name)
            ->assertDontSee('Siswa Sudah Lulus')->assertDontSee('Siswa Guru Lain')
            ->assertDontSee('Distribusi Nilai Modul')->assertDontSee('Status Kompetensi')
            ->assertDontSee('<canvas', false)->assertDontSee('new Chart')->assertDontSee('dashboardBarChart')->assertDontSee('dashboardDonutChart');
        $this->assertSame(['total_murid' => 2], $response->viewData('stats'));
        $this->assertEqualsCanonicalizing([$first->name, $second->name], $response->viewData('nextRaporSchedules')->pluck('student_name')->all());
        $this->assertDatabaseCount('student_progress', 1);
        $this->capture('dashboard', $response);
    }

    public function test_empty_dashboard_and_missing_teacher_profile_render_without_chart_fallbacks(): void
    {
        $teacher = $this->teacher();
        $withoutProfile = User::factory()->create(['role' => 'guru']);
        foreach ([$teacher->user, $withoutProfile] as $user) {
            $response = $this->actingAs($user)->get(route('guru.dashboard'))->assertOk()
                ->assertSee('Belum ada siswa dengan tanggal masuk terdaftar.')
                ->assertDontSee('Distribusi Nilai Modul')->assertDontSee('Status Kompetensi')->assertDontSee('<canvas', false);
            $this->assertSame(['total_murid' => 0], $response->viewData('stats'));
            $this->assertCount(0, $response->viewData('nextRaporSchedules'));
        }
        $this->capture('empty-dashboard', $response);
    }

    public function test_current_and_historical_reports_keep_grades_attendance_and_note_editing_without_comparison(): void
    {
        $teacher = $this->teacher();
        $student = $this->student($teacher);
        $grade = $this->progress($student);
        Attendance::create(['student_id' => $student->id, 'attendance_date' => '2026-09-01', 'status' => 'hadir']);

        foreach ([1 => 'K', 2 => 'T'] as $number => $status) {
            $response = $this->actingAs($teacher->user)->get(route('guru.rapor', ['student_id' => $student->id, 'period_number' => $number]))
                ->assertOk()->assertSee('Catatan asli siswa.')->assertSee('Hasil Absensi per Bulan Belajar')
                ->assertSee('Catatan Perkembangan Manual')->assertSee('Update Nilai Baru')
                ->assertSee('QR Code Rapor')->assertDontSee('Perbandingan dengan Periode Sebelumnya')
                ->assertDontSee('Perbandingan pencapaian')->assertDontSee('lucide:git-compare')->assertDontSee('<canvas', false);
            $this->assertSame($status, $response->viewData('reportData')['baca']['details']->first()->display_status);
            $this->assertSame($number === 1 ? 1 : 0, $response->viewData('attendanceReport')['summary']['hadir']);
            $this->assertArrayNotHasKey('prevReportData', $response->original->getData());
            $this->capture('report-'.$number, $response);
        }

        $this->post(route('guru.rapor.notes'), ['student_id' => $student->id, 'period_number' => 1, 'development_notes' => 'Catatan setelah revisi.'])
            ->assertRedirect(route('guru.rapor', ['student_id' => $student->id, 'period_number' => 1]))->assertSessionHasNoErrors();
        $this->assertSame('Catatan setelah revisi.', $student->fresh()->development_notes);
        $this->assertSame('T', $grade->fresh()->display_status);
        $this->assertDatabaseCount('student_progress', 1);
        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_shared_admin_report_and_pdf_do_not_require_previous_period_data(): void
    {
        $teacher = $this->teacher();
        $student = $this->student($teacher);
        $this->progress($student);
        $admin = User::factory()->create(['role' => 'admin']);
        $params = ['student' => $student->id, 'period_number' => 1, 'year' => 2026];
        $response = $this->actingAs($admin)->get(route('admin.rapor.show', $params))->assertOk()
            ->assertSee('Catatan asli siswa.')->assertDontSee('Perbandingan dengan Periode Sebelumnya');
        $this->assertArrayNotHasKey('prevReportData', $response->original->getData());
        $this->get(route('admin.rapor.pdf', $params))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->capture('admin-report', $response);
    }

    private function teacher(): Teacher
    {
        $user = User::factory()->create(['role' => 'guru', 'name' => 'Dewi Lestari']);

        return Teacher::create(['user_id' => $user->id, 'nip' => Teacher::generateNip(), 'status' => 'aktif']);
    }

    private function student(Teacher $teacher, array $attributes = []): Student
    {
        return Student::create($attributes + ['name' => 'Alif Contoh', 'gender' => 'L', 'join_date' => '2026-07-01', 'teacher_id' => $teacher->id, 'status' => 'aktif', 'development_notes' => 'Catatan asli siswa.']);
    }

    private function progress(Student $student): StudentProgress
    {
        $material = Material::create(['name' => 'Mengenal Huruf', 'skill_type' => 'baca', 'level' => 'Level 1', 'sort_order' => 1]);

        return StudentProgress::create(['student_id' => $student->id, 'material_id' => $material->id, 'teacher_id' => $student->teacher_id, 'start_date' => '2026-07-01', 'skilled_date' => '2026-10-15', 'status' => 'T']);
    }

    private function capture(string $name, $response): void
    {
        if (getenv('RAPOR_VISUAL_QA')) {
            $directory = storage_path('app/output/playwright/teacher-revision');
            File::ensureDirectoryExists($directory);
            File::put($directory.'/'.$name.'.html', $response->getContent());
        }
    }
}
