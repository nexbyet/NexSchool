<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_inquiries', function (Blueprint $table) {
            $table->dropColumn(['student_name_gu', 'student_name_en']);
        });

        Schema::table('admission_inquiries', function (Blueprint $table) {
            $table->string('first_name_gu')->after('id');
            $table->string('father_name_gu')->after('first_name_gu');
            $table->string('surname_gu')->nullable()->after('father_name_gu');
            $table->string('first_name_en')->nullable()->after('surname_gu');
            $table->string('father_name_en')->nullable()->after('first_name_en');
            $table->string('surname_en')->nullable()->after('father_name_en');
        });

        DB::statement("ALTER TABLE admission_inquiries MODIFY COLUMN gender ENUM('kumar', 'kumari') NOT NULL DEFAULT 'kumar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE admission_inquiries MODIFY COLUMN gender ENUM('male', 'female', 'other') NOT NULL DEFAULT 'male'");

        Schema::table('admission_inquiries', function (Blueprint $table) {
            $table->dropColumn(['first_name_gu', 'father_name_gu', 'surname_gu', 'first_name_en', 'father_name_en', 'surname_en']);
        });

        Schema::table('admission_inquiries', function (Blueprint $table) {
            $table->string('student_name_gu')->after('id');
            $table->string('student_name_en')->nullable()->after('student_name_gu');
        });
    }
};
