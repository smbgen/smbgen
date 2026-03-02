<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add super_admin role to users table
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false)->after('role');
            });
        }

        // Create tenants table (Stancl expects this)
        if (! Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('subdomain')->unique()->nullable();
                $table->string('custom_domain')->unique()->nullable();

                // Trial & subscription tracking
                $table->enum('plan', ['trial', 'starter', 'professional', 'enterprise'])->default('trial');
                $table->timestamp('trial_ends_at')->nullable();
                $table->boolean('is_active')->default(true);

                // Billing
                $table->string('stripe_customer_id')->nullable();
                $table->string('stripe_subscription_id')->nullable();

                // Metadata
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }

        // Create domains table (Stancl expects this)
        if (! Schema::hasTable('domains')) {
            Schema::create('domains', function (Blueprint $table) {
                $table->increments('id');
                $table->string('domain', 255)->unique();
                $table->string('tenant_id');
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        // Add tenant_id to users table
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('tenant_id')->nullable()->after('id')->index();
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'tenant_id')) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                }
                if (Schema::hasColumn('users', 'is_super_admin')) {
                    $table->dropColumn('is_super_admin');
                }
            });
        }

        Schema::dropIfExists('domains');
        Schema::dropIfExists('tenants');
    }
};
