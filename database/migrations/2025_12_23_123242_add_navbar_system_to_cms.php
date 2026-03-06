<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add show_navbar field to cms_pages
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->boolean('show_navbar')->default(true)->after('is_published');
        });

        // Create navbar settings table for global navbar configuration
        Schema::create('cms_navbar_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_text')->nullable();
            $table->string('logo_image_url')->nullable();
            $table->boolean('use_business_colors')->default(true);
            $table->string('custom_bg_color')->nullable();
            $table->string('custom_text_color')->nullable();
            $table->json('menu_items')->nullable(); // Array of {label, url, target, order}
            $table->timestamps();
        });

        // Create default navbar settings
        DB::table('cms_navbar_settings')->insert([
            'logo_text' => config('business.name'),
            'use_business_colors' => true,
            'menu_items' => json_encode([
                ['label' => 'Home', 'url' => '/', 'target' => '_self', 'order' => 1],
                ['label' => 'Blog', 'url' => '/blog', 'target' => '_self', 'order' => 2],
                ['label' => 'Contact', 'url' => '/contact', 'target' => '_self', 'order' => 3],
                ['label' => 'Book', 'url' => '/book', 'target' => '_self', 'order' => 4],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn('show_navbar');
        });

        Schema::dropIfExists('cms_navbar_settings');
    }
};
