<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_carry_forwards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('from_academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('to_academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_carry_forwards');
    }
};
