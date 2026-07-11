<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_only_students', function (Blueprint $table) {
            $table->id();
            $table->string('full_name_gu');
            $table->string('standard_label', 50)->nullable();
            $table->string('gaam', 100)->nullable();
            $table->string('mobile', 10)->nullable();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->decimal('fee_sem1', 10, 2)->default(0);
            $table->decimal('fee_sem2', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_only_students');
    }
};
