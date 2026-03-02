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
            // Custom CSS Editor
            $table->text('custom_css')->nullable()->after('enabled_effects');
            $table->text('base_theme_css')->nullable()->after('custom_css');

            // CSS Class Whitelist for AI (JSON array)
            $table->json('allowed_css_classes')->nullable()->after('base_theme_css');

            // SEO Meta Settings (Global Defaults)
            $table->string('seo_title_template')->nullable()->after('allowed_css_classes');
            $table->text('seo_meta_description')->nullable()->after('seo_title_template');
            $table->text('seo_meta_keywords')->nullable()->after('seo_meta_description');

            // Open Graph (OG) Tags
            $table->string('og_site_name')->nullable()->after('seo_meta_keywords');
            $table->string('og_type')->default('website')->after('og_site_name');
            $table->text('og_image_url')->nullable()->after('og_type');

            // Twitter Card
            $table->string('twitter_card_type')->default('summary_large_image')->after('og_image_url');
            $table->string('twitter_site_handle')->nullable()->after('twitter_card_type');

            // Additional Header Scripts/Styles
            $table->text('custom_head_scripts')->nullable()->after('twitter_site_handle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_company_colors', function (Blueprint $table) {
            $table->dropColumn([
                'custom_css',
                'base_theme_css',
                'allowed_css_classes',
                'seo_title_template',
                'seo_meta_description',
                'seo_meta_keywords',
                'og_site_name',
                'og_type',
                'og_image_url',
                'twitter_card_type',
                'twitter_site_handle',
                'custom_head_scripts',
            ]);
        });
    }
};
