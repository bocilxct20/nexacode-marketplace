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
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->after('author_id')->constrained('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn(['author_id', 'product_id']);
        });
    }
};
