<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Full audit log of every publish attempt (success or failure) per target
        Schema::create('social_publish_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_post_target_id')->constrained()->onDelete('cascade');

            $table->string('status'); // published | failed
            $table->string('platform'); // facebook | instagram | linkedin

            // Raw API response / error
            $table->text('response_body')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();

            // Idempotency key to prevent duplicate submissions
            $table->string('idempotency_key')->nullable();

            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index(['social_post_target_id', 'status']);
            $table->index('idempotency_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_publish_attempts');
    }
};
