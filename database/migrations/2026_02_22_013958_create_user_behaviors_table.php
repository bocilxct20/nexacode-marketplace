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
        Schema::create('user_behaviors', function (Blueprint $class) {
            $class->id();
            $class->foreignId('user_id')->constrained()->onDelete('cascade');
            $class->foreignId('category_id')->constrained()->onDelete('cascade');
            $class->integer('views_count')->default(1);
            $class->timestamp('last_viewed_at')->useCurrent();
            $class->unique(['user_id', 'category_id']);
            $class->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_behaviors');
    }
};
