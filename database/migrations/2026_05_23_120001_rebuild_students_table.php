<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('students');

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('gr_number', 20)->unique();
            $table->foreignId('admission_standard_id')->constrained('standards');
            $table->foreignId('current_standard_id')->constrained('standards');
            $table->date('date_of_admission');
            $table->string('student_name_gu');
            $table->string('student_name_en');
            $table->string('father_name_gu');
            $table->string('father_name_en');
            $table->string('surname_gu');
            $table->string('surname_en');
            $table->string('full_name_gu');
            $table->string('full_name_en');
            $table->string('mother_name_gu')->nullable();
            $table->string('mother_name_en')->nullable();
            $table->date('date_of_birth');
            $table->string('dob_in_text_gu')->nullable();
            $table->string('dob_in_text_en')->nullable();
            $table->string('birth_place_gu')->nullable();
            $table->string('birth_place_en')->nullable();
            $table->string('native_place_gu')->nullable();
            $table->string('native_place_en')->nullable();
            $table->string('religion')->nullable();
            $table->string('cast_gu')->nullable();
            $table->string('cast_en')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_minority')->nullable();
            $table->enum('sharirik_jaati', ['kumar', 'kumari'])->nullable();
            $table->string('last_school_gu')->nullable();
            $table->string('last_school_en')->nullable();
            $table->boolean('admission_under_rte')->default(false);
            $table->string('mobile', 10)->nullable();
            $table->string('whatsapp', 10)->nullable();
            $table->string('apaar_id', 12)->nullable();
            $table->string('uid_no', 18)->nullable();
            $table->string('pen_no', 11)->nullable();
            $table->string('aadhar_no', 12)->nullable();
            $table->string('name_as_per_aadhar')->nullable();
            $table->string('ration_card_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_ifsc')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('name_as_per_bank')->nullable();
            $table->text('leaving_reason_gu')->nullable();
            $table->text('leaving_reason_en')->nullable();
            $table->date('leaving_date')->nullable();
            $table->foreignId('leaving_standard_id')->nullable()->constrained('standards');
            $table->string('lc_number')->nullable();
            $table->text('leaving_remarks')->nullable();
            $table->enum('status', ['active', 'inactive', 'alumni'])->default('active');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });
        Schema::dropIfExists('students');
    }
};
