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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('payment_method')->constrained('payment_methods')->onDelete('set null');
            $table->string('payment_proof')->nullable()->after('payment_method_id');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
            $table->text('qris_dynamic')->nullable()->after('payment_proof_uploaded_at');
            $table->timestamp('expires_at')->nullable()->after('qris_dynamic');
            $table->timestamp('paid_at')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['payment_method_id', 'payment_proof', 'payment_proof_uploaded_at', 'qris_dynamic', 'expires_at', 'paid_at']);
        });
    }
};
