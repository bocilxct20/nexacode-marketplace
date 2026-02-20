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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->unique(); // Midtrans transaction ID
            $table->string('payment_type')->nullable(); // credit_card, bank_transfer, gopay, etc
            $table->decimal('gross_amount', 15, 2);
            $table->string('status')->default('pending'); // pending, settlement, expire, cancel, deny
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->string('fraud_status')->nullable();
            $table->string('bank')->nullable();
            $table->string('va_number')->nullable(); // Virtual Account number
            $table->string('bill_key')->nullable();
            $table->string('biller_code')->nullable();
            $table->json('metadata')->nullable(); // Store full Midtrans response
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
