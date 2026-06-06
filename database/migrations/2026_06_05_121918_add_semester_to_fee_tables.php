<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->tinyInteger('semester')->nullable()->comment('1=Semester 1, 2=Semester 2, NULL=Annual');
        });

        Schema::table('student_fees', function (Blueprint $table) {
            $table->tinyInteger('semester')->nullable()->comment('1=Semester 1, 2=Semester 2, NULL=Annual');
        });

        Schema::table('fee_payments', function (Blueprint $table) {
            $table->tinyInteger('semester')->nullable()->comment('1=Semester 1, 2=Semester 2, NULL=Annual');
        });
    }

    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn('semester');
        });

        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropColumn('semester');
        });

        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
