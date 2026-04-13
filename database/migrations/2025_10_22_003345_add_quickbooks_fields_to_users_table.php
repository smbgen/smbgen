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
        Schema::table('users', function (Blueprint $table) {
            $table->string('quickbooks_realm_id')->nullable()->after('google_refresh_token');
            $table->text('quickbooks_access_token')->nullable()->after('quickbooks_realm_id');
            $table->text('quickbooks_refresh_token')->nullable()->after('quickbooks_access_token');
            $table->timestamp('quickbooks_token_expires_at')->nullable()->after('quickbooks_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'quickbooks_realm_id',
                'quickbooks_access_token',
                'quickbooks_refresh_token',
                'quickbooks_token_expires_at',
            ]);
        });
    }
};
