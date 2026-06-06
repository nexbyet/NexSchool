<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standards', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // ધોરણ નામ (Std 1, પ્રથમ ધોરણ, વર્ષ 1)
            $table->integer('sort_order')->default(0);       // ક્રમ (drag-drop rearrange)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standards');
    }
};
