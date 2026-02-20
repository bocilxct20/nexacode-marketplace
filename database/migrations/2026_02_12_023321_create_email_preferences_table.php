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
        Schema::create('email_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('order_confirmations')->default(true);
            $table->boolean('sale_notifications')->default(true);
            $table->boolean('product_updates')->default(true);
            $table->boolean('review_notifications')->default(true);
            $table->boolean('withdrawal_notifications')->default(true);
            $table->boolean('marketing_emails')->default(true);
            $table->boolean('newsletter')->default(true);
            $table->boolean('admin_notifications')->default(true);
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_preferences');
    }
};
