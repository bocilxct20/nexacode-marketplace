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
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('slug');
            $table->boolean('allow_trial')->default(false)->after('price');
            $table->boolean('is_elite')->default(false)->after('allow_trial');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['is_default', 'allow_trial', 'is_elite']);
        });
    }
};
