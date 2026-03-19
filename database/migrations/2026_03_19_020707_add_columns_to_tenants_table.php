<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tenants', 'name')) {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('plan')->default('starter')->after('slug');
            $table->string('owner_email')->nullable()->after('plan');
            $table->json('modules_enabled')->nullable()->after('owner_email');
            $table->boolean('is_active')->default(true)->after('modules_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug', 'plan', 'owner_email', 'modules_enabled', 'is_active']);
        });
    }
};
