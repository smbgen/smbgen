<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('subdomain')->unique()->nullable();
                $table->string('custom_domain')->unique()->nullable();
                $table->enum('plan', ['trial', 'starter', 'professional', 'enterprise'])->default('trial');
                $table->foreignId('subscription_tier_id')->nullable()->constrained('subscription_tiers')->nullOnDelete();
                $table->timestamp('trial_ends_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('stripe_customer_id')->nullable();
                $table->string('stripe_subscription_id')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('domains')) {
            Schema::create('domains', function (Blueprint $table) {
                $table->increments('id');
                $table->string('domain', 255)->unique();
                $table->string('tenant_id');
                $table->timestamps();

                $table->foreign('tenant_id')
                    ->references('id')
                    ->on('tenants')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('tenant_id')->nullable()->after('id');
                $table->index('tenant_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
        Schema::dropIfExists('tenants');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
};
