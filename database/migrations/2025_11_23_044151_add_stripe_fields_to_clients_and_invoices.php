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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('quickbooks_synced_at');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id')->nullable()->after('quickbooks_synced_at');
            $table->string('stripe_checkout_session_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_client_secret')->nullable()->after('stripe_checkout_session_id');
            $table->string('stripe_payment_url')->nullable()->after('stripe_client_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('stripe_customer_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_payment_intent_id',
                'stripe_checkout_session_id',
                'stripe_client_secret',
                'stripe_payment_url',
            ]);
        });
    }
};
