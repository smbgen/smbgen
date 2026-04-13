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
            $table->boolean('has_form')->default(false)->after('is_published');
            $table->json('form_fields')->nullable()->after('has_form');
            $table->string('form_submit_button_text')->default('Submit')->after('form_fields');
            $table->text('form_success_message')->nullable()->after('form_submit_button_text');
            $table->string('form_redirect_url')->nullable()->after('form_success_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn([
                'has_form',
                'form_fields',
                'form_submit_button_text',
                'form_success_message',
                'form_redirect_url',
            ]);
        });
    }
};
