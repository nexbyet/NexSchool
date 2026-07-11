<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_only_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_only_student_id')->constrained('bus_only_students')->onDelete('cascade');
            $table->tinyInteger('semester');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank', 'cheque', 'online'])->default('cash');
            $table->string('reference_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_only_fee_payments');
    }
};
