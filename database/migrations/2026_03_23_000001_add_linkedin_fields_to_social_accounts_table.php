<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->string('page_id')->nullable()->after('account_url');
            $table->string('page_name')->nullable()->after('page_id');
            $table->string('access_token_expires_at')->nullable()->after('page_name');
        });
    }

    public function down(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropColumn(['page_id', 'page_name', 'access_token_expires_at']);
        });
    }
};
