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
            if (Schema::hasColumn('products', 'regular_price')) {
                $table->renameColumn('regular_price', 'price');
            }
            if (Schema::hasColumn('products', 'extended_price')) {
                $table->dropColumn('extended_price');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'license_type')) {
                $table->dropColumn('license_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'price')) {
                $table->renameColumn('price', 'regular_price');
            }
            if (!Schema::hasColumn('products', 'extended_price')) {
                $table->decimal('extended_price', 10, 2)->after('regular_price');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'license_type')) {
                $table->enum('license_type', ['regular', 'extended'])->after('product_id');
            }
        });
    }
};
