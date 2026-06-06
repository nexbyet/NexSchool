<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
        });
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropUnique(['academic_year_id', 'standard_id']);
            $table->string('type', 30)->default('tuition')->after('standard_id');
            $table->unique(['academic_year_id', 'standard_id', 'type']);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
        });
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropUnique(['academic_year_id', 'standard_id', 'type']);
            $table->dropColumn('type');
            $table->unique(['academic_year_id', 'standard_id']);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
        });
    }
};
