<?php

// NexSchool - Subjects + subject_teacher (pivot) Table Migration
// વિષયો: નામ, કોડ, પાસ/ટોટલ માર્ક
// Pivot: subject_teacher - Many-to-Many relationship between subjects & teachers

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');                              // વિષયનું નામ
            $table->string('code')->nullable()->unique();        // વિષય કોડ (MTH01, SCI02)
            $table->text('description')->nullable();
            $table->foreignId('class_id')                        // કયા વર્ગ માટે?
                  ->nullable()
                  ->constrained('school_classes')
                  ->onDelete('set null');
            $table->integer('credit_hours')->nullable();         // ક્રેડિટ કલાકો
            $table->integer('pass_mark')->nullable();            // પાસ થવા માટે માર્ક
            $table->integer('total_mark')->nullable();           // કુલ માર્ક
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Pivot table: કયો શિક્ષક કયો વિષય ભણાવે છે
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_teacher');
        Schema::dropIfExists('subjects');
    }
};
