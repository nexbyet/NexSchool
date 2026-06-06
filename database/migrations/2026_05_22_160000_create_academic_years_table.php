<?php

// NexSchool - Academic Years Migration
// શૈક્ષણિક વર્ષો: 2025-26, 2026-27...
// is_active: only one year can be active at a time

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('year', 20);                              // શૈક્ષણિક વર્ષ (2025-26)
            $table->date('start_date');                               // શરૂઆત તારીખ
            $table->date('end_date');                                 // સમાપ્તિ તારીખ
            $table->boolean('is_active')->default(false);             // સક્રિય વર્ષ (only one)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
