<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per platform/account that a post should publish to
        Schema::create('social_post_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('social_account_id')->constrained()->onDelete('cascade');

            // Per-platform publish lifecycle
            $table->string('status')->default('pending'); // pending | publishing | published | failed | skipped

            // Platform-assigned post ID after successful publish
            $table->string('platform_post_id')->nullable();

            // URL to the live post
            $table->string('platform_post_url', 1000)->nullable();

            // Last error detail
            $table->text('last_error')->nullable();

            // Attempt tracking
            $table->unsignedTinyInteger('attempt_count')->default(0);
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->unique(['social_post_id', 'social_account_id']);
            $table->index(['social_account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_post_targets');
    }
};
