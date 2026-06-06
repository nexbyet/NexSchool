<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
