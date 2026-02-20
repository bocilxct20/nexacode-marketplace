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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('bundle_id')->nullable()->after('product_id')->constrained('product_bundles')->onDelete('set null');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('bundle_id')->nullable()->after('product_id')->constrained('product_bundles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
            $table->dropColumn('bundle_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
            $table->dropColumn('bundle_id');
        });
    }
};
