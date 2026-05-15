<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Material;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HardcodedContentReplacementTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_dashboard_uses_real_notifications_for_recent_activity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Notification::send(
            $admin->id,
            'Guru Baru Ditambahkan',
            'Guru Rina berhasil ditambahkan ke sistem.',
            'success',
            'lucide:user-plus',
            route('admin.guru')
        );

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Guru Baru Ditambahkan')
            ->assertSee('Guru Rina berhasil ditambahkan ke sistem.')
            ->assertDontSee('Input Nilai Level 2 - P')
            ->assertDontSee('Tambah Murid Baru (M005)');
    }

    public function test_wali_dashboard_uses_student_progress_for_cards_and_chart(): void
    {
        Carbon::setTestNow('2026-05-15 08:00:00');

        $wali = User::factory()->create(['role' => 'wali']);
        $classroom = Classroom::create([
            'name' => 'Level 1 - A',
            'level' => 'Level 1',
            'capacity' => 20,
        ]);
        $student = Student::create([
            'nis' => 'BM001',
            'name' => 'Aisyah Test',
            'classroom_id' => $classroom->id,
            'parent_id' => $wali->id,
            'join_date' => '2026-01-01',
            'status' => 'aktif',
        ]);

        $baca = Material::create(['name' => 'Baca 1', 'skill_type' => 'baca', 'level' => 'Level 1', 'sort_order' => 1]);
        $tulis = Material::create(['name' => 'Tulis 1', 'skill_type' => 'tulis', 'level' => 'Level 1', 'sort_order' => 1]);
        $hitung = Material::create(['name' => 'Hitung 1', 'skill_type' => 'hitung', 'level' => 'Level 1', 'sort_order' => 1]);

        StudentProgress::create([
            'student_id' => $student->id,
            'material_id' => $baca->id,
            'start_date' => '2026-01-05',
            'understand_date' => '2026-03-10',
            'skilled_date' => '2026-05-01',
            'status' => 'T',
        ]);
        StudentProgress::create([
            'student_id' => $student->id,
            'material_id' => $tulis->id,
            'start_date' => '2026-02-01',
            'understand_date' => '2026-04-05',
            'status' => 'P',
        ]);
        StudentProgress::create([
            'student_id' => $student->id,
            'material_id' => $hitung->id,
            'status' => 'K',
        ]);

        $this->actingAs($wali)
            ->get(route('wali.dashboard'))
            ->assertOk()
            ->assertSee('Status: T')
            ->assertSee('1 dari 1 materi sudah terampil')
            ->assertSee('Status: P')
            ->assertSee('1 dari 1 materi sudah paham')
            ->assertSee('Status: K')
            ->assertSee('const waliDashboardChartData =', false)
            ->assertDontSee('data: [45, 55, 62, 70, 82]', false);
    }

    public function test_admin_can_update_landing_and_support_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.pengaturan.update'), [
            'institution_name' => 'BiMBA Unit Test',
            'institution_address' => 'Jl. Test No. 1',
            'unit_name' => 'Unit Test',
            'support_whatsapp' => '628111222333',
            'support_email' => 'support@test.local',
            'landing_badge' => 'Belajar Lebih Terpantau',
            'landing_title' => 'Rapor Digital Anak',
            'landing_highlight' => 'BiMBA Test',
            'landing_description' => 'Deskripsi landing dari pengaturan.',
            'landing_cta_title' => 'Mulai Pantau Belajar',
            'landing_cta_description' => 'CTA dari pengaturan.',
        ]);

        $response->assertRedirect(route('admin.pengaturan'));

        $this->assertSame('628111222333', Setting::get('support_whatsapp'));
        $this->assertSame('Rapor Digital Anak', Setting::get('landing_title'));

        $this->post(route('logout'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Kembali ke Beranda');

        $this->get('/')
            ->assertOk()
            ->assertSee('Rapor Digital Anak')
            ->assertSee('BiMBA Test')
            ->assertSee('Deskripsi landing dari pengaturan.')
            ->assertSee('support@test.local')
            ->assertSee('628111222333');
    }
}
