<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['school_class_id', 'academic_year_id'], 'ct_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_teachers');
    }
};
