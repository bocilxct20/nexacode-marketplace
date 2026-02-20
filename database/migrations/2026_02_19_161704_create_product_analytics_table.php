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
        Schema::create('product_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
            $table->decimal('revenue', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_analytics');
    }
};
