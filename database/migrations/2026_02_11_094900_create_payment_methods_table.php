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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['bank_transfer', 'qris', 'ewallet'])->default('bank_transfer');
            $table->string('name'); // BCA, Mandiri, BRI, QRIS, etc
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->text('qris_static')->nullable(); // For QRIS static code
            $table->text('logo')->nullable(); // Logo URL
            $table->json('instructions')->nullable(); // Payment instructions
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
