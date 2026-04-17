<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Material;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin User ─────────────────────────────────────
        User::create([
            'name'           => 'Admin BiMBA',
            'role'           => 'admin',
            'email'          => 'admin@bimba.id',
            'password'       => Hash::make('password'),
            'plain_password' => 'password',
        ]);

        // ─── Classrooms (Tahapan) ──────────────────────────
        $classrooms = [];
        $levels = [
            'Level 1' => ['Level 1'],
            'Level 2' => ['Level 2'],
            'Level 3' => ['Level 3'],
            'Level 4' => ['Level 4'],
        ];
        foreach ($levels as $level => $names) {
            foreach ($names as $name) {
                $classrooms[] = Classroom::create([
                    'name'     => $name,
                    'level'    => $level,
                    'capacity' => 20,
                ]);
            }
        }

        // ─── Teachers ──────────────────────────────────────
        $teacherData = [
            ['Siti Aminah',   'siti.a@bimba.id',  'T-001', 'Baca-Tulis',     'aktif'],
            ['Budi Santoso',  'budi.s@bimba.id',  'T-002', 'Matematika',     'aktif'],
            ['Larasati Putri','laras.p@bimba.id',  'T-003', 'Bahasa Inggris', 'cuti'],
            ['Andi Wijaya',   'andi.w@bimba.id',  'T-004', 'Seni & Kreasi',  'aktif'],
            ['Dewi Lestari',  'dewi.l@bimba.id',  'T-005', 'Baca-Tulis',     'aktif'],
        ];

        $teachers = [];
        foreach ($teacherData as $i => $td) {
            $user = User::create([
                'name'           => $td[0],
                'role'           => 'guru',
                'email'          => $td[1],
                'password'       => Hash::make('password'),
                'plain_password' => 'password',
            ]);
            $teacher = Teacher::create([
                'user_id'        => $user->id,
                'nip'            => $td[2],
                'specialization' => $td[3],
                'status'         => $td[4],
            ]);
            // Assign 2-4 classrooms each
            $assigned = collect($classrooms)->random(min(rand(2, 4), count($classrooms)));
            $teacher->classrooms()->attach($assigned->pluck('id'));
            $teachers[] = $teacher;
        }

        // ─── Parents & Students ────────────────────────────
        $studentData = [
            ['BM001', 'Aisyah Putri',    'Bpk. Budi',    'budi.wali@bimba.id',   0, '2024-01-12', 'aktif'],
            ['BM002', 'Bima Satria',      'Ibu Siti',     'siti.wali@bimba.id',   1, '2024-02-05', 'aktif'],
            ['BM003', 'Citra Kirana',     'Bpk. Agus',    'agus.wali@bimba.id',   2, '2024-02-20', 'cuti'],
            ['BM004', 'Dedi Kurniawan',   'Ibu Lani',     'lani.wali@bimba.id',   3, '2024-03-15', 'aktif'],
            ['BM005', 'Eka Prasetya',     'Bpk. Toto',    'toto.wali@bimba.id',   4, '2024-04-01', 'aktif'],
            ['BM006', 'Fira Amelia',      'Ibu Rina',     'rina.wali@bimba.id',   0, '2024-01-20', 'aktif'],
            ['BM007', 'Galih Permana',    'Bpk. Hendra',  'hendra.wali@bimba.id', 1, '2024-03-10', 'aktif'],
            ['BM008', 'Hana Safitri',     'Ibu Dewi',     'dewi.wali@bimba.id',   2, '2024-02-28', 'aktif'],
            ['BM009', 'Irfan Maulana',    'Bpk. Joko',    'joko.wali@bimba.id',   5, '2024-04-15', 'aktif'],
            ['BM010', 'Jasmine Zahra',    'Ibu Nita',     'nita.wali@bimba.id',   6, '2024-05-01', 'aktif'],
            ['BM011', 'Kemal Farhan',     'Bpk. Rudi',    'rudi.wali@bimba.id',   0, '2024-01-05', 'aktif'],
            ['BM012', 'Luna Maharani',    'Ibu Yanti',    'yanti.wali@bimba.id',  3, '2024-06-10', 'aktif'],
            ['BM013', 'Mahesa Putra',     'Bpk. Doni',    'doni.wali@bimba.id',   7, '2024-03-22', 'aktif'],
            ['BM014', 'Nabila Rahma',     'Ibu Fitri',    'fitri.wali@bimba.id',  4, '2024-07-01', 'cuti'],
            ['BM015', 'Omar Fariz',       'Bpk. Eko',     'eko.wali@bimba.id',    5, '2024-02-14', 'aktif'],
        ];

        $students = [];
        foreach ($studentData as $sd) {
            $parent = User::create([
                'name'           => $sd[2],
                'role'           => 'wali',
                'email'          => $sd[3],
                'password'       => Hash::make('password'),
                'plain_password' => 'password',
            ]);

            $classIdx = $sd[4] % count($classrooms);
            $students[] = Student::create([
                'nis'          => $sd[0],
                'name'         => $sd[1],
                'classroom_id' => $classrooms[$classIdx]->id,
                'parent_id'    => $parent->id,
                'join_date'    => $sd[5],
                'status'       => $sd[6],
                'report_token' => Str::random(40),
            ]);
        }

        // ─── Materials (Baca, Tulis, Hitung) ───────────────
        $materialsData = [
            // ── BACA Level 1 ──
            ['HV AIUEO',                'baca', 'Level 1', 1],
            ['4 HVK',                   'baca', 'Level 1', 2],
            ['HV BDG KMPSY',            'baca', 'Level 1', 3],
            ['SIMBOL AIUEO',            'baca', 'Level 1', 4],
            ['BACA 1A (BDG KMPSY)',     'baca', 'Level 1', 5],
            ['4 HVS',                   'baca', 'Level 1', 6],
            ['HV ABC',                  'baca', 'Level 1', 7],
            ['BACA 1B (JLN TRC)',       'baca', 'Level 1', 8],
            ['BACA 1C',                 'baca', 'Level 1', 9],
            ['5HVS+VERBAL -NG',         'baca', 'Level 1', 10],
            ['BACA 1D',                 'baca', 'Level 1', 11],
            ['BACA 2',                  'baca', 'Level 1', 12],
            // ── BACA Level 2 ──
            ['BACA 1E',                 'baca', 'Level 2', 1],
            ['BACA 1F',                 'baca', 'Level 2', 2],
            ['BACA 3',                  'baca', 'Level 2', 3],
            ['BACA 4',                  'baca', 'Level 2', 4],
            ['BACA 5',                  'baca', 'Level 2', 5],
            ['BACA 1G',                 'baca', 'Level 2', 6],
            ['BACA 1H',                 'baca', 'Level 2', 7],
            ['BACA 6',                  'baca', 'Level 2', 8],
            ['BACA 7',                  'baca', 'Level 2', 9],
            ['BACA 8',                  'baca', 'Level 2', 10],
            ['BACA 9',                  'baca', 'Level 2', 11],
            ['BACA 10',                 'baca', 'Level 2', 12],
            ['BACA 11',                 'baca', 'Level 2', 13],
            // ── BACA Level 3 ──
            ['BACA 12',                 'baca', 'Level 3', 1],
            ['HABBC',                   'baca', 'Level 3', 2],
            ['HABBK',                   'baca', 'Level 3', 3],
            // ── BACA Level 4 ──
            ['SB',                      'baca', 'Level 4', 1],
            ['SC',                      'baca', 'Level 4', 2],
            ['PG',                      'baca', 'Level 4', 3],
            // ── TULIS Level 1 ──
            ['MENCORET BEBAS',          'tulis', 'Level 1', 1],
            ['MEWARNAI HURUF',          'tulis', 'Level 1', 2],
            ['TULIS 1A',                'tulis', 'Level 1', 3],
            ['TULIS 1B (HV AIUEO)',     'tulis', 'Level 1', 4],
            ['TULIS 2',                 'tulis', 'Level 1', 5],
            ['TULIS 4',                 'tulis', 'Level 1', 6],
            ['TULIS 6',                 'tulis', 'Level 1', 7],
            ['DIKTE 1',                 'tulis', 'Level 1', 8],
            // ── TULIS Level 2 ──
            ['TULIS 3',                 'tulis', 'Level 2', 1],
            ['TULIS 5',                 'tulis', 'Level 2', 2],
            ['DIKTE 2',                 'tulis', 'Level 2', 3],
            ['TULIS 7',                 'tulis', 'Level 2', 4],
            ['DIKTE 3',                 'tulis', 'Level 2', 5],
            ['TULIS 8',                 'tulis', 'Level 2', 6],
            ['TULIS 9',                 'tulis', 'Level 2', 7],
            ['DIKTE 4',                 'tulis', 'Level 2', 8],
            ['TULIS 10',                'tulis', 'Level 2', 9],
            ['TULIS 11',                'tulis', 'Level 2', 10],
            ['DIKTE 5',                 'tulis', 'Level 2', 11],
            ['TULIS 12A',               'tulis', 'Level 2', 12],
            ['TULIS 12B',               'tulis', 'Level 2', 13],
            ['DIKTE 6',                 'tulis', 'Level 2', 14],
            // ── TULIS Level 3 ──
            ['TULIS SAMBUNG 1',         'tulis', 'Level 3', 1],
            ['TULIS SAMBUNG 2',         'tulis', 'Level 3', 2],
            ['TULIS SAMBUNG 3',         'tulis', 'Level 3', 3],
            ['TULIS SAMBUNG 4',         'tulis', 'Level 3', 4],
            // ── TULIS Level 4 ──
            ['LB',                      'tulis', 'Level 4', 1],
            ['NB',                      'tulis', 'Level 4', 2],
            ['NT',                      'tulis', 'Level 4', 3],
            ['MBAP',                    'tulis', 'Level 4', 4],
            // ── MATEMATIKA Level 1 ──
            ['MTK 1A',                  'hitung', 'Level 1', 1],
            ['MTK 1B',                  'hitung', 'Level 1', 2],
            ['MTK 2A',                  'hitung', 'Level 1', 3],
            ['MTK 2B',                  'hitung', 'Level 1', 4],
            ['MTK 3A',                  'hitung', 'Level 1', 5],
            ['MTK 3B',                  'hitung', 'Level 1', 6],
            // ── MATEMATIKA Level 2 ──
            ['MTK 4A',                  'hitung', 'Level 2', 1],
            ['MTK 4B',                  'hitung', 'Level 2', 2],
            ['MTK 6A',                  'hitung', 'Level 2', 3],
            ['MTK 6B',                  'hitung', 'Level 2', 4],
            ['MTK 5A',                  'hitung', 'Level 2', 5],
            ['MTK 5B',                  'hitung', 'Level 2', 6],
            // ── MATEMATIKA Level 3 ──
            ['MTK 7A',                  'hitung', 'Level 3', 1],
            ['MTK 7B',                  'hitung', 'Level 3', 2],
            // ── MATEMATIKA Level 4 ──
            ['PJTM',                    'hitung', 'Level 4', 1],
            ['PBDM',                    'hitung', 'Level 4', 2],
            ['PJDM',                    'hitung', 'Level 4', 3],
            ['PGTM',                    'hitung', 'Level 4', 4],
            ['PGDM',                    'hitung', 'Level 4', 5],
        ];

        $materials = [];
        foreach ($materialsData as $md) {
            $materials[] = Material::create([
                'name'       => $md[0],
                'skill_type' => $md[1],
                'level'      => $md[2],
                'sort_order' => $md[3],
            ]);
        }

        // ─── Student Progress (sample data) ────────────────
        $statuses = ['K', 'B', 'P', 'T'];
        $level1Materials = collect($materials)->filter(fn ($m) => $m->level === 'Level 1');

        foreach ($students as $student) {
            foreach ($level1Materials as $material) {
                // Random progress for each material
                $statusIdx = rand(0, 3);
                $startDate = $student->join_date->copy()->addDays(rand(0, 30));
                $understandDate = $statusIdx >= 2 ? $startDate->copy()->addDays(rand(5, 20)) : null;
                $skilledDate = $statusIdx >= 3 ? ($understandDate?->copy()->addDays(rand(3, 15))) : null;

                StudentProgress::create([
                    'student_id'      => $student->id,
                    'material_id'     => $material->id,
                    'teacher_id'      => $teachers[array_rand($teachers)]->id,
                    'start_date'      => $statusIdx >= 1 ? $startDate : null,
                    'understand_date' => $understandDate,
                    'skilled_date'    => $skilledDate,
                    'status'          => $statuses[$statusIdx],
                ]);
            }
        }
    }
}
