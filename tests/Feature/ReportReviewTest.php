<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Material;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\ReportPeriodService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ReportReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 9, 11)->setTime(10, 0));
    }

    public function test_report_counts_only_saved_attendance_in_the_students_three_month_period(): void
    {
        [, , $student] = $this->family();
        foreach (['2026-06-30' => 'hadir', '2026-07-01' => 'hadir', '2026-07-31' => 'sakit', '2026-08-01' => 'izin', '2026-09-11' => 'alpa', '2026-09-12' => 'hadir', '2026-10-01' => 'hadir'] as $date => $status) {
            $this->attendance($student, $date, $status);
        }
        $report = app(AttendanceService::class)->forReport($student);

        $this->assertCount(3, $report['months']);
        $this->assertSame('2026-07-01', $report['period']['start']->toDateString());
        $this->assertSame('2026-09-30', $report['period']['end']->toDateString());
        $this->assertSame('2026-09-11', $report['period']['cutoff']->toDateString());
        $this->assertSame([2, 1, 1], array_column(array_column($report['months'], 'summary'), 'total'));
        $this->assertSame(['hadir' => 1, 'sakit' => 1, 'izin' => 1, 'alpa' => 1, 'total' => 4, 'percentage' => 25.0], $report['summary']);
    }

    public function test_three_month_periods_have_no_gaps_or_overlaps_at_month_ends(): void
    {
        $student = new Student(['join_date' => '2026-01-31']);
        $periods = app(ReportPeriodService::class);
        $first = $periods->forNumber($student, 1);
        $second = $periods->forNumber($student, 2);
        $this->assertSame('2026-04-29', $first['end']->toDateString());
        $this->assertSame('2026-04-30', $second['start']->toDateString());
        $this->assertSame('2026-07-30', $second['end']->toDateString());
        $this->travelTo(now()->setDate(2026, 7, 30));
        $this->assertSame(2, $periods->current($student)['number']);
        $this->travelTo(now()->setDate(2026, 7, 31));
        $this->assertSame(3, $periods->current($student)['number']);
    }

    public function test_empty_and_future_months_do_not_become_alpa(): void
    {
        [, , $student] = $this->family();
        $student->update(['join_date' => '2026-09-01']);
        $report = app(AttendanceService::class)->forReport($student);
        $this->assertSame(0, $report['summary']['alpa']);
        $this->assertSame(0, $report['summary']['total']);
        $this->assertSame([false, true, true], array_column($report['months'], 'future'));
        $this->assertStringContainsString('Belum ada absensi tercatat', view('rapor.attendance', ['attendanceReport' => $report])->render());
    }

    public function test_teacher_parent_and_pdf_share_attendance_without_changing_graph_values(): void
    {
        [$guru, $wali, $student] = $this->family();
        $this->attendance($student, '2026-07-01', 'hadir');
        $this->attendance($student, '2026-08-01', 'sakit');
        $this->attendance($student, '2026-09-01', 'izin');
        foreach (['baca', 'tulis', 'hitung'] as $skill) {
            $material = Material::create(['name' => 'Materi '.$skill, 'skill_type' => $skill, 'level' => 'Level 1', 'sort_order' => 1]);
            StudentProgress::create(['student_id' => $student->id, 'material_id' => $material->id, 'teacher_id' => $student->teacher_id, 'start_date' => '2026-07-01', 'skilled_date' => '2026-08-01', 'status' => 'T']);
        }

        $teacherResponse = $this->actingAs($guru)->get(route('guru.rapor', ['student_id' => $student->id]))->assertOk();
        $parentResponse = $this->actingAs($wali)->get(route('wali.rapor', ['student_id' => $student->id]))->assertOk();
        foreach ([$teacherResponse, $parentResponse] as $response) {
            $response->assertSee('Hasil Absensi per Bulan')->assertSee('Total Periode 3 Bulan')->assertSee('Catatan khusus dari guru.')->assertDontSee('Aspek terkuat')->assertDontSee('Rata-Rata Penguasaan');
            $this->assertSame(3, $response->viewData('attendanceReport')['summary']['total']);
            $this->assertSame(100.0, $response->viewData('reportData')['baca']['percentage']);
            $this->assertStringContainsString('data: [100, 100, 100]', $response->getContent());
            preg_match('/<table class="rapor-table">(.*?)<\/table>/s', $response->getContent(), $grades);
            $this->assertStringNotContainsString('%', strip_tags($grades[1]));
        }
        $this->actingAs($guru)->get(route('guru.absensi.index'))->assertOk()->assertSee('Lihat Absensi di Rapor');
        $pdf = $this->get(route('rapor.download', $student->report_token))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());

        // Optional visual QA output contains only these synthetic test records.
        if (getenv('RAPOR_VISUAL_QA')) {
            $directory = storage_path('app/rapor-qa');
            File::ensureDirectoryExists($directory);
            File::put($directory.'/guru.html', $teacherResponse->getContent());
            File::put($directory.'/wali.html', $parentResponse->getContent());
            File::put($directory.'/rapor.pdf', $pdf->getContent());
        }
    }

    public function test_historical_attendance_is_bounded_and_cannot_access_another_child(): void
    {
        [, $wali, $student] = $this->family();
        $student->update(['join_date' => '2026-04-01']);
        $this->attendance($student, '2026-06-30', 'hadir');
        $this->attendance($student, '2026-07-01', 'sakit');
        $params = ['student_id' => $student->id, 'period_number' => 1, 'period_end' => '2026-06-30'];
        $response = $this->actingAs($wali)->get(route('wali.rapor.periode', $params))->assertOk()->assertSee('Hasil Absensi per Bulan')->assertDontSee('Aspek terkuat');
        $this->assertSame(1, $response->viewData('attendanceReport')['summary']['total']);
        $this->assertSame(0, $response->viewData('attendanceReport')['summary']['sakit']);
        $this->get(route('wali.rapor.periode', array_replace($params, ['period_end' => '2026-07-01'])))->assertStatus(422);
        $this->get(route('wali.rapor.periode', array_replace($params, ['period_number' => 999999])))->assertNotFound();
        $this->get(route('wali.riwayat', ['student_id' => $student->id]))->assertOk();
        $otherParent = $this->user('wali', 'other@example.test');
        $this->actingAs($otherParent)->get(route('wali.rapor.periode', $params))->assertNotFound();
    }

    public function test_admin_class_management_is_removed_but_existing_class_data_remains(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $classroom = Classroom::create(['name' => 'Level 1', 'level' => 'Level 1', 'capacity' => 20]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertDontSee('Data Kelas');
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            $this->call($method, '/admin/kelas'.(in_array($method, ['PUT', 'DELETE']) ? '/'.$classroom->id : ''))->assertNotFound();
        }
        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id]);
    }

    public function test_support_whatsapp_can_be_configured_normalized_and_cleared(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Atur Nomor WhatsApp')->assertSee('/admin/pengaturan#support_whatsapp', false);
        $this->get(route('admin.pengaturan'))->assertOk()->assertSee('Nomor WhatsApp Pusat Bantuan');
        $settings = ['institution_name' => 'BiMBA Uji', 'institution_address' => 'Alamat Uji', 'support_whatsapp' => '0812-3456-7890'];
        $this->put(route('admin.pengaturan.update'), $settings)->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('6281234567890', Setting::get('support_whatsapp'));
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('https://wa.me/6281234567890', false);
        $this->get(route('admin.guru'))->assertOk()->assertSee('https://wa.me/6281234567890', false);
        $this->get('/')->assertOk()->assertSee('https://wa.me/6281234567890', false);
        $this->put(route('admin.pengaturan.update'), array_replace($settings, ['support_whatsapp' => 'abc123']))->assertSessionHasErrors('support_whatsapp');
        $this->assertSame('6281234567890', Setting::get('support_whatsapp'));
        $this->put(route('admin.pengaturan.update'), array_replace($settings, ['support_whatsapp' => '']))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNull(Setting::supportWhatsappUrl());
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Atur Nomor WhatsApp');
        $this->assertSame('6281234567890', Setting::normalizeWhatsapp('+62 812 3456 7890'));
    }

    private function family(): array
    {
        $guru = $this->user('guru', 'guru@example.test');
        $wali = $this->user('wali', 'wali@example.test');
        $teacher = Teacher::create(['user_id' => $guru->id, 'nip' => 'T-01', 'status' => 'aktif']);
        $student = Student::create(['nis' => 'BM-UJI', 'name' => 'Murid Contoh', 'gender' => 'P', 'teacher_id' => $teacher->id, 'parent_id' => $wali->id, 'join_date' => '2026-07-01', 'status' => 'aktif', 'development_notes' => 'Catatan khusus dari guru.']);

        return [$guru, $wali, $student];
    }

    private function user(string $role, string $email): User
    {
        return User::create(['name' => ucfirst($role).' Contoh', 'role' => $role, 'email' => $email, 'password' => 'password']);
    }

    private function attendance(Student $student, string $date, string $status): void
    {
        Attendance::create(['student_id' => $student->id, 'attendance_date' => $date, 'status' => $status]);
    }
}
