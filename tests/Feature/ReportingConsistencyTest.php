<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Material;
use App\Models\Notification;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\ReportPeriodService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ReportingConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(CarbonImmutable::parse('2026-12-11 09:00:00', 'Asia/Jakarta'));
    }

    public function test_mid_month_join_has_exactly_three_learning_months_and_inclusive_attendance(): void
    {
        [$guru, $wali, $student] = $this->family('2026-09-11');
        foreach (['baca', 'baca', 'tulis', 'tulis', 'hitung', 'hitung'] as $skill) {
            $grade = $this->progress($student, ['skilled_date' => '2026-09-12', 'status' => 'T']);
            $grade->material->update(['skill_type' => $skill]);
        }
        foreach (['2026-09-10', '2026-09-11', '2026-10-10', '2026-10-11', '2026-11-10', '2026-11-11', '2026-12-10', '2026-12-11'] as $date) {
            Attendance::create(['student_id' => $student->id, 'attendance_date' => $date, 'status' => 'hadir']);
        }
        $period = app(ReportPeriodService::class)->forNumber($student, 1);
        $report = app(AttendanceService::class)->forReport($student, $period);
        $this->assertSame(['Bulan ke-1', 'Bulan ke-2', 'Bulan ke-3'], array_column($report['months'], 'label'));
        $this->assertSame(['2026-10-10', '2026-11-10', '2026-12-10'], array_map(fn ($month) => $month['end']->toDateString(), $report['months']));
        $this->assertSame([2, 2, 2], array_column(array_column($report['months'], 'summary'), 'total'));
        $this->assertSame(6, $report['summary']['total']);

        $params = ['student_id' => $student->id, 'period_number' => 1, 'period_end' => '2026-12-10'];
        $teacher = $this->actingAs($guru)->get(route('guru.rapor', $params))->assertOk();
        $parent = $this->actingAs($wali)->get(route('wali.rapor.periode', $params))->assertOk();
        foreach ([$teacher, $parent] as $response) {
            $response->assertSee('Bulan ke-3')->assertDontSee('Bulan ke-4');
            $this->assertSame(6, $response->viewData('attendanceReport')['summary']['total']);
        }
        $pdf = $this->get(route('rapor.download', ['token' => $student->report_token, 'period_number' => 1, 'period_end' => '2026-12-10']))->assertOk()->assertHeader('content-type', 'application/pdf');

        if (getenv('RAPOR_VISUAL_QA')) {
            File::ensureDirectoryExists(storage_path('app/rapor-qa'));
            File::put(storage_path('app/rapor-qa/learning-months.html'), $teacher->getContent());
            File::put(storage_path('app/rapor-qa/learning-months.pdf'), $pdf->getContent());
        }
    }

    public function test_month_end_and_leap_year_periods_never_lose_or_double_count_a_day(): void
    {
        foreach (['2026-01-31', '2023-11-30', '2024-02-29', '2026-08-31'] as $join) {
            $student = new Student(['join_date' => $join]);
            $previousEnd = null;
            for ($number = 1; $number <= 5; $number++) {
                $period = app(ReportPeriodService::class)->forNumber($student, $number);
                $this->assertCount(3, $period['months']);
                foreach ($period['months'] as $month) {
                    if ($previousEnd) {
                        $this->assertSame($previousEnd->addDay()->toDateString(), $month['start']->toDateString());
                    }
                    $this->assertTrue($month['end']->gte($month['start']));
                    $previousEnd = $month['end'];
                }
                $this->assertSame($previousEnd->toDateString(), $period['end']->toDateString());
                $this->assertSame($previousEnd->addDay()->toDateString(), $period['due_date']->toDateString());
            }
        }
    }

    public function test_legacy_empty_k_rows_do_not_count_in_graphs_and_are_not_deleted(): void
    {
        [$guru, $wali, $student] = $this->family();
        for ($index = 0; $index < 26; $index++) {
            $this->progress($student, $index < 6 ? ['skilled_date' => '2026-09-12', 'status' => 'K'] : ['status' => 'K']);
        }
        $response = $this->actingAs($guru)->get(route('guru.grafik'))->assertOk();
        $this->assertSame(['T' => 6, 'P' => 0, 'K' => 0], $response->viewData('statusCounts'));
        $this->assertSame(6, $response->viewData('totalProgress'));
        $this->assertEquals(100, $response->viewData('statusPercent')['T']);
        $this->assertEquals(100, $response->viewData('studentStats')[0]['progress_pct']);
        $this->assertSame(100.0, $student->skillPercentage('baca'));
        $this->assertDatabaseCount('student_progress', 26);
        $dashboard = $this->get(route('guru.dashboard'))->assertOk();
        $this->assertSame(['T' => 6, 'P' => 0, 'K' => 0], $dashboard->viewData('stats')['status_counts']);
        $this->assertSame(0, $dashboard->viewData('stats')['perlu_perhatian']);
        $parent = $this->actingAs($wali)->get(route('wali.dashboard'))->assertOk();
        $this->assertSame('T', $parent->viewData('skillCards')['baca']['status']);
        $this->assertNull($parent->viewData('skillCards')['tulis']['status']);
        $this->assertDatabaseCount('student_progress', 26);

        if (getenv('RAPOR_VISUAL_QA')) {
            File::ensureDirectoryExists(storage_path('app/rapor-qa'));
            File::put(storage_path('app/rapor-qa/graph.html'), $response->getContent());
        }
    }

    public function test_saving_t_only_does_not_create_k_for_blank_materials_or_touch_other_values(): void
    {
        [$guru, , $student] = $this->family();
        $skilled = $this->progress($student, ['skilled_date' => '2026-09-12', 'status' => 'T']);
        $untouched = $this->progress($student, ['understand_date' => '2026-09-12', 'status' => 'P']);
        $blank = Material::create(['name' => 'Modul belum diisi', 'skill_type' => 'baca', 'level' => 'Level 1', 'sort_order' => 99]);
        $this->actingAs($guru)->post(route('guru.nilai.store'), [
            'student_id' => $student->id,
            'progress' => [
                ['material_id' => $skilled->material_id, 'skilled_date' => '2026-09-13'],
                ['material_id' => $blank->id, 'start_date' => null, 'understand_date' => null, 'skilled_date' => null],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseCount('student_progress', 2);
        $this->assertSame('T', $skilled->fresh()->display_status);
        $this->assertSame('P', $untouched->fresh()->display_status);
        $this->assertDatabaseMissing('student_progress', ['student_id' => $student->id, 'material_id' => $blank->id]);
        $this->post(route('guru.nilai.store'), ['student_id' => $student->id, 'progress' => [['material_id' => $skilled->material_id]]])->assertRedirect();
        $this->assertDatabaseMissing('student_progress', ['id' => $skilled->id]);
        $this->assertDatabaseHas('student_progress', ['id' => $untouched->id, 'status' => 'P']);
    }

    public function test_unassessed_student_is_not_labelled_kenal_and_search_filters_the_whole_chart(): void
    {
        [$guru, , $empty] = $this->family();
        $this->progress($empty, ['status' => 'K']);
        $other = Student::create(['nis' => 'OTHER', 'name' => 'Lain', 'gender' => 'L', 'status' => 'aktif', 'teacher_id' => $empty->teacher_id, 'join_date' => '2026-09-11']);
        $this->progress($other, ['start_date' => '2026-09-12', 'status' => 'K']);
        $response = $this->actingAs($guru)->get(route('guru.grafik', ['search' => $empty->name]))->assertOk()->assertSee('Belum Dinilai');
        $this->assertSame(0, $response->viewData('totalProgress'));
        $this->assertNull($response->viewData('studentStats')[0]['latest_status']);
        $this->assertNull($response->viewData('studentStats')[0]['progress_pct']);
        $full = $this->get(route('guru.grafik'))->assertOk();
        $this->assertSame(['T' => 0, 'P' => 0, 'K' => 1], $full->viewData('statusCounts'));
    }

    public function test_reminders_use_the_assigned_teacher_are_idempotent_and_link_the_completed_period(): void
    {
        [$guru, $wali, $student] = $this->family();
        $admin = $this->user('admin');
        $unrelatedGuru = $this->user('guru');
        $unrelatedTeacher = Teacher::create(['user_id' => $unrelatedGuru->id, 'nip' => 'OTHER', 'status' => 'aktif']);
        $class = Classroom::create(['name' => 'Kelas Uji', 'level' => 'Level 1', 'capacity' => 20]);
        $class->teachers()->attach($unrelatedTeacher);
        $student->update(['classroom_id' => $class->id]);
        $this->travelTo(CarbonImmutable::parse('2026-12-10 09:00:00'));
        $this->artisan('rapor:remind')->assertSuccessful();
        $this->assertDatabaseCount('notifications', 0);
        $this->travelTo(CarbonImmutable::parse('2026-12-11 09:00:00'));
        $this->artisan('rapor:remind', ['--dry-run' => true])->assertSuccessful();
        $this->assertDatabaseCount('notifications', 0);
        $this->artisan('rapor:remind')->assertSuccessful();
        $this->artisan('rapor:remind')->assertSuccessful();
        $this->assertDatabaseCount('notifications', 3);
        $this->assertEqualsCanonicalizing([$guru->id, $wali->id, $admin->id], Notification::pluck('user_id')->all());
        $notification = Notification::where('user_id', $guru->id)->firstOrFail();
        $this->assertStringContainsString('period_number=1', $notification->link);
        $this->assertStringContainsString('period_end=2026-12-10', $notification->link);
        $report = $this->actingAs($guru)->get($notification->link)->assertOk();
        $this->assertSame(1, $report->viewData('attendanceReport')['period']['number']);
        $this->actingAs($wali)->get(Notification::where('user_id', $wali->id)->firstOrFail()->link)->assertOk();
    }

    public function test_dashboard_and_reminder_agree_on_due_day_and_end_of_month(): void
    {
        [$guru, $wali, $student] = $this->family('2023-11-30');
        $this->travelTo(CarbonImmutable::parse('2024-02-29 09:00:00'));
        $period = app(ReportPeriodService::class)->nextDistribution($student);
        $this->assertSame(1, $period['number']);
        $this->assertSame('2024-02-29', $period['due_date']->toDateString());
        $teacher = $this->actingAs($guru)->get(route('guru.dashboard'))->assertOk();
        $parent = $this->actingAs($wali)->get(route('wali.dashboard'))->assertOk();
        $this->assertSame(1, $teacher->viewData('nextRaporSchedules')[0]['period_number']);
        $this->assertSame(1, $parent->viewData('raporSchedules')[0]['period_number']);
        $this->artisan('rapor:remind')->assertSuccessful();
        $this->assertDatabaseCount('notifications', 2);
        $this->travelTo(CarbonImmutable::parse('2024-03-01 09:00:00'));
        $this->assertSame('2024-05-30', app(ReportPeriodService::class)->nextDistribution($student)['due_date']->toDateString());
        $this->artisan('rapor:remind')->assertSuccessful();
        $this->assertDatabaseCount('notifications', 2);
    }

    public function test_scheduler_is_registered_once_daily_in_jakarta_time(): void
    {
        $events = collect(app(Schedule::class)->events())->filter(fn ($event) => str_contains($event->command ?? '', 'rapor:remind'));
        $this->assertCount(1, $events);
        $this->assertSame('0 9 * * *', $events->first()->expression);
        $this->assertSame('Asia/Jakarta', $events->first()->timezone);
    }

    public function test_historical_report_does_not_pull_milestones_from_the_next_period(): void
    {
        [$guru, , $student] = $this->family();
        $grade = $this->progress($student, ['start_date' => '2026-09-11', 'skilled_date' => '2026-12-11', 'status' => 'T']);
        $params = ['student_id' => $student->id, 'period_number' => 1, 'period_end' => '2026-12-10'];
        $past = $this->actingAs($guru)->get(route('guru.rapor', $params))->assertOk();
        $this->assertSame('K', $past->viewData('reportData')['baca']['details']->first()->display_status);
        $this->assertEquals(0, $past->viewData('reportData')['baca']['percentage']);
        $current = $this->get(route('guru.rapor', ['student_id' => $student->id]))->assertOk();
        $this->assertSame('T', $current->viewData('reportData')['baca']['details']->first()->display_status);
        $this->assertSame('T', $grade->fresh()->status);
        $this->get(route('guru.rapor', array_replace($params, ['period_end' => '2026-12-11'])))->assertStatus(422);
    }

    public function test_reminders_skip_inactive_students_and_future_students(): void
    {
        [, , $student] = $this->family();
        $student->update(['status' => 'lulus']);
        $this->artisan('rapor:remind')->assertSuccessful();
        $this->assertDatabaseCount('notifications', 0);
        $student->update(['status' => 'aktif', 'join_date' => '2027-09-11']);
        $this->artisan('rapor:remind')->assertSuccessful();
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_teachers_cannot_read_or_replace_another_teachers_assessment(): void
    {
        [$owner, , $student] = $this->family();
        [$other] = $this->family();
        $grade = $this->progress($student, ['skilled_date' => '2026-09-12', 'status' => 'T']);
        $response = $this->actingAs($other)->get(route('guru.nilai', ['student_id' => $student->id]))->assertOk();
        $this->assertNull($response->viewData('selectedStudent'));
        $this->assertSame([], $response->viewData('progress'));
        $this->post(route('guru.nilai.store'), [
            'student_id' => $student->id,
            'progress' => [['material_id' => $grade->material_id]],
        ])->assertSessionHasErrors('student_id');
        $this->assertSame('T', $grade->fresh()->display_status);
        $this->actingAs($owner)->get(route('guru.nilai', ['student_id' => $student->id]))->assertOk();
    }

    private function user(string $role): User
    {
        $this->sequence++;

        return User::create(['name' => ucfirst($role).' Uji '.$this->sequence, 'email' => 'user'.$this->sequence.'@example.test', 'role' => $role, 'password' => 'password']);
    }

    private function family(string $join = '2026-09-11'): array
    {
        $guru = $this->user('guru');
        $wali = $this->user('wali');
        $teacher = Teacher::create(['user_id' => $guru->id, 'nip' => 'T'.$this->sequence, 'status' => 'aktif']);
        $student = Student::create(['nis' => 'ST'.$this->sequence, 'name' => 'Mawar Contoh', 'gender' => 'P', 'join_date' => $join, 'status' => 'aktif', 'teacher_id' => $teacher->id, 'parent_id' => $wali->id]);

        return [$guru, $wali, $student];
    }

    private function progress(Student $student, array $dates): StudentProgress
    {
        $this->sequence++;
        $material = Material::create(['name' => 'Modul '.$this->sequence, 'skill_type' => 'baca', 'level' => 'Level 1', 'sort_order' => $this->sequence]);

        return StudentProgress::create(['student_id' => $student->id, 'material_id' => $material->id, 'teacher_id' => $student->teacher_id] + $dates);
    }
}
