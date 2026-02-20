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
            if (!$this->hasIndex('products', 'products_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('products', 'products_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->hasIndex('products', 'products_author_id_index')) {
                $table->index('author_id');
            }
            if (!$this->hasIndex('products', 'products_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            // Check if fulltext index exists
            if (!$this->hasIndex('products', 'products_name_description_fulltext')) {
                $table->fullText(['name', 'description']);
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!$this->hasIndex('orders', 'orders_buyer_id_index')) {
                $table->index('buyer_id');
            }
            if (!$this->hasIndex('orders', 'orders_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('orders', 'orders_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->hasIndex('orders', 'orders_buyer_id_status_index')) {
                $table->index(['buyer_id', 'status']);
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (!$this->hasIndex('reviews', 'reviews_product_id_index')) {
                $table->index('product_id');
            }
            if (!$this->hasIndex('reviews', 'reviews_buyer_id_index')) {
                $table->index('buyer_id');
            }
            if (!$this->hasIndex('reviews', 'reviews_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('product_views', function (Blueprint $table) {
            if (!$this->hasIndex('product_views', 'product_views_product_id_index')) {
                $table->index('product_id');
            }
            if (!$this->hasIndex('product_views', 'product_views_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->hasIndex('product_views', 'product_views_product_id_created_at_index')) {
                $table->index(['product_id', 'created_at']);
            }
        });
    }

    /**
     * Helper method to check if index exists
     */
    /**
     * Helper method to check if index exists
     */
    private function hasIndex($table, $index)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $idx) {
            if ($idx->Key_name === $index) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['author_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropFullText(['name', 'description']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['buyer_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['buyer_id', 'status']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['buyer_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('product_views', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['product_id', 'created_at']);
        });
    }
};
