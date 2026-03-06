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
        Schema::create('cms_company_colors', function (Blueprint $table) {
            $table->id();
            $table->string('primary_color', 7)->default('#3B82F6');
            $table->string('secondary_color', 7)->default('#10B981');
            $table->string('background_color', 7)->default('#1f2937');
            $table->string('text_color', 7)->default('#1f2937');
            $table->string('accent_color', 7)->default('#F59E0B');
            $table->boolean('auto_inject_css')->default(true);
            $table->timestamps();
        });

        // Create default colors record
        DB::table('cms_company_colors')->insert([
            'primary_color' => config('business.branding.primary_color') ?: '#3B82F6',
            'secondary_color' => config('business.branding.secondary_color') ?: '#10B981',
            'background_color' => config('business.branding.background_color') ?: '#1f2937',
            'text_color' => '#1f2937',
            'accent_color' => '#F59E0B',
            'auto_inject_css' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_company_colors');
    }
};
