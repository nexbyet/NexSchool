<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('timetable_slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('standard_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1=Monday, 6=Saturday
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['academic_year_id', 'timetable_slot_id', 'standard_id', 'school_class_id', 'day_of_week'], 'timetable_entry_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
