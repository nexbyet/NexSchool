<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['jaher', 'sthanik'])->default('jaher');
            $table->date('date');
            $table->timestamps();

            $table->unique(['academic_year_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_holidays');
    }
};
