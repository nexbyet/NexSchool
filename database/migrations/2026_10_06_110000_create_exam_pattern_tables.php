<?php

// NexSchool - Exam Pattern Tables Migration (v2 — modular: std × class × subject)
// પેટર્ન: સત્ર-વાર હેડ (total marks + converted/result marks) + ક્યાં લાગુ (ધોરણ×વર્ગ×વિષય)
// NOTE: Never run migrate:fresh — use php artisan migrate only.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pattern header (per academic year — year badle to navu pattern/copy)
        Schema::create('exam_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Marks heads per semester.
        // total_marks = paper ketla marks nu | converted_marks = result ma ketlama ganvu (e.g. 80 -> 40)
        Schema::create('exam_pattern_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_pattern_id')->constrained('exam_patterns')->cascadeOnDelete();
            $table->tinyInteger('semester'); // 1 | 2
            $table->string('name');
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('converted_marks', 8, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['exam_pattern_id', 'semester']);
        });

        // 3. Pattern kya laghu: (standard × class × subject) rows.
        // Ek pattern anek dhoran/varg/vishay ma sarkhi rite lagu padi shake.
        Schema::create('exam_pattern_applies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_pattern_id')->constrained('exam_patterns')->cascadeOnDelete();
            $table->foreignId('standard_id')->constrained('standards')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['exam_pattern_id', 'standard_id', 'school_class_id', 'subject_id'], 'exam_pat_apply_unique');
            $table->index(['exam_pattern_id', 'standard_id'], 'exam_pat_apply_std_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_pattern_applies');
        Schema::dropIfExists('exam_pattern_heads');
        Schema::dropIfExists('exam_patterns');
    }
};
