<?php

// NexSchool - School Classes Table Migration
// વર્ગો: ધોરણ, વિભાગ, વર્ગશિક્ષક (teacher_id), ક્ષમતા
// Foreign key: teacher_id → teachers.id

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // ધોરણ નામ (Std 1, 2, etc.)
            $table->string('section')->nullable();           // વિભાગ (A, B, C)
            $table->string('room_number')->nullable();
            $table->foreignId('teacher_id')                  // વર્ગશિક્ષક
                  ->nullable()
                  ->constrained('teachers')
                  ->onDelete('set null');
            $table->string('academic_year')->nullable();
            $table->integer('capacity')->nullable();         // મહત્તમ વિદ્યાર્થી સંખ્યા
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
