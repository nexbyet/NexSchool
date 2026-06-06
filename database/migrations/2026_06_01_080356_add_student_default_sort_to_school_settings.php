<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->json('student_default_sort')->nullable()->after('logo');
        });

        // Set default sort: Name (English) → Father's Name (English) → Surname (English)
        DB::table('school_settings')->where('id', 1)->update([
            'student_default_sort' => json_encode(['name_en', 'father_name_en', 'surname_en']),
        ]);
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn('student_default_sort');
        });
    }
};
