<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name_en', 100);
            $table->string('name_gu', 100);
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_break')->default(false);
            $table->integer('sort_order')->default(0);
            $table->time('saturday_start_time')->nullable();
            $table->time('saturday_end_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};
