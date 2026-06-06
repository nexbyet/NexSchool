<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('standard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['subject_id', 'standard_id', 'class_id', 'academic_year_id'], 'sta_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_teacher_assignments');
    }
};
