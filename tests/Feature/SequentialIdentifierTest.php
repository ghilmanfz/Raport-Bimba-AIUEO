<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequentialIdentifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_nis_continues_after_three_digit_boundary(): void
    {
        Student::create([
            'nis' => 'BM099',
            'name' => 'Murid 99',
            'join_date' => '2026-01-01',
            'status' => 'aktif',
        ]);

        Student::create([
            'nis' => 'BM100',
            'name' => 'Murid 100',
            'join_date' => '2026-01-02',
            'status' => 'aktif',
        ]);

        $this->assertSame('BM101', Student::generateNis());
    }

    public function test_student_nis_ignores_non_bm_identifiers(): void
    {
        Student::create([
            'nis' => 'BM010',
            'name' => 'Murid Valid',
            'join_date' => '2026-01-01',
            'status' => 'aktif',
        ]);

        Student::create([
            'nis' => 'XX999',
            'name' => 'Murid Tidak Valid',
            'join_date' => '2026-01-02',
            'status' => 'aktif',
        ]);

        $this->assertSame('BM011', Student::generateNis());
    }

    public function test_teacher_nip_ignores_non_teacher_identifiers(): void
    {
        $teacherUser = User::factory()->create(['role' => 'guru']);
        $otherUser = User::factory()->create(['role' => 'guru']);

        Teacher::create([
            'user_id' => $teacherUser->id,
            'nip' => 'T-010',
            'status' => 'aktif',
        ]);

        Teacher::create([
            'user_id' => $otherUser->id,
            'nip' => 'XX999',
            'status' => 'aktif',
        ]);

        $this->assertSame('T-011', Teacher::generateNip());
    }
}
