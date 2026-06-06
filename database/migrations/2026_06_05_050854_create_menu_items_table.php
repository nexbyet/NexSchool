<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title_gu');
            $table->string('title_en')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('target')->default('_self');
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('menu_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
