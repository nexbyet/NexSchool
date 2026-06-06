<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('standard_id')->nullable()->constrained('standards')->nullOnDelete();
            $table->string('frequency', 20)->default('monthly'); // monthly / semesterly / yearly
            $table->string('late_fee_type', 20)->default('none'); // none / fixed / per_month
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->integer('late_fee_after_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['academic_year_id', 'standard_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
