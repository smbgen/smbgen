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
            $table->string('body_background_color', 7)->default('#ffffff')->after('background_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_company_colors', function (Blueprint $table) {
            $table->dropColumn('body_background_color');
        });
    }
};
