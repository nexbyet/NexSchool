<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('gaam', 255)->nullable()->after('native_place_en');
            $table->string('gaam_en', 255)->nullable()->after('gaam');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['gaam', 'gaam_en']);
        });
    }
};
