<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Coupons Table
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->decimal('min_purchase', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_user')->default(1);
            $table->integer('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->timestamps();
            
            $table->index('author_id');
            $table->index('code');
            $table->index('status');
            $table->index('expires_at');
        });

        // Coupon Products Pivot Table
        Schema::create('coupon_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['coupon_id', 'product_id']);
            $table->index('coupon_id');
            $table->index('product_id');
        });

        // Coupon Usage Tracking
        Schema::create('coupon_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();
            
            $table->index('coupon_id');
            $table->index('user_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usage');
        Schema::dropIfExists('coupon_products');
        Schema::dropIfExists('coupons');
    }
};
