<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->string('activity_name');
            $table->date('date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_plans');
    }
};
