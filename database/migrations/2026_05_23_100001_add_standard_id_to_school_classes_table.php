<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->foreignId('standard_id')
                  ->nullable()
                  ->constrained('standards')
                  ->onDelete('cascade');
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropForeign(['standard_id']);
            $table->dropColumn('standard_id');
            $table->dropColumn('sort_order');
        });
    }
};
