<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->index('academic_year_id', 'fee_structures_academic_year_id_index');
            $table->dropUnique(['academic_year_id', 'standard_id', 'type']);
            $table->unique(['academic_year_id', 'standard_id', 'type', 'semester'], 'fee_structures_year_std_type_sem_unique');
        });
    }

    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropUnique('fee_structures_year_std_type_sem_unique');
            $table->unique(['academic_year_id', 'standard_id', 'type']);
            $table->dropIndex('fee_structures_academic_year_id_index');
        });
    }
};
