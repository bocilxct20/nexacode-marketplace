<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Account lockout (Feature #6)
            $table->integer('failed_login_count')->default(0)->after('last_login_device');
            $table->timestamp('account_locked_until')->nullable()->after('failed_login_count');

            // Pending email change (Feature #5)
            $table->string('pending_email')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['failed_login_count', 'account_locked_until', 'pending_email']);
        });
    }
};
