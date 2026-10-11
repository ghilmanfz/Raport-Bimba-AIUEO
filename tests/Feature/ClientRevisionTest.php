<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Material;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use App\Services\ReportPeriodService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ClientRevisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 9)->startOfDay());
    }

    public function test_attendance_groups_by_assigned_teacher_instead_of_learning_level(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dewi = $this->teacher('Ms. Dewi');
        $rina = $this->teacher('Ms. Rina');
        $level = Classroom::create(['name' => 'Level 1', 'level' => 'Level 1', 'capacity' => 25]);
        $child = $this->student($dewi, ['name' => 'Anak Dewi', 'classroom_id' => $level->id]);
        $this->student($rina, ['name' => 'Anak Rina', 'classroom_id' => $level->id]);

        $response = $this->actingAs($admin)->get(route('admin.absensi.index', ['teacher_id' => $dewi->id]))
            ->assertOk()->assertSee('Kelas Ms. Dewi')->assertSee('Anak Dewi')->assertDontSee('Anak Rina')
            ->assertDontSee('Level 1')->assertDontSee('name="classroom_id"', false);
        $this->assertSame([$child->id], $response->viewData('students')->modelKeys());
        $this->assertSame('Level 1', $child->fresh()->classroom->level);

        $this->post(route('admin.absensi.store'), [
            'attendance_date' => '2026-10-09', 'teacher_id' => $dewi->id,
            'attendances' => [['student_id' => $child->id, 'status' => 'hadir']],
        ])->assertRedirect(route('admin.absensi.index', ['attendance_date' => '2026-10-09', 'teacher_id' => $dewi->id]));
        $this->assertDatabaseHas('attendances', ['student_id' => $child->id, 'classroom_id' => $level->id]);

        $this->capture('daily-attendance', $response);
    }

    public function test_year_and_month_filters_limit_totals_and_keep_historical_inactive_students(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $child = $this->student($this->teacher(), ['status' => 'keluar']);
        $this->attendance($child, '2024-12-31', 'alpa');
        $this->attendance($child, '2025-01-01', 'hadir');
        $this->attendance($child, '2025-12-31', 'izin');
        $this->attendance($child, '2026-01-01', 'sakit');

        $response = $this->actingAs($admin)->get(route('admin.absensi.report', ['year' => 2025]))
            ->assertOk()->assertSee('Rekap Tahunan per Anak')->assertViewHas('studentStatus', 'semua');
        $this->assertSame(2, $response->viewData('overall')['total']);
        $this->assertSame(50.0, $response->viewData('overall')['percentage']);
        $this->assertSame(['2025-12-31', '2025-01-01'], $response->viewData('dailySummary')->pluck('date')->all());
        $this->get(route('admin.absensi.report', ['year' => 2025, 'report_month' => 12]))->assertOk()
            ->assertViewHas('overall', fn ($summary) => $summary['total'] === 1 && $summary['izin'] === 1);
        $this->get(route('admin.absensi.report', ['month' => '2025-01', 'student_id' => $child->id]))->assertOk()
            ->assertViewHas('overall', fn ($summary) => $summary['total'] === 1 && $summary['hadir'] === 1);
        $this->assertDatabaseCount('attendances', 4);
        $this->capture('annual-attendance', $response);
    }

    public function test_attendance_exports_use_the_same_year_and_teacher_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dewi = $this->teacher('Ms. Dewi');
        $child = $this->student($dewi);
        $other = $this->student($this->teacher('Ms. Rina'), ['name' => 'Anak Lain']);
        $this->attendance($child, '2025-01-01', 'hadir');
        $this->attendance($child, '2026-01-01', 'alpa');
        $this->attendance($other, '2025-01-01', 'alpa');
        $params = ['year' => 2025, 'teacher_id' => $dewi->id];
        $csv = $this->actingAs($admin)->get(route('admin.absensi.export', $params))->assertOk()->assertDownload('laporan-absensi-2025.csv')->streamedContent();
        $this->assertStringContainsString('Tahun 2025', $csv);
        $this->assertStringContainsString('Ms. Dewi', $csv);
        $this->assertStringNotContainsString('Anak Lain', $csv);
        $this->assertStringNotContainsString('Kelas Saat Ini', $csv);
        $row = str_getcsv(explode("\n", trim($csv))[1], ';');
        $this->assertSame('1', $row[5]);
        $this->assertSame('0', $row[8]);
        $this->get(route('admin.absensi.pdf', $params))->assertOk()
            ->assertHeader('content-type', 'application/pdf')->assertDownload('laporan-absensi-2025.pdf');
    }

    public function test_rapor_year_uses_period_end_and_preserves_cross_year_attendance(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Uji']);
        $child = $this->student($this->teacher('Ms. Dewi'), ['join_date' => '2025-11-15', 'status' => 'keluar']);
        $this->attendance($child, '2025-12-31', 'hadir');
        $this->attendance($child, '2026-01-01', 'izin');
        $this->attendance($child, '2026-02-15', 'alpa');
        $material = Material::create(['name' => 'Modul Uji', 'skill_type' => 'baca', 'level' => 'Level 1', 'sort_order' => 1]);
        StudentProgress::create(['student_id' => $child->id, 'material_id' => $material->id, 'teacher_id' => $child->teacher_id, 'start_date' => '2025-12-01', 'skilled_date' => '2026-03-01', 'status' => 'T']);

        $list = $this->actingAs($admin)->get(route('admin.rapor', ['year' => 2026]))->assertOk()->assertSee($child->name);
        $this->assertSame([4, 3, 2, 1], collect($list->viewData('raporRows')->items())->pluck('period.number')->all());
        $this->get(route('admin.rapor', ['year' => 2025]))->assertOk()->assertSee('Belum ada data rapor untuk filter ini.');
        $params = ['student' => $child->id, 'year' => 2026, 'period_number' => 1];
        $report = $this->get(route('admin.rapor.show', $params))->assertOk()
            ->assertDontSee('Catatan Perkembangan Manual')->assertSee('Laporan Rapor');
        $this->assertSame(2, $report->viewData('attendanceReport')['summary']['total']);
        $this->assertSame('2025-11-15', $report->viewData('attendanceReport')['period']['start']->toDateString());
        $this->assertSame('2026-02-14', $report->viewData('attendanceReport')['period']['end']->toDateString());
        $this->assertSame('K', $report->viewData('reportData')['baca']['details']->first()->display_status);
        $this->get(route('admin.rapor.pdf', $params))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->get(route('admin.rapor.show', array_replace($params, ['year' => 2025])))->assertStatus(422);
        $this->get(route('admin.rapor.show', array_replace($params, ['period_number' => 99])))->assertNotFound();
        $this->assertDatabaseCount('attendances', 3);
        $this->capture('annual-rapor', $list);
        $this->capture('admin-rapor-preview', $report);
    }

    public function test_report_filters_and_pagination_preserve_the_selected_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dewi = $this->teacher('Ms. Dewi');
        foreach (range(1, 16) as $index) {
            $child = $this->student($dewi, ['name' => 'Murid '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)]);
            $this->attendance($child, '2025-01-01', 'hadir');
        }
        $other = $this->student($this->teacher('Ms. Rina'), ['name' => 'Murid Rina']);
        $this->attendance($other, '2025-01-01', 'hadir');
        $params = ['year' => 2025, 'teacher_id' => $dewi->id, 'search' => 'Murid'];
        $response = $this->actingAs($admin)->get(route('admin.rapor', $params))->assertOk()->assertDontSee('Murid Rina');
        $paginator = $response->viewData('raporRows');
        $this->assertSame(16, $paginator->total());
        $this->assertSame(15, $paginator->count());
        $this->assertStringContainsString('year=2025', $paginator->nextPageUrl());
        $this->get($paginator->nextPageUrl())->assertOk()->assertViewHas('raporRows', fn ($rows) => $rows->count() === 1);
    }

    public function test_annual_period_selection_matches_existing_month_end_and_leap_year_periods(): void
    {
        $periods = app(ReportPeriodService::class);
        $teacher = $this->teacher();
        foreach (['2023-01-31', '2024-02-29', '2024-11-30', '2025-12-31'] as $joinDate) {
            $child = $this->student($teacher, ['join_date' => $joinDate]);
            foreach (range(2023, 2027) as $year) {
                $expected = array_values(array_filter($periods->options($child), fn ($period) => $period['end']->year === $year));
                $this->assertSame(array_column($expected, 'number'), array_column($periods->forYear($child, $year), 'number'));
            }
        }
    }

    public function test_removed_graph_page_redirects_and_admin_reports_remain_role_protected(): void
    {
        $teacher = $this->teacher();
        $child = $this->student($teacher);
        $this->actingAs($teacher->user)->get(route('guru.grafik'))->assertRedirect('/guru/nilai');
        $this->get(route('guru.dashboard'))->assertOk()->assertDontSee('Grafik Perkembangan')->assertDontSee('href="'.route('guru.grafik').'"', false);
        foreach (['admin.rapor', 'admin.rapor.show', 'admin.rapor.pdf'] as $route) {
            $this->get(route($route, ['student' => $child->id]))->assertForbidden();
        }
        $wali = User::factory()->create(['role' => 'wali']);
        $this->actingAs($wali)->get(route('admin.rapor'))->assertForbidden();
    }

    public function test_invalid_year_and_month_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->getJson(route('admin.absensi.report', ['year' => 2027]))->assertUnprocessable()->assertJsonValidationErrors('year');
        $this->getJson(route('admin.absensi.report', ['year' => 2025, 'report_month' => 13]))->assertUnprocessable()->assertJsonValidationErrors('report_month');
        $this->getJson(route('admin.rapor', ['year' => 'invalid']))->assertUnprocessable()->assertJsonValidationErrors('year');
    }

    private function teacher(string $name = 'Ms. Dewi'): Teacher
    {
        $user = User::factory()->create(['role' => 'guru', 'name' => $name]);

        return Teacher::create(['user_id' => $user->id, 'nip' => Teacher::generateNip(), 'status' => 'aktif']);
    }

    private function student(Teacher $teacher, array $attributes = []): Student
    {
        return Student::create($attributes + ['name' => 'Anak Uji', 'gender' => 'P', 'teacher_id' => $teacher->id, 'join_date' => '2024-11-01', 'status' => 'aktif']);
    }

    private function attendance(Student $student, string $date, string $status): void
    {
        Attendance::create(['student_id' => $student->id, 'attendance_date' => $date, 'status' => $status]);
    }

    private function capture(string $name, $response): void
    {
        if (getenv('RAPOR_VISUAL_QA')) {
            File::ensureDirectoryExists(storage_path('app/revision-qa'));
            File::put(storage_path('app/revision-qa/'.$name.'.html'), $response->getContent());
        }
    }
}
