<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending',
                'pending_payment',
                'pending_verification',
                'processing',
                'completed',
                'cancelled',
                'failed',
                'refunded'
            ) DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending',
                'pending_verification',
                'completed',
                'failed',
                'refunded',
                'cancelled'
            ) DEFAULT 'pending'");
        });
    }
};
