<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->nullable()->constrained('fee_structures')->nullOnDelete();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('concession_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};
