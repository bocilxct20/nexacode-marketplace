<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product Bundles Table
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('thumbnail')->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
            $table->integer('sales_count')->default(0);
            $table->timestamps();
            
            $table->index('author_id');
            $table->index('status');
            $table->index('slug');
        });

        // Bundle Products Pivot Table
        Schema::create('bundle_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('product_bundles')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['bundle_id', 'product_id']);
            $table->index('bundle_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_products');
        Schema::dropIfExists('product_bundles');
    }
};
