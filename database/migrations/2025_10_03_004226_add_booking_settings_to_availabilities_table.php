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
        Schema::table('availabilities', function (Blueprint $table) {
            $table->integer('minimum_booking_notice_hours')->default(24)->after('duration');
            $table->integer('maximum_booking_days_ahead')->default(28)->after('minimum_booking_notice_hours');
            $table->string('timezone')->default('UTC')->after('maximum_booking_days_ahead');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn(['minimum_booking_notice_hours', 'maximum_booking_days_ahead', 'timezone']);
        });
    }
};
