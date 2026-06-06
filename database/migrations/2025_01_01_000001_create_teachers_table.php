<?php

// NexSchool - Teachers Table Migration
// શિક્ષકોની માહિતી: લાયકાત, વિશેષતા, જોડાવાની તારીખ

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('qualification')->nullable();    // લાયકાત (B.Ed, M.Sc, etc.)
            $table->string('specialization')->nullable();   // વિશેષતા (Maths, Science, etc.)
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('joining_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
