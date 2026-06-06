<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['academic_year_id']);
        });
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'academic_year_id']);
            $table->unique(['student_id', 'academic_year_id', 'fee_structure_id']);
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['academic_year_id']);
        });
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'academic_year_id', 'fee_structure_id']);
            $table->unique(['student_id', 'academic_year_id']);
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
        });
    }
};
