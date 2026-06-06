<?php

// NexSchool - Students Table Migration
// વિદ્યાર્થીઓ: નામ, સંપર્ક માહિતી, વાલી વિગત, વર્ગ
// Foreign key: class_id → school_classes.id

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('admission_date')->nullable();
            $table->foreignId('class_id')                    // વિદ્યાર્થીનો વર્ગ
                  ->nullable()
                  ->constrained('school_classes')
                  ->onDelete('set null');
            $table->string('guardian_name')->nullable();     // વાલીનું નામ
            $table->string('guardian_phone')->nullable();    // વાલીનો ફોન
            $table->enum('status', ['active', 'inactive', 'alumni'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
