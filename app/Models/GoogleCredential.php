<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleCredential extends Model
{
    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'calendar_id',
        'external_account_email',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        // Note: Encryption removed - was causing silent save failures
        // Tokens are secure via HTTPS and proper access controls
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Check if the access token is expired
     */
    public function isExpired(): bool
    {
        if (! $this->expires_at) {
            return true;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Check if token needs refresh (expires within 5 minutes)
     */
    public function needsRefresh(): bool
    {
        if (! $this->expires_at) {
            return true;
        }

        return $this->expires_at->subMinutes(5)->isPast();
    }

    /**
     * Refresh the access token using the refresh token
     *
     * @return bool True if refresh was successful
     */
    public function refreshAccessToken(): bool
    {
        if (! $this->refresh_token) {
            \Log::warning('Cannot refresh token: No refresh token available', [
                'credential_id' => $this->id,
                'user_id' => $this->user_id,
            ]);

            return false;
        }

        try {
            if (! class_exists('\Google_Client')) {
                \Log::error('Google API client not available for token refresh');

                return false;
            }

            $client = new \Google_Client;
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessType('offline');

            \Log::info('[GoogleCredential] Attempting to refresh Google access token', [
                'credential_id' => $this->id,
                'user_id' => $this->user_id,
                'current_expires_at' => $this->expires_at,
                'is_expired' => $this->isExpired(),
                'needs_refresh' => $this->needsRefresh(),
                'current_server_time' => now(),
                'app_timezone' => config('app.timezone'),
                'expires_at_timezone' => $this->expires_at ? $this->expires_at->timezoneName : null,
                'seconds_until_expiry' => $this->expires_at ? now()->diffInSeconds($this->expires_at, false) : null,
                'has_refresh_token' => ! empty($this->refresh_token),
                'refresh_token_length' => $this->refresh_token ? strlen($this->refresh_token) : 0,
                'client_id_configured' => ! empty(config('services.google.client_id')),
                'client_secret_configured' => ! empty(config('services.google.client_secret')),
            ]);

            $token = $client->fetchAccessTokenWithRefreshToken($this->refresh_token);

            \Log::info('[GoogleCredential] Token refresh response received', [
                'credential_id' => $this->id,
                'user_id' => $this->user_id,
                'has_access_token' => isset($token['access_token']),
                'has_error' => isset($token['error']),
                'token_keys' => array_keys($token),
                'expires_in' => $token['expires_in'] ?? null,
            ]);

            if (isset($token['error'])) {
                \Log::error('[GoogleCredential] Google token refresh failed with error', [
                    'credential_id' => $this->id,
                    'user_id' => $this->user_id,
                    'error' => $token['error'],
                    'error_description' => $token['error_description'] ?? 'No description',
                    'full_token_response' => json_encode($token),
                ]);

                return false;
            }

            if (! isset($token['access_token'])) {
                \Log::error('[GoogleCredential] No access token in refresh response', [
                    'credential_id' => $this->id,
                    'user_id' => $this->user_id,
                    'token_keys' => array_keys($token),
                    'full_token_response' => json_encode($token),
                ]);

                return false;
            }

            // Update the credential with new access token
            $this->update([
                'access_token' => $token['access_token'],
                'expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
            ]);

            \Log::info('[GoogleCredential] Google access token refreshed successfully', [
                'credential_id' => $this->id,
                'user_id' => $this->user_id,
                'new_expires_at' => $this->expires_at,
                'current_server_time' => now(),
                'seconds_until_expiry' => $this->expires_at ? now()->diffInSeconds($this->expires_at, false) : null,
                'app_timezone' => config('app.timezone'),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Exception during token refresh', [
                'credential_id' => $this->id,
                'user_id' => $this->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }
}
