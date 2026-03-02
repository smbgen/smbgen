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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // home, about, contact, etc.
            $table->string('title');
            $table->text('head_content')->nullable(); // Custom meta tags, CSS, scripts for <head>
            $table->text('body_content')->nullable(); // Main page HTML content
            $table->string('cta_text')->nullable(); // Call-to-action button text
            $table->string('cta_url')->nullable(); // Call-to-action button URL
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index('slug');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
