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
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->string('notification_email')->nullable()->after('form_redirect_url');
            $table->boolean('send_admin_notification')->default(false)->after('notification_email');
            $table->boolean('send_client_notification')->default(false)->after('send_admin_notification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn(['notification_email', 'send_admin_notification', 'send_client_notification']);
        });
    }
};
