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
        Schema::table('google_credentials', function (Blueprint $table) {
            // Add unique constraint - one Google account per user
            $table->unique('user_id');

            // Add index for faster lookups by external email
            $table->index('external_account_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('google_credentials')) {
            return;
        }

        Schema::table('google_credentials', function (Blueprint $table) {
            if (Schema::hasColumn('google_credentials', 'user_id')) {
                $table->dropForeign(['user_id']);
            }

            $table->dropUnique('google_credentials_user_id_unique');
            $table->dropIndex('google_credentials_external_account_email_index');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
