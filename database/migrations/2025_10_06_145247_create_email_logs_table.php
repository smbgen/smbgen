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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');

            // Email details
            $table->string('to_email');
            $table->text('cc_email')->nullable();
            $table->string('subject', 500);
            $table->text('body');

            // Status tracking
            $table->string('status')->default('pending');
            // pending, sent, delivered, bounced, failed, opened, clicked

            // Delivery tracking
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();

            // Error tracking
            $table->text('error_message')->nullable();
            $table->text('smtp_response')->nullable();

            // Engagement tracking
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->timestamp('last_opened_at')->nullable();
            $table->timestamp('last_clicked_at')->nullable();

            // Metadata
            $table->uuid('tracking_id')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('status');
            $table->index('to_email');
            $table->index('sent_at');
            $table->index('tracking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
