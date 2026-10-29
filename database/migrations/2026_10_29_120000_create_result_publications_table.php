<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('standard_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->cascadeOnDelete();
            $table->enum('semester', ['1', '2', 'both'])->default('both');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'standard_id', 'school_class_id', 'semester'], 'result_pub_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_publications');
    }
};