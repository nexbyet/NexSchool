<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->date('session_start_date')->nullable()->after('end_date');
        });
    }

    public function down()
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn('session_start_date');
        });
    }
};
