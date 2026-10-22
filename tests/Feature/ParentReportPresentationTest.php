<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Material;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use App\Services\ProgressReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ParentReportPresentationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 22)->startOfDay());
    }

    public function test_history_counts_assessed_materials_per_level_at_each_period_cutoff(): void
    {
        [, $wali, $student] = $this->family();
        $this->assessments($student);

        $response = $this->actingAs($wali)->get(route('wali.riwayat', ['student_id' => $student->id]))
            ->assertOk()->assertSee('Membaca (Level 1)')->assertSee('Membaca (Level 2)');
        $history = $response->viewData('riwayatData');
        $this->assertCount(2, $history);
        $this->assertSame([['level' => 'Level 1', 'skilled' => 6, 'total' => 7]], $history[0]['skills']['baca']['levels']);
        $this->assertSame([
            ['level' => 'Level 1', 'skilled' => 7, 'total' => 7],
            ['level' => 'Level 2', 'skilled' => 1, 'total' => 2],
        ], $history[1]['skills']['baca']['levels']);
        $this->assertSame([], $history[0]['skills']['tulis']['levels']);
        $this->assertSame([['level' => 'Level 1', 'skilled' => 1, 'total' => 1]], $history[1]['skills']['tulis']['levels']);
        $this->assertSame([], $history[1]['skills']['hitung']['levels']);
        $this->assertArrayNotHasKey('average', $history[0]);
        $this->assertDatabaseCount('student_progress', 12);
        $this->assertDatabaseHas('student_progress', ['student_id' => $student->id, 'status' => 'K', 'skilled_date' => '2026-07-01 00:00:00']);
        $this->capture('history', $response);
    }

    public function test_parent_pages_keep_level_counts_without_charts_percentages_or_comparison(): void
    {
        [, $wali, $student] = $this->family();
        $this->assessments($student);
        $params = ['student_id' => $student->id];
        $routes = [
            'dashboard' => route('wali.dashboard', $params),
            'report' => route('wali.rapor', $params),
            'history' => route('wali.riwayat', $params),
            'period' => route('wali.rapor.periode', $params + ['period_number' => 1, 'period_end' => '2026-08-31']),
        ];
        foreach ($routes as $name => $url) {
            $response = $this->actingAs($wali)->get($url)->assertOk()
                ->assertSee('Membaca (Level 1)')->assertSee('materi terampil')
                ->assertDontSee('<canvas', false)->assertDontSee('new Chart')
                ->assertDontSee('Grafik Perkembangan')->assertDontSee('Perbandingan dengan Periode Sebelumnya')
                ->assertDontSee('Rata-rata')->assertDontSee('waliRaporRadarChart');
            preg_match('/<main\b[^>]*>(.*?)<\/main>/s', $response->getContent(), $main);
            $this->assertStringNotContainsString('%', strip_tags($main[1]));
            $this->capture($name, $response);
        }
    }

    public function test_switching_children_changes_counts_and_cannot_expose_another_parents_grades(): void
    {
        [, $wali, $first] = $this->family();
        $this->progress($first, 'baca', 'Level 1', ['skilled_date' => '2026-07-01']);
        $second = Student::create(['name' => 'Anak Kedua', 'gender' => 'L', 'join_date' => '2026-06-01', 'parent_id' => $wali->id, 'teacher_id' => $first->teacher_id, 'status' => 'aktif']);
        $this->progress($second, 'baca', 'Level 2', ['start_date' => '2026-07-01']);
        $otherParent = User::factory()->create(['role' => 'wali']);
        $private = Student::create(['name' => 'Anak Rahasia', 'gender' => 'L', 'join_date' => '2026-06-01', 'parent_id' => $otherParent->id, 'status' => 'aktif']);
        $this->progress($private, 'baca', 'Level 4', ['skilled_date' => '2026-07-01']);

        foreach (['wali.dashboard', 'wali.rapor', 'wali.riwayat'] as $route) {
            $this->actingAs($wali)->get(route($route, ['student_id' => $first->id]))->assertOk()->assertSee('Membaca (Level 1)')->assertDontSee('Membaca (Level 2)');
            $this->get(route($route, ['student_id' => $second->id]))->assertOk()->assertSee('Membaca (Level 2)')->assertDontSee('Membaca (Level 1)');
            $this->get(route($route, ['student_id' => $private->id]))->assertOk()->assertDontSee('Anak Rahasia')->assertDontSee('Membaca (Level 4)');
        }
        $this->get(route('wali.rapor.periode', ['student_id' => $private->id, 'period_number' => 1, 'period_end' => '2026-08-31']))->assertNotFound();
    }

    public function test_empty_accounts_and_unassessed_subjects_do_not_display_zero_over_zero_or_fake_levels(): void
    {
        $wali = User::factory()->create(['role' => 'wali']);
        foreach (['wali.dashboard', 'wali.rapor', 'wali.riwayat'] as $route) {
            $this->actingAs($wali)->get(route($route))->assertOk()->assertDontSee('<canvas', false)->assertDontSee('0/0');
        }
        $student = Student::create(['name' => 'Belum Dinilai', 'gender' => 'P', 'parent_id' => $wali->id, 'join_date' => '2026-06-01', 'status' => 'aktif']);
        $this->progress($student, 'baca', 'Level 1', ['status' => 'T']);
        foreach (['wali.dashboard', 'wali.rapor', 'wali.riwayat'] as $route) {
            $response = $this->get(route($route, ['student_id' => $student->id]))->assertOk()
                ->assertSee('Belum ada materi yang dinilai.')->assertDontSee('0/0')->assertDontSee('Membaca (Level 1)');
            if ($route === 'wali.dashboard') {
                $this->capture('empty-grades', $response);
            }
        }
        $student->update(['join_date' => '2027-01-01']);
        $this->get(route('wali.riwayat', ['student_id' => $student->id]))->assertOk()->assertSee('Belum Ada Riwayat');
    }

    public function test_teacher_admin_and_pdf_reports_keep_grades_attendance_and_notes_without_radar(): void
    {
        [$guru, $wali, $student] = $this->family();
        $this->progress($student, 'baca', 'Level 1', ['skilled_date' => '2026-07-01']);
        Attendance::create(['student_id' => $student->id, 'attendance_date' => '2026-07-01', 'status' => 'hadir']);
        $params = ['student_id' => $student->id, 'period_number' => 1];
        $teacher = $this->actingAs($guru)->get(route('guru.rapor', $params))->assertOk();
        $parent = $this->actingAs($wali)->get(route('wali.rapor', $params))->assertOk();
        $admin = User::factory()->create(['role' => 'admin']);
        $adminReport = $this->actingAs($admin)->get(route('admin.rapor.show', ['student' => $student->id, 'period_number' => 1, 'year' => 2026]))->assertOk();
        foreach ([$teacher, $parent, $adminReport] as $response) {
            $response->assertDontSee('<canvas', false)->assertDontSee('RadarChart')
                ->assertSee('Total Periode 3 Bulan')->assertSee('Catatan latihan membaca.');
            $this->assertSame(1, $response->viewData('attendanceReport')['summary']['hadir']);
            $this->assertSame('T', $response->viewData('reportData')['baca']['details']->first()->display_status);
        }
        $this->get(route('rapor.download', ['token' => $student->report_token, 'period_number' => 1]))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->capture('teacher-report', $teacher);
        $this->capture('admin-report', $adminReport);
    }

    public function test_level_summary_sorts_naturally_and_does_not_invent_level_one(): void
    {
        [, , $student] = $this->family();
        foreach (['Level 10', 'Level 2', 'Level 1', ''] as $level) {
            $this->progress($student, 'baca', $level, ['skilled_date' => '2026-07-01']);
        }
        $report = app(ProgressReportService::class)->reportData($student, today());
        $this->assertSame(['Level 1', 'Level 2', 'Level 10', 'Tanpa Level'], array_column($report['baca']['levels'], 'level'));
        $this->assertSame([1, 1, 1, 1], array_column($report['baca']['levels'], 'total'));
    }

    private function family(): array
    {
        $guru = User::factory()->create(['role' => 'guru', 'name' => 'Ms. Dewi']);
        $wali = User::factory()->create(['role' => 'wali', 'name' => 'Wali Contoh']);
        $teacher = Teacher::create(['user_id' => $guru->id, 'nip' => 'T-UJI', 'status' => 'aktif']);
        $student = Student::create(['name' => 'Nabila Contoh', 'gender' => 'P', 'parent_id' => $wali->id, 'teacher_id' => $teacher->id, 'join_date' => '2026-06-01', 'status' => 'aktif', 'development_notes' => 'Catatan latihan membaca.']);

        return [$guru, $wali, $student];
    }

    private function assessments(Student $student): void
    {
        foreach (range(1, 6) as $index) {
            $this->progress($student, 'baca', 'Level 1', ['skilled_date' => '2026-07-01', 'status' => 'K']);
        }
        $this->progress($student, 'baca', 'Level 1', ['start_date' => '2026-07-01', 'skilled_date' => '2026-10-10']);
        $this->progress($student, 'baca', 'Level 2', ['skilled_date' => '2026-09-10']);
        $this->progress($student, 'baca', 'Level 2', ['start_date' => '2026-09-10']);
        $this->progress($student, 'tulis', 'Level 1', ['skilled_date' => '2026-09-10']);
        $this->progress($student, 'hitung', 'Level 1', ['status' => 'K']);
        $this->progress($student, 'baca', 'Level 3', ['start_date' => '2026-11-01']);
    }

    private function progress(Student $student, string $skill, string $level, array $dates): StudentProgress
    {
        $material = Material::create(['name' => 'Materi '.$skill.' '.Material::count(), 'skill_type' => $skill, 'level' => $level, 'sort_order' => Material::count() + 1]);

        return StudentProgress::create(['student_id' => $student->id, 'material_id' => $material->id, 'teacher_id' => $student->teacher_id] + $dates);
    }

    private function capture(string $name, $response): void
    {
        if (getenv('RAPOR_VISUAL_QA')) {
            $directory = storage_path('app/output/playwright/parent-revision');
            File::ensureDirectoryExists($directory);
            File::put($directory.'/'.$name.'.html', $response->getContent());
        }
    }
}
