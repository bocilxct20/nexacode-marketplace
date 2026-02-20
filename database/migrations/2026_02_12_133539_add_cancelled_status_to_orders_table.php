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
            // Modify the status enum to include 'cancelled' and 'pending_verification'
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'pending_verification', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert to original enum values
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending'");
        });
    }
};
