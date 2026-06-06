<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->foreignId('student_fee_id')->nullable()->after('id')->constrained('student_fees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropForeign(['student_fee_id']);
            $table->dropColumn('student_fee_id');
        });
    }
};
