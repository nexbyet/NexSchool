<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notice_board', function (Blueprint $table) {
            $table->id();
            $table->string('title_gu');
            $table->string('title_en')->nullable();
            $table->text('content_gu')->nullable();
            $table->text('content_en')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_circular')->default(false);
            $table->date('date');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_board');
    }
};
