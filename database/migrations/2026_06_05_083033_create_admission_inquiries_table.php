<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('student_name_gu');
            $table->string('student_name_en')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->string('standard_applied_for');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('gr_number')->nullable()->unique();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_inquiries');
    }
};
