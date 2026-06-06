<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'absent_with_leave', 'medical_leave']);
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
