<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Change status enum from ['aktif', 'cuti', 'nonaktif'] to ['aktif', 'lulus', 'pindah']
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'lulus', 'pindah'])->default('aktif')->after('classroom_id');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete()->after('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Teacher::class);
            $table->dropColumn('teacher_id');
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'cuti', 'nonaktif'])->default('aktif')->after('classroom_id');
        });
    }
};
