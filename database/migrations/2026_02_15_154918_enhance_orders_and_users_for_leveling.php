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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('xp')->default(0)->after('trial_ends_at');
            $table->integer('level')->default(1)->after('xp');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('type')->default('product')->after('buyer_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'level']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn('subscription_plan_id');
        });
    }
};
