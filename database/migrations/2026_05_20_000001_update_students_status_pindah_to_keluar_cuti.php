<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand ENUM to include both old ('pindah') and new ('keluar', 'cuti') values
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('aktif', 'lulus', 'pindah', 'keluar', 'cuti') NOT NULL DEFAULT 'aktif'");

        // Step 2: Migrate existing 'pindah' rows to 'keluar'
        DB::table('students')->where('status', 'pindah')->update(['status' => 'keluar']);

        // Step 3: Remove 'pindah' from the ENUM now that no rows use it
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('aktif', 'lulus', 'keluar', 'cuti') NOT NULL DEFAULT 'aktif'");
    }

    public function down(): void
    {
        // Revert 'keluar' back to 'pindah' (cuti has no old equivalent, will be set to 'pindah')
        DB::table('students')->where('status', 'keluar')->update(['status' => 'pindah']);
        DB::table('students')->where('status', 'cuti')->update(['status' => 'pindah']);

        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('aktif', 'lulus', 'pindah') NOT NULL DEFAULT 'aktif'");
    }
};
