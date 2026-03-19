<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tables that belong to a tenant (SIGNAL, RELAY, SURGE, CAST). */
    private array $tables = [
        'social_posts',
        'email_sequences',
        'email_sequence_steps',
        'email_sequence_enrollments',
        'deals',
        'managed_sites',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->string('tenant_id')->nullable()->after('id');
                $blueprint->foreign('tenant_id')
                    ->references('id')
                    ->on('tenants')
                    ->cascadeOnDelete();
                $blueprint->index('tenant_id', "{$table}_tenant_id_index");
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropForeign(['tenant_id']);
                $blueprint->dropIndex("{$table}_tenant_id_index");
                $blueprint->dropColumn('tenant_id');
            });
        }
    }
};
