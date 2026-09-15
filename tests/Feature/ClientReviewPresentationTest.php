<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Material;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ClientReviewPresentationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(CarbonImmutable::parse('2026-09-15 10:00:00', 'Asia/Jakarta'));
    }

    public function test_removed_class_filters_never_hide_assigned_students_from_old_links(): void
    {
        [$guru, , $first, $second] = $this->family();
        $this->grade($first, 'T');
        $this->grade($second, 'P');
        $outsider = Student::create(['name' => 'Bukan Bimbingan', 'nis' => 'LUAR', 'gender' => 'L', 'status' => 'aktif', 'join_date' => '2026-09-11', 'classroom_id' => $first->classroom_id]);
        $this->grade($outsider, 'K');
        Student::create(['name' => 'Siswa Lulus', 'nis' => 'LULUS', 'gender' => 'L', 'status' => 'lulus', 'join_date' => '2026-09-11', 'teacher_id' => $first->teacher_id]);

        foreach ([$first->classroom_id, 'kelas-lama-tidak-valid'] as $legacyClass) {
            $daily = $this->actingAs($guru)->get(route('guru.absensi.index', ['classroom_id' => $legacyClass]))->assertOk();
            $graph = $this->get(route('guru.grafik', ['classroom_id' => $legacyClass]))->assertOk();
            foreach ([$daily, $graph] as $response) {
                $this->assertEqualsCanonicalizing([$first->id, $second->id], $response->viewData('students')->modelKeys());
                $response->assertDontSee('name="classroom_id"', false)->assertDontSee('Semua Kelas')->assertDontSee('Bukan Bimbingan')->assertDontSee('Siswa Lulus');
            }
            $this->assertSame(2, $daily->viewData('dailyStats')['unrecorded']);
            $this->assertSame(['T' => 1, 'P' => 1, 'K' => 0], $graph->viewData('statusCounts'));
            $this->assertSame(0, $this->xpath($graph->getContent())->query('//th[normalize-space()="Kelas"]')->length);
            $this->assertSame(1, $this->xpath($graph->getContent())->query('//tbody//p[normalize-space()="Level 1"]')->length);
        }

        $this->savePreview('teacher-attendance.html', $daily->getContent());
        $this->savePreview('teacher-graph.html', $graph->getContent());
    }

    public function test_search_by_name_or_nis_still_filters_attendance_and_graph_totals(): void
    {
        [$guru, , $first, $second] = $this->family();
        $this->grade($first, 'T');
        $this->grade($second, 'P');
        foreach ([$second->name, $second->nis] as $search) {
            $params = ['search' => $search, 'classroom_id' => $first->classroom_id];
            $daily = $this->actingAs($guru)->get(route('guru.absensi.index', $params))->assertOk();
            $graph = $this->get(route('guru.grafik', $params))->assertOk();
            $this->assertSame([$second->id], $daily->viewData('students')->modelKeys());
            $this->assertSame([$second->id], $graph->viewData('students')->modelKeys());
            $this->assertSame(['T' => 0, 'P' => 1, 'K' => 0], $graph->viewData('statusCounts'));
        }
        $empty = $this->get(route('guru.grafik', ['search' => 'tidak-ditemukan']))->assertOk()->assertSee('Tidak ada siswa ditemukan.');
        $this->assertSame(0, $empty->viewData('totalProgress'));
        $this->assertSame('4', $this->xpath($empty->getContent())->query('//tbody/tr/td')->item(0)->getAttribute('colspan'));
    }

    public function test_saving_attendance_ignores_old_class_filter_but_preserves_class_records(): void
    {
        [$guru, , $first, $second] = $this->family();
        $payload = ['attendance_date' => '2026-09-15', 'classroom_id' => 'kelas-lama-tidak-valid', 'search' => '', 'attendances' => [
            ['student_id' => $first->id, 'status' => 'hadir'],
            ['student_id' => $second->id, 'status' => 'izin'],
        ]];
        $this->actingAs($guru)->post(route('guru.absensi.store'), $payload)
            ->assertSessionHasNoErrors()->assertRedirect(route('guru.absensi.index', ['attendance_date' => '2026-09-15']));
        foreach ([$first, $second] as $student) {
            $this->assertDatabaseHas('attendances', ['student_id' => $student->id, 'classroom_id' => $student->classroom_id]);
            $this->assertDatabaseHas('classrooms', ['id' => $student->classroom_id]);
        }
        $admin = User::create(['name' => 'Admin Uji', 'email' => 'admin-review@example.test', 'password' => 'password', 'role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.absensi.store'), $payload)->assertSessionHasErrors('classroom_id');
        $this->assertDatabaseCount('attendances', 2);
    }

    public function test_parent_summary_keeps_printable_identity_without_attendance_percentage(): void
    {
        [, $wali, $first, $second] = $this->family();
        foreach (['2026-09-11' => 'hadir', '2026-09-12' => 'hadir', '2026-09-15' => 'sakit'] as $date => $status) {
            Attendance::create(['student_id' => $first->id, 'classroom_id' => $first->classroom_id, 'attendance_date' => $date, 'status' => $status]);
        }
        $response = $this->actingAs($wali)->get(route('wali.absensi.index', ['student_id' => $first->id, 'month' => '2026-09']))
            ->assertOk()->assertSee('Kehadiran Anak')->assertSee('Riwayat Kehadiran')->assertDontSee('Hadir dari Tercatat')->assertDontSee('Rasio hadir');
        $header = $this->xpath($response->getContent())->query('//header[contains(@class,"attendance-identity")]')->item(0);
        $this->assertNotNull($header);
        foreach ([$first->name, $first->nis, 'September 2026'] as $identity) {
            $this->assertStringContainsString($identity, $header->textContent);
        }
        $this->assertStringNotContainsString('%', $header->textContent);
        $this->assertStringNotContainsString('hero-gradient', $header->getAttribute('class'));
        $this->assertStringNotContainsString('no-print', $header->getAttribute('class'));
        $this->assertSame(3, $response->viewData('summary')['total']);
        $this->assertSame(2, $response->viewData('summary')['hadir']);
        $this->savePreview('parent-attendance.html', $response->getContent());

        $empty = $this->get(route('wali.absensi.index', ['student_id' => $second->id, 'month' => '2026-08']))->assertOk()->assertSee('Belum ada absensi tercatat pada Agustus 2026.');
        $emptyHeader = $this->xpath($empty->getContent())->query('//header[contains(@class,"attendance-identity")]')->item(0);
        $this->assertStringContainsString($second->name, $emptyHeader->textContent);
        $this->assertStringContainsString('Agustus 2026', $emptyHeader->textContent);
    }

    public function test_teacher_parent_history_and_pdf_have_level_without_duplicate_class_identity(): void
    {
        [$guru, $wali, $student] = $this->family();
        foreach (['baca', 'tulis', 'hitung'] as $skill) {
            $this->grade($student, 'T', $skill);
        }
        $params = ['student_id' => $student->id, 'period_number' => 1, 'period_end' => '2026-09-15'];
        $teacher = $this->actingAs($guru)->get(route('guru.rapor', $params))->assertOk();
        $parent = $this->actingAs($wali)->get(route('wali.rapor', $params))->assertOk();
        $history = $this->get(route('wali.rapor.periode', $params))->assertOk();
        $pdfHtml = view('rapor.pdf', array_merge($teacher->original->getData(), ['teacherName' => $guru->name]))->render();
        foreach ([$teacher->getContent(), $parent->getContent(), $history->getContent(), $pdfHtml] as $html) {
            $xpath = $this->xpath($html);
            $table = $xpath->query('//table[@class="rapor-header-table" or @class="identity-table"]')->item(0);
            $this->assertNotNull($table);
            $this->assertSame(0, $xpath->query('.//td[normalize-space()="Kelas"]', $table)->length);
            $this->assertSame(1, $xpath->query('.//td[normalize-space()="Level"]', $table)->length);
            $this->assertSame(1, $xpath->query('.//td[normalize-space()="Level 1"]', $table)->length);
            $this->assertStringContainsString($student->nis, $table->textContent);
        }
        $pdf = $this->get(route('rapor.download', ['token' => $student->report_token, 'period_number' => 1, 'period_end' => '2026-09-15']))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertDatabaseHas('students', ['id' => $student->id, 'classroom_id' => $student->classroom_id]);
        $this->assertDatabaseCount('student_progress', 3);
        $this->savePreview('teacher-report.html', $teacher->getContent());
        $this->savePreview('report.pdf', $pdf->getContent());
    }

    public function test_teacher_navigation_and_operational_labels_use_siswa(): void
    {
        [$guru] = $this->family();
        foreach (['guru.dashboard', 'guru.murid', 'guru.absensi.index', 'guru.nilai', 'guru.grafik', 'guru.rapor'] as $route) {
            $this->actingAs($guru)->get(route($route))->assertOk()
                ->assertSee('Absensi Siswa')->assertSee('Daftar Siswa Bimbingan')
                ->assertDontSee('Absensi Anak')->assertDontSee('Cari Anak')->assertDontSee('Pilih Murid');
        }
    }

    private function family(): array
    {
        $guru = User::create(['name' => 'Guru Uji', 'email' => 'guru-review@example.test', 'password' => 'password', 'role' => 'guru']);
        $wali = User::create(['name' => 'Wali Uji', 'email' => 'wali-review@example.test', 'password' => 'password', 'role' => 'wali']);
        $teacher = Teacher::create(['user_id' => $guru->id, 'nip' => 'REVIEW-01', 'status' => 'aktif']);
        $students = [];
        foreach (['Nadia Contoh', 'Rafi Contoh'] as $index => $name) {
            $class = Classroom::create(['name' => 'Kelompok '.($index + 1), 'level' => 'Level '.($index + 1), 'capacity' => 20]);
            $students[] = Student::create(['name' => $name, 'nis' => 'UJI-'.($index + 1), 'gender' => 'P', 'join_date' => '2026-09-11', 'status' => 'aktif', 'teacher_id' => $teacher->id, 'parent_id' => $wali->id, 'classroom_id' => $class->id]);
        }

        return [$guru, $wali, ...$students];
    }

    private function grade(Student $student, string $status, string $skill = 'baca'): void
    {
        $material = Material::create(['name' => 'Modul Uji '.strtoupper($skill), 'level' => $student->classroom?->level ?? 'Level 1', 'skill_type' => $skill, 'sort_order' => 1]);
        $field = ['T' => 'skilled_date', 'P' => 'understand_date', 'K' => 'start_date'][$status];
        StudentProgress::create(['student_id' => $student->id, 'material_id' => $material->id, 'teacher_id' => $student->teacher_id, 'status' => $status, $field => '2026-09-12']);
    }

    private function xpath(string $html): \DOMXPath
    {
        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new \DOMXPath($document);
    }

    private function savePreview(string $filename, string $content): void
    {
        if (getenv('CLIENT_REVIEW_VISUAL_QA')) {
            File::ensureDirectoryExists(storage_path('app/client-review-qa'));
            File::put(storage_path('app/client-review-qa/'.$filename), $content);
        }
    }
}
