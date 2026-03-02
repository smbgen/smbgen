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
        Schema::create('subscription_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free Trial, Starter ($47), Professional ($97), Dedicated
            $table->string('slug')->unique(); // free-trial, starter, professional, dedicated
            $table->text('description')->nullable();
            $table->unsignedInteger('price_cents')->default(0); // 0 for free, 4700 for $47, etc.
            $table->string('billing_period')->default('monthly'); // monthly or yearly
            $table->string('stripe_price_id')->nullable(); // Stripe price ID for Stripe integration
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable(); // Array of feature flags: ['booking' => true, 'messaging' => false, ...]
            $table->json('limits')->nullable(); // Array of limits: ['max_users' => 3, 'max_clients' => 100, ...]
            $table->integer('sort_order')->default(0); // Order to display on pricing page
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_tiers');
    }
};
