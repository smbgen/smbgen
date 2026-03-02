<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Add subscription_tier_id foreign key
            $table->foreignId('subscription_tier_id')
                ->nullable()
                ->constrained('subscription_tiers')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeignIdFor('subscription_tiers');
        });
    }
};
