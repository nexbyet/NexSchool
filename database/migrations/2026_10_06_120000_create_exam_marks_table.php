<?php

// NexSchool - Exam Marks Entry Table Migration
// ગુણ એન્ટ્રી: (pattern × year × std × class × subject × semester × head × student) દીઠ 1 row
// Cell khali hoy to row j nahi (delete) — etale "not entered" state saf rahe.
// NOTE: Never run migrate:fresh — use php artisan migrate only.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_pattern_id')->constrained('exam_patterns')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('standard_id')->constrained('standards')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->tinyInteger('semester'); // 1 | 2
            $table->foreignId('head_id')->constrained('exam_pattern_heads')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('marks_obtained', 8, 2);
            $table->timestamps();

            $table->unique(
                ['exam_pattern_id', 'student_id', 'subject_id', 'semester', 'head_id'],
                'exam_marks_unique'
            );
            $table->index(['exam_pattern_id', 'standard_id', 'school_class_id', 'subject_id', 'semester'], 'exam_marks_grid_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};
