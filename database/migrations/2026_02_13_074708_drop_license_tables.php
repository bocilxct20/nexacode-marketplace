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
        // Drop license_activations table first (has foreign key to licenses)
        Schema::dropIfExists('license_activations');

        // Drop licenses table
        Schema::dropIfExists('licenses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate licenses table
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('version_id')->nullable()->constrained('product_versions')->onDelete('set null');
            $table->string('license_key')->unique();
            $table->enum('type', ['regular', 'extended'])->default('regular');
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->integer('activation_limit')->default(1);
            $table->integer('activation_count')->default(0);
            $table->timestamps();
        });

        // Recreate license_activations table
        Schema::create('license_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->string('domain')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('activated_at');
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });

        // Add license_id back to downloads table
        Schema::table('downloads', function (Blueprint $table) {
            $table->foreignId('license_id')->nullable()->after('version_id')->constrained()->onDelete('set null');
        });
    }
};
