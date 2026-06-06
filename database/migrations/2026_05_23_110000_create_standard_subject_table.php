<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standard_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['standard_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standard_subject');
    }
};
