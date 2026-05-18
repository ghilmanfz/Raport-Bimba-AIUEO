<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('phone');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('father_phone', 20)->nullable()->after('mother_name');
            $table->string('mother_phone', 20)->nullable()->after('father_phone');
            $table->text('address')->nullable()->after('mother_phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'mother_name', 'father_phone', 'mother_phone', 'address']);
        });
    }
};
