<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('updated_from')->nullable();
            $table->text('file_manifest')->nullable();
            $table->string('status')->default('current');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
