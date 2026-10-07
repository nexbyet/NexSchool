<?php

// NexSchool - Grade Scale Tables Migration
// ગ્રેડ વ્યવસ્થાપન: 100 માંથી slabs (grade + min% + max% + pass/fail).
// Result phase ma: percent = melvel/total*100 -> aa slabs thi grade lookup thashe.
// NOTE: Never run migrate:fresh — use php artisan migrate only.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Scale header (per academic year — year badle to navo scale/copy)
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name'); // e.g. મુખ્ય ગ્રેડ સ્કેલ
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Slabs: grade + percent range + pass/fail
        Schema::create('grade_slabs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_scale_id')->constrained('grade_scales')->cascadeOnDelete();
            $table->string('grade', 10); // e.g. A1, B2, E2
            $table->decimal('min_percent', 5, 2); // 0.00 - 100.00
            $table->decimal('max_percent', 5, 2);
            $table->boolean('is_pass')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['grade_scale_id', 'sort_order'], 'grade_slab_scale_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_slabs');
        Schema::dropIfExists('grade_scales');
    }
};
