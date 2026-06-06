<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('receipt_number', 50)->unique();
            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method', 30)->default('cash'); // cash / bank / cheque / online
            $table->string('reference_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamps();

            $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
