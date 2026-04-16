<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            // OAuth token storage
            $table->text('access_token')->nullable()->after('credentials');
            $table->text('refresh_token')->nullable()->after('access_token');
            $table->timestamp('token_expires_at')->nullable()->after('refresh_token');

            // Platform-specific identifiers
            $table->string('platform_user_id')->nullable()->after('token_expires_at');
            $table->string('platform_page_id')->nullable()->after('platform_user_id');
            $table->string('platform_page_name')->nullable()->after('platform_page_id');

            // Meta Graph API: long-lived page access token
            $table->text('page_access_token')->nullable()->after('platform_page_name');

            // Scope granted during OAuth
            $table->text('scopes')->nullable()->after('page_access_token');

            // Soft connection state
            $table->string('connection_status')->default('connected')->after('active'); // connected | error | revoked
            $table->text('last_error')->nullable()->after('connection_status');
            $table->timestamp('last_used_at')->nullable()->after('last_error');
        });
    }

    public function down(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'access_token',
                'refresh_token',
                'token_expires_at',
                'platform_user_id',
                'platform_page_id',
                'platform_page_name',
                'page_access_token',
                'scopes',
                'connection_status',
                'last_error',
                'last_used_at',
            ]);
        });
    }
};
