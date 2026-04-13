<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                    // e.g. "Pengenalan Huruf Vokal"
            $table->enum('skill_type', ['baca', 'tulis', 'hitung']);   // 3 skill areas
            $table->string('level');                                    // Level 1, 2, 3, 4
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
