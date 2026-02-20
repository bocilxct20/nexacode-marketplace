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
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('regular_price', 'price');
            $table->dropColumn('extended_price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('license_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('price', 'regular_price');
            $table->decimal('extended_price', 10, 2)->after('regular_price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('license_type', ['regular', 'extended'])->after('product_id');
        });
    }
};
