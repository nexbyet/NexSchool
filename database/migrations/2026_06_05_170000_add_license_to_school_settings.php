<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->text('license_key')->nullable()->after('copyright_text');
            $table->string('licensee_name')->nullable()->after('license_key');
            $table->date('licensed_until')->nullable()->after('licensee_name');
            $table->string('license_status')->default('unlicensed')->after('licensed_until');
            $table->timestamp('last_license_ping')->nullable()->after('license_status');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['license_key', 'licensee_name', 'licensed_until', 'license_status', 'last_license_ping']);
        });
    }
};
