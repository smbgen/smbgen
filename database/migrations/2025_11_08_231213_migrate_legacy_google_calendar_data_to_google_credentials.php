<?php

use App\Models\GoogleCredential;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate any users with legacy google_refresh_token to google_credentials table
        $usersWithLegacyData = User::whereNotNull('google_refresh_token')
            ->whereDoesntHave('googleCredential')
            ->get();

        foreach ($usersWithLegacyData as $user) {
            try {
                GoogleCredential::create([
                    'user_id' => $user->id,
                    'access_token' => '', // Access token not stored in legacy format
                    'refresh_token' => $user->google_refresh_token,
                    'expires_at' => now(), // Mark as expired to force immediate refresh
                    'calendar_id' => $user->google_calendar_id ?? 'primary',
                    'external_account_email' => $user->email,
                ]);

                \Log::info('Migrated legacy Google Calendar data', [
                    'user_id' => $user->id,
                    'calendar_id' => $user->google_calendar_id ?? 'primary',
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to migrate legacy Google Calendar data', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        \Log::info('Legacy Google Calendar migration complete', [
            'migrated_count' => $usersWithLegacyData->count(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't delete data on rollback - keep it safe
        \Log::info('Rollback: Not deleting google_credentials data for safety');
    }
};
