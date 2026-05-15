<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMuridControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_murid_form_shows_wali_account_options(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $wali = User::factory()->create([
            'role' => 'wali',
            'name' => 'Ibu Siti',
            'email' => 'siti@example.test',
        ]);
        $classroom = $this->createClassroom();

        Student::create([
            'nis' => 'BM001',
            'name' => 'Anak Pertama',
            'classroom_id' => $classroom->id,
            'parent_id' => $wali->id,
            'join_date' => '2026-05-01',
            'status' => 'aktif',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.murid'))
            ->assertOk()
            ->assertSee('Tanpa wali dulu')
            ->assertSee('Pilih wali yang sudah ada')
            ->assertSee('Buat akun wali baru')
            ->assertSee('Cari nama atau email wali')
            ->assertSee('Ibu Siti')
            ->assertSee('1 anak');
    }

    public function test_admin_can_connect_new_student_to_existing_wali_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $wali = User::factory()->create([
            'role' => 'wali',
            'name' => 'Ibu Siti',
            'email' => 'siti@example.test',
        ]);
        $classroom = $this->createClassroom();

        $response = $this->actingAs($admin)->post(route('admin.murid.store'), [
            'name' => 'Anak Kedua',
            'classroom_id' => $classroom->id,
            'join_date' => '2026-05-15',
            'status' => 'aktif',
            'parent_mode' => 'existing',
            'existing_parent_id' => $wali->id,
        ]);

        $response->assertRedirect(route('admin.murid'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'name' => 'Anak Kedua',
            'parent_id' => $wali->id,
        ]);
        $this->assertSame(1, User::where('email', 'siti@example.test')->count());
    }

    public function test_admin_can_create_student_with_new_wali_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $classroom = $this->createClassroom();

        $response = $this->actingAs($admin)->post(route('admin.murid.store'), [
            'name' => 'Anak Baru',
            'classroom_id' => $classroom->id,
            'join_date' => '2026-05-15',
            'status' => 'aktif',
            'parent_mode' => 'new',
            'parent_name' => 'Ibu Rina',
            'parent_email' => 'rina@example.test',
            'parent_password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('admin.murid'));
        $response->assertSessionHasNoErrors();

        $wali = User::where('email', 'rina@example.test')->firstOrFail();

        $this->assertSame('wali', $wali->role);
        $this->assertSame('rahasia123', $wali->plain_password);
        $this->assertTrue(Hash::check('rahasia123', $wali->password));
        $this->assertDatabaseHas('students', [
            'name' => 'Anak Baru',
            'parent_id' => $wali->id,
        ]);
    }

    private function createClassroom(): Classroom
    {
        return Classroom::create([
            'name' => 'Level 1 - A',
            'level' => 'Level 1',
            'capacity' => 20,
        ]);
    }
}
