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
            'name'     => 'Admin BiMBA',
            'role'     => 'admin',
            'email'    => 'admin@bimba.id',
            'password' => Hash::make('password'),
        ]);

        // ─── Classrooms ────────────────────────────────────
        $classrooms = [];
        $levels = [
            'Level 1' => ['Level 1 - Persiapan A', 'Level 1 - Persiapan B', 'Level 1 - Persiapan C'],
            'Level 2' => ['Level 2 - Dasar A', 'Level 2 - Dasar B'],
            'Level 3' => ['Level 3 - Lanjutan A'],
            'Level 4' => ['Level 4 - Mahir A', 'Level 4 - Mahir B'],
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
                'name'     => $td[0],
                'role'     => 'guru',
                'email'    => $td[1],
                'password' => Hash::make('password'),
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
                'name'     => $sd[2],
                'role'     => 'wali',
                'email'    => $sd[3],
                'password' => Hash::make('password'),
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
            // Baca - Level 1
            ['Pengenalan Huruf Vokal (A-I-U-E-O)',     'baca',   'Level 1', 1],
            ['Pengenalan Huruf Konsonan B-D-G-K',      'baca',   'Level 1', 2],
            ['Membaca Suku Kata Terbuka (Ba, Bi, Bu)',  'baca',   'Level 1', 3],
            ['Pengenalan Kata Bermakna 4 Huruf',        'baca',   'Level 1', 4],
            ['Kalimat Sederhana (3 Kata)',              'baca',   'Level 1', 5],
            // Baca - Level 2
            ['Membaca Kata dengan Huruf Mati',          'baca',   'Level 2', 1],
            ['Membaca Kalimat Pendek',                  'baca',   'Level 2', 2],
            ['Membaca Paragraf Sederhana',              'baca',   'Level 2', 3],
            // Tulis - Level 1
            ['Menelusur Garis Dasar',                   'tulis',  'Level 1', 1],
            ['Menulis Huruf Vokal',                     'tulis',  'Level 1', 2],
            ['Menulis Huruf Konsonan',                  'tulis',  'Level 1', 3],
            ['Merangkai Suku Kata',                     'tulis',  'Level 1', 4],
            ['Menulis Kata Sederhana',                  'tulis',  'Level 1', 5],
            // Tulis - Level 2
            ['Menulis Kalimat Pendek',                  'tulis',  'Level 2', 1],
            ['Menulis Nama Sendiri',                    'tulis',  'Level 2', 2],
            // Hitung - Level 1
            ['Pengenalan Angka 1-10',                   'hitung', 'Level 1', 1],
            ['Menghitung Benda 1-10',                   'hitung', 'Level 1', 2],
            ['Pengenalan Angka 11-20',                  'hitung', 'Level 1', 3],
            ['Penjumlahan Sederhana 1-5',               'hitung', 'Level 1', 4],
            ['Pengurangan Sederhana 1-5',               'hitung', 'Level 1', 5],
            // Hitung - Level 2
            ['Penjumlahan 1-20',                        'hitung', 'Level 2', 1],
            ['Pengurangan 1-20',                        'hitung', 'Level 2', 2],
            ['Pengenalan Bentuk Geometri',              'hitung', 'Level 2', 3],
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
