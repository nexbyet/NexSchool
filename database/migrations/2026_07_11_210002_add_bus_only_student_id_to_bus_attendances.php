<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bus_attendances', function (Blueprint $table) {
            $table->foreignId('bus_only_student_id')->nullable()->constrained('bus_only_students')->onDelete('cascade')->after('student_id');
            $table->string('student_type', 20)->default('regular')->after('bus_only_student_id');
        });
    }

    public function down(): void
    {
        Schema::table('bus_attendances', function (Blueprint $table) {
            $table->dropColumn(['bus_only_student_id', 'student_type']);
        });
    }
};
