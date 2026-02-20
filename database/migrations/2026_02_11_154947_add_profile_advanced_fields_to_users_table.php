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
            // Two-Factor Authentication
            $table->boolean('two_factor_enabled')->default(false)->after('password');
            $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            
            // Notification Preferences (JSON)
            $table->json('notification_preferences')->nullable()->after('two_factor_secret');
            
            // Activity Log
            $table->timestamp('last_login_at')->nullable()->after('notification_preferences');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->string('last_login_device')->nullable()->after('last_login_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_enabled',
                'two_factor_secret',
                'notification_preferences',
                'last_login_at',
                'last_login_ip',
                'last_login_device',
            ]);
        });
    }
};
