<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->boolean('is_waived')->default(false)->after('net_amount');
            $table->json('excluded_fee_heads')->nullable()->after('is_waived');
        });
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropColumn(['is_waived', 'excluded_fee_heads']);
        });
    }
};
