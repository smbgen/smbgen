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
            // Track when user account was provisioned
            $table->timestamp('user_provisioned_at')->nullable()->after('is_active');
            // Track when user activated their account (first login)
            $table->timestamp('account_activated_at')->nullable()->after('user_provisioned_at');
            // Track last login for this client
            $table->timestamp('last_login_at')->nullable()->after('account_activated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['user_provisioned_at', 'account_activated_at', 'last_login_at']);
        });
    }
};
