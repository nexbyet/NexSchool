<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('favicon')->nullable()->after('logo');
            $table->string('backend_favicon')->nullable()->after('favicon');
            $table->text('footer_credits')->nullable()->after('student_default_sort');
            $table->string('copyright_text')->nullable()->after('footer_credits');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['favicon', 'backend_favicon', 'footer_credits', 'copyright_text']);
        });
    }
};
