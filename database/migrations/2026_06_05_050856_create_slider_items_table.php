<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_items', function (Blueprint $table) {
            $table->id();
            $table->string('title_gu');
            $table->string('title_en')->nullable();
            $table->string('subtitle_gu')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_items');
    }
};
