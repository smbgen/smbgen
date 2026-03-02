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
        Schema::table('booking_field_configs', function (Blueprint $table) {
            $table->boolean('send_admin_notifications')->default(true)->after('require_notes');
            $table->string('admin_notification_email')->nullable()->after('send_admin_notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_field_configs', function (Blueprint $table) {
            $table->dropColumn(['send_admin_notifications', 'admin_notification_email']);
        });
    }
};
