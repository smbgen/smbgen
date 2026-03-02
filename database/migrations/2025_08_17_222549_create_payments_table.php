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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('amount'); // Amount in cents
            $table->string('currency', 3)->default('usd');
            $table->string('description');
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_session_id')->nullable()->unique();
            $table->string('status')->default('pending');
            $table->string('payment_type')->default('product'); // invoice, product, subscription
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
