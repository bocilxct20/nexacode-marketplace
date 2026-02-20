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
            $table->text('private_notes')->nullable()->after('archived_by');
            $table->boolean('notifications_enabled')->default(true)->after('private_notes');
            $table->string('notification_priority')->default('normal')->after('notifications_enabled'); // normal, high, muted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['private_notes', 'notifications_enabled', 'notification_priority']);
        });
    }
};
