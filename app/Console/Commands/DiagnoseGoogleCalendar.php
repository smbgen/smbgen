<?php

namespace App\Console\Commands;

use App\Models\GoogleCredential;
use App\Models\User;
use Illuminate\Console\Command;

class DiagnoseGoogleCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:diagnose {--migrate : Migrate legacy data to new table} {--details : Show timezone and expiry details}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose Google Calendar connection issues and optionally migrate legacy data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Diagnosing Google Calendar Setup...');
        $this->newLine();

        // Timezone context
        $this->info('🕒 Timezone Context:');
        $this->line('  App timezone: '.config('app.timezone'));
        $this->line('  Server now(): '.now()->format('Y-m-d H:i:s T'));
        try {
            $dbNow = \DB::select('select now() as now')[0]->now ?? null;
            if ($dbNow) {
                $this->line('  DB now(): '.$dbNow);
            }
        } catch (\Throwable $e) {
            $this->line('  DB now(): unavailable ('.$e->getMessage().')');
        }
        $this->newLine();

        // Check configuration
        $this->info('📋 Configuration Check:');
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');
        $calendarRedirect = config('services.google.calendar_redirect');

        $this->line('  Client ID: '.($clientId ? '✓ Set' : '✗ Missing'));
        $this->line('  Client Secret: '.($clientSecret ? '✓ Set' : '✗ Missing'));
        $this->line('  Redirect URI: '.($redirectUri ?: '✗ Not set'));
        $this->line('  Calendar Redirect: '.($calendarRedirect ?: '(using default)'));
        $this->newLine();

        // Check database tables
        $this->info('💾 Database Schema:');
        $hasGoogleCredsTable = \Schema::hasTable('google_credentials');
        $this->line('  google_credentials table: '.($hasGoogleCredsTable ? '✓ Exists' : '✗ Missing'));

        if ($hasGoogleCredsTable) {
            $credCount = GoogleCredential::count();
            $this->line("  Records in google_credentials: {$credCount}");
        }

        $usersColumns = \Schema::getColumnListing('users');
        $hasLegacyColumns = in_array('google_refresh_token', $usersColumns);
        $this->line('  Legacy columns in users: '.($hasLegacyColumns ? '✓ Present' : '✗ Not found'));
        $this->newLine();

        // Check for users with calendar connections
        $this->info('👥 User Connections:');

        $usersWithNew = User::whereHas('googleCredential', function ($q) {
            $q->whereNotNull('refresh_token');
        })->get();

        $this->line('  Users with new GoogleCredential: '.$usersWithNew->count());
        foreach ($usersWithNew as $user) {
            $cred = $user->googleCredential;
            $expired = $cred->isExpired() ? '⚠️ EXPIRED' : '✓ Valid';
            $this->line("    - {$user->name} ({$user->email}) - {$expired}");
            $this->line("      Calendar: {$cred->calendar_id}");
            $this->line('      Expires: '.($cred->expires_at ? $cred->expires_at->format('Y-m-d H:i:s T') : 'Unknown'));
            if ($this->option('details')) {
                $this->line('      Needs refresh (<=5m): '.($cred->needsRefresh() ? 'Yes' : 'No'));
                $this->line('      Seconds until expiry: '.($cred->expires_at ? now()->diffInSeconds($cred->expires_at, false) : 'Unknown'));
            }
        }

        if ($hasLegacyColumns) {
            $usersWithLegacy = User::whereNotNull('google_refresh_token')
                ->whereDoesntHave('googleCredential')
                ->get();

            $this->line('  Users with legacy google_refresh_token: '.$usersWithLegacy->count());
            foreach ($usersWithLegacy as $user) {
                $this->line("    - {$user->name} ({$user->email})");
                $this->line('      Calendar: '.($user->google_calendar_id ?? 'primary'));
            }

            if ($usersWithLegacy->count() > 0 && $this->option('migrate')) {
                $this->newLine();
                $this->warn('🔄 Migrating legacy data to google_credentials table...');
                foreach ($usersWithLegacy as $user) {
                    try {
                        GoogleCredential::create([
                            'user_id' => $user->id,
                            'access_token' => '',
                            'refresh_token' => $user->google_refresh_token,
                            'expires_at' => now(),
                            'calendar_id' => $user->google_calendar_id ?? 'primary',
                            'external_account_email' => $user->email,
                        ]);
                        $this->line("  ✓ Migrated: {$user->name}");
                    } catch (\Exception $e) {
                        $this->error("  ✗ Failed: {$user->name} - ".$e->getMessage());
                    }
                }
            } elseif ($usersWithLegacy->count() > 0) {
                $this->newLine();
                $this->warn('💡 Tip: Run with --migrate to automatically migrate legacy data');
            }
        }

        $this->newLine();

        // Check Google API client
        $this->info('📦 Dependencies:');
        $hasGoogleClient = class_exists('\Google_Client');
        $this->line('  Google API Client: '.($hasGoogleClient ? '✓ Installed' : '✗ Missing'));

        if (! $hasGoogleClient) {
            $this->error('  Run: composer require google/apiclient');
        }

        $this->newLine();

        // Final recommendations
        $this->info('💡 Recommendations:');
        if (! $clientId || ! $clientSecret) {
            $this->line('  • Configure GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env');
        }
        if (! $hasGoogleCredsTable) {
            $this->line('  • Run: php artisan migrate');
        }
        if ($usersWithNew->count() === 0 && $usersWithLegacy->count() === 0) {
            $this->line('  • Connect calendar at /admin/calendar');
        }
        foreach ($usersWithNew as $user) {
            if ($user->googleCredential->isExpired()) {
                $this->line("  • Reconnect calendar for {$user->name} (token expired)");
            }
        }

        $this->newLine();
        $this->info('✅ Diagnosis complete!');

        return 0;
    }
}
