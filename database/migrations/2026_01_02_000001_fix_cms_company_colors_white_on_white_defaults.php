<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_company_colors')) {
            return;
        }

        if (! Schema::hasColumn('cms_company_colors', 'text_color')) {
            return;
        }

        $query = DB::table('cms_company_colors')->where('text_color', '#ffffff');

        if (Schema::hasColumn('cms_company_colors', 'body_background_color')) {
            $query->where(function ($subQuery) {
                $subQuery
                    ->whereNull('body_background_color')
                    ->orWhere('body_background_color', '#ffffff');
            });
        }

        $query->update([
            'text_color' => '#1f2937',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Non-reversible data repair.
    }
};
