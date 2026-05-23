<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep current values before recreating enum column (works for MySQL & SQLite).
        Schema::table('students', function (Blueprint $table) {
            $table->string('status_tmp')->nullable()->after('status');
        });

        DB::table('students')->update(['status_tmp' => DB::raw('status')]);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'lulus', 'keluar', 'cuti'])->default('aktif')->after('classroom_id');
        });

        DB::table('students')->where('status_tmp', 'aktif')->update(['status' => 'aktif']);
        DB::table('students')->where('status_tmp', 'lulus')->update(['status' => 'lulus']);
        DB::table('students')->where('status_tmp', 'keluar')->update(['status' => 'keluar']);
        DB::table('students')->where('status_tmp', 'cuti')->update(['status' => 'cuti']);
        DB::table('students')->where('status_tmp', 'pindah')->update(['status' => 'keluar']);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status_tmp');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('status_tmp')->nullable()->after('status');
        });

        DB::table('students')->update(['status_tmp' => DB::raw('status')]);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'lulus', 'pindah'])->default('aktif')->after('classroom_id');
        });

        DB::table('students')->where('status_tmp', 'aktif')->update(['status' => 'aktif']);
        DB::table('students')->where('status_tmp', 'lulus')->update(['status' => 'lulus']);
        DB::table('students')->where('status_tmp', 'pindah')->update(['status' => 'pindah']);
        DB::table('students')->where('status_tmp', 'keluar')->update(['status' => 'pindah']);
        DB::table('students')->where('status_tmp', 'cuti')->update(['status' => 'pindah']);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status_tmp');
        });
    }
};
