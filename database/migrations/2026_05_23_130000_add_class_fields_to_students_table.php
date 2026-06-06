<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('admission_class_id')->nullable()->after('admission_standard_id')->constrained('school_classes')->nullOnDelete();
            $table->foreignId('current_class_id')->nullable()->after('current_standard_id')->constrained('school_classes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['admission_class_id']);
            $table->dropForeign(['current_class_id']);
            $table->dropColumn(['admission_class_id', 'current_class_id']);
        });
    }
};
