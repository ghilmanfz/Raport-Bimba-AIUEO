<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('report_token', 64)->unique()->nullable()->after('photo');
        });

        // Generate tokens for existing students
        foreach (\App\Models\Student::all() as $student) {
            $student->update(['report_token' => Str::random(40)]);
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('report_token');
        });
    }
};
