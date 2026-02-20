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
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->text('admin_notes')->nullable()->after('admin_response');
            $table->timestamp('processed_at')->nullable()->after('admin_notes');
            $table->foreignId('processed_by')->nullable()->after('processed_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropColumn(['admin_notes', 'processed_at', 'processed_by']);
        });
    }
};
