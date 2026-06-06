<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_route', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stop_id')->nullable()->constrained('route_stops')->nullOnDelete();
            $table->boolean('pickup')->default(true);
            $table->boolean('drop')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_route');
    }
};
