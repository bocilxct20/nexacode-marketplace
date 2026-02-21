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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('icon_dark')->nullable()->after('icon');
            $table->string('accent_color')->nullable()->after('icon_dark');
        });

        Schema::table('help_categories', function (Blueprint $table) {
            $table->string('icon_dark')->nullable()->after('icon');
            $table->string('accent_color')->nullable()->after('icon_dark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['icon_dark', 'accent_color']);
        });

        Schema::table('help_categories', function (Blueprint $table) {
            $table->dropColumn(['icon_dark', 'accent_color']);
        });
    }
};
