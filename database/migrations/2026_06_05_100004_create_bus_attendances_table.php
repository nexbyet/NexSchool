<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('morning_status', ['present', 'absent', 'leave'])->nullable();
            $table->enum('evening_status', ['present', 'absent', 'leave'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'route_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_attendances');
    }
};
