<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function (object $notifiable): string {
            $expiresAt = now()->addMinutes(config('auth.verification.expire', 60));

            $relativeSignedUrl = URL::temporarySignedRoute(
                'verification.verify',
                $expiresAt,
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
                absolute: false,
            );

            Log::info('Verification URL generated', [
                'user_id' => $notifiable->getKey(),
                'email' => method_exists($notifiable, 'getEmailForVerification') ? $notifiable->getEmailForVerification() : null,
                'expires_at_iso' => $expiresAt->toIso8601String(),
                'expires_unix' => $expiresAt->timestamp,
                'now_iso' => now()->toIso8601String(),
                'now_unix' => now()->timestamp,
                'timezone' => config('app.timezone'),
                'app_url' => config('app.url'),
                'signed_path' => parse_url($relativeSignedUrl, PHP_URL_PATH),
                'expires_query' => request()?->query('expires'),
            ]);

            return URL::to($relativeSignedUrl);
        });

        // Load business settings from DB and merge with config
        try {
            $dbSettings = \App\Models\BusinessSetting::getAll();

            // Override config with DB settings
            foreach ($dbSettings as $key => $value) {
                config(["business.{$key}" => $value]);
            }
        } catch (\Exception $e) {
            // Ignore DB errors during migrations or when table doesn't exist yet
        }
    }
}
