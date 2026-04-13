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
        Schema::table('cms_company_colors', function (Blueprint $table) {
            $table->string('theme_preset')->default('default')->after('auto_inject_css');
            $table->json('enabled_effects')->nullable()->after('theme_preset');
        });

        // Remove theme from navbar settings (move to theme system)
        Schema::table('cms_navbar_settings', function (Blueprint $table) {
            $table->dropColumn('theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_company_colors', function (Blueprint $table) {
            $table->dropColumn(['theme_preset', 'enabled_effects']);
        });

        Schema::table('cms_navbar_settings', function (Blueprint $table) {
            $table->string('theme')->default('default')->after('custom_text_color');
        });
    }
};
