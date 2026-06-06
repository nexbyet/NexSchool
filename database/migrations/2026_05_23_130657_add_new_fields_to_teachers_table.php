<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('teacher_id', 20)->unique()->after('id');
            $table->string('whatsapp_number', 20)->nullable()->after('phone');
            $table->string('joining_number', 50)->nullable()->after('joining_date');
            $table->unsignedTinyInteger('experience_in_years')->nullable()->after('joining_number');
            $table->string('blood_group', 10)->nullable()->after('experience_in_years');
            $table->decimal('basic_pay', 10, 2)->nullable()->after('blood_group');
            $table->unsignedSmallInteger('max_lwp')->nullable()->after('basic_pay');
            $table->unsignedSmallInteger('max_cl')->nullable()->after('max_lwp');
            $table->text('ratings')->nullable()->after('max_cl');
            $table->decimal('basic_salary', 10, 2)->nullable()->after('ratings');
            $table->decimal('other_salary', 10, 2)->nullable()->after('basic_salary');
            $table->text('reason_inactive')->nullable()->after('status');
            $table->date('date_inactive')->nullable()->after('reason_inactive');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'teacher_id', 'whatsapp_number', 'joining_number',
                'experience_in_years', 'blood_group', 'basic_pay',
                'max_lwp', 'max_cl', 'ratings', 'basic_salary',
                'other_salary', 'reason_inactive', 'date_inactive',
            ]);
        });
    }
};
