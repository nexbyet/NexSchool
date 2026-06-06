<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fee_structure_standard', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
            $table->foreignId('standard_id')->constrained('standards')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['fee_structure_id', 'standard_id']);
        });

        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropUnique('fee_structures_year_std_type_sem_unique');
            $table->dropIndex('fee_structures_standard_id_foreign');
            $table->dropColumn('standard_id');
            $table->index(['academic_year_id', 'type', 'semester'], 'fee_structures_year_type_sem_index');
            $table->unique(['academic_year_id', 'type', 'semester'], 'fee_structures_year_type_sem_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_structure_standard');

        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropIndex('fee_structures_year_type_sem_index');
            $table->dropUnique('fee_structures_year_type_sem_unique');
            $table->foreignId('standard_id')->nullable()->constrained('standards')->cascadeOnDelete();
            $table->unique(['academic_year_id', 'standard_id', 'type', 'semester'], 'fee_structures_year_std_type_sem_unique');
        });
    }
};
