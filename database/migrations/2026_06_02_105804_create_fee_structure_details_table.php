<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structure_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
            $table->foreignId('fee_head_id')->constrained('fee_heads')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['fee_structure_id', 'fee_head_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structure_details');
    }
};
