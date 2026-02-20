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
        // 1. Add fields to help_articles
        Schema::table('help_articles', function (Blueprint $table) {
            $table->string('schema_type')->nullable()->after('content');
            $table->json('schema_data')->nullable()->after('schema_type');
            $table->text('internal_notes')->nullable()->after('schema_data');
            $table->integer('read_time_minutes')->nullable()->after('sort_order');
        });

        // 2. Create help_search_logs for analytics
        Schema::create('help_search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->integer('results_count')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // 3. Create help_article_feedbacks for detailed insights
        Schema::create('help_article_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('help_article_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_helpful');
            $table->text('comment')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // 4. Create help_article_versions for CMS history
        Schema::create('help_article_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('help_article_id')->constrained()->cascadeOnDelete();
            $table->string('title_snapshot');
            $table->longText('content_snapshot');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_article_versions');
        Schema::dropIfExists('help_article_feedbacks');
        Schema::dropIfExists('help_search_logs');
        
        Schema::table('help_articles', function (Blueprint $table) {
            $table->dropColumn(['schema_type', 'schema_data', 'internal_notes', 'read_time_minutes']);
        });
    }
};
