<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleCredential;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class CalendarController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('admin.calendar.index', ['user' => $user]);
    }

    public function redirectToGoogle()
    {
        // Use the calendar-specific redirect URI if configured
        $redirectUri = config('services.google.calendar_redirect') ?? config('services.google.redirect');

        \Log::info('Initiating Google Calendar OAuth redirect', [
            'configured_calendar_redirect' => config('services.google.calendar_redirect'),
            'configured_base_redirect' => config('services.google.redirect'),
            'final_redirect_uri' => $redirectUri,
            'app_url' => config('app.url'),
            'client_id_configured' => !empty(config('services.google.client_id')),
            'client_id_value' => config('services.google.client_id'),
            'client_secret_configured' => !empty(config('services.google.client_secret')),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'request_url' => request()->url(),
            'request_full_url' => request()->fullUrl(),
        ]);

        $socialite = Socialite::driver('google')
            ->redirectUrl($redirectUri)
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/calendar.events',
                'https://www.googleapis.com/auth/calendar.readonly', // Needed to list calendars
                'https://www.googleapis.com/auth/drive.file', // Needed to store inspection reports
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent']);

        // Log the actual redirect URL that will be sent to Google
        try {
            $redirectResponse = $socialite->redirect();
            $actualRedirectUrl = $redirectResponse->getTargetUrl();
            
            \Log::info('Google OAuth redirect URL generated', [
                'full_redirect_url' => $actualRedirectUrl,
                'url_length' => strlen($actualRedirectUrl),
            ]);
            
            return $redirectResponse;
        } catch (\Exception $e) {
            \Log::error('Failed to generate Google OAuth redirect', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        \Log::info('Google Calendar callback received', [
            'has_code' => $request->has('code'),
            'has_error' => $request->has('error'),
            'query_params' => $request->query(),
        ]);

        try {
            // Check for error from Google
            if ($request->has('error')) {
                $errorDescription = $request->get('error_description', 'User denied access');
                \Log::warning('Google OAuth error', [
                    'error' => $request->get('error'),
                    'description' => $errorDescription,
                ]);

                return Redirect::route('admin.calendar.index')
                    ->with('error', 'Google Calendar connection failed: '.$errorDescription);
            }

            // Use the calendar-specific redirect URI if configured
            $redirectUri = config('services.google.calendar_redirect') ?? config('services.google.redirect');
            \Log::info('Using redirect URI', ['uri' => $redirectUri]);

            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUri)
                ->stateless()
                ->user();

            \Log::info('Google user retrieved', [
                'email' => $googleUser->getEmail(),
                'has_refresh_token' => ! empty($googleUser->refreshToken),
            ]);

            // Persist tokens to currently authenticated admin's user record
            $user = auth()->user();
            \Log::info('Current authenticated user', [
                'id' => $user->id,
                'email' => $user->email,
            ]);

            // Store the refresh token (required for offline access)
            $refreshToken = $googleUser->refreshToken;
            if (! $refreshToken) {
                \Log::warning('Google Calendar connection: No refresh token received', [
                    'user_id' => $user->id,
                    'has_token' => isset($googleUser->token),
                    'google_user_data' => $googleUser->user,
                ]);

                return Redirect::route('admin.calendar.index')
                    ->with('error', 'No refresh token received. Try revoking access at https://myaccount.google.com/permissions and reconnecting.');
            }

            \Log::info('About to save tokens to database', [
                'user_id' => $user->id,
                'refresh_token_length' => strlen($refreshToken),
                'access_token_length' => strlen($googleUser->token),
                'calendar_id' => $googleUser->user['email'] ?? 'primary',
                'expires_in' => $googleUser->expiresIn ?? 3600,
            ]);

            // Store or update credentials in google_credentials table
            try {
                $credential = GoogleCredential::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'access_token' => $googleUser->token,
                        'refresh_token' => $refreshToken,
                        'expires_at' => now()->addSeconds($googleUser->expiresIn ?? 3600),
                        'calendar_id' => $googleUser->user['email'] ?? 'primary',
                        'external_account_email' => $googleUser->getEmail(),
                    ]
                );

                \Log::info('Google credentials saved successfully', [
                    'credential_id' => $credential->id,
                    'user_id' => $user->id,
                    'calendar_id' => $credential->calendar_id,
                    'external_email' => $credential->external_account_email,
                    'expires_at' => $credential->expires_at,
                    'has_refresh_token' => ! empty($credential->refresh_token),
                ]);

                // Verify it was actually saved
                $verified = GoogleCredential::where('user_id', $user->id)->first();
                if (! $verified) {
                    throw new \Exception('Failed to verify credential was saved to database');
                }
                \Log::info('Verified credential in database', [
                    'verified_id' => $verified->id,
                    'has_refresh_token' => ! empty($verified->refresh_token),
                ]);

            } catch (\Exception $e) {
                \Log::error('Failed to save Google credentials to database', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }

            return Redirect::route('admin.calendar.index')
                ->with('status', 'Google Calendar connected successfully!');

        } catch (\Exception $e) {
            \Log::error('Google Calendar connection failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::route('admin.calendar.index')
                ->with('error', 'Failed to connect Google Calendar: '.$e->getMessage());
        }
    }

    public function disconnect(Request $request)
    {
        // Admin can disconnect any user, or user can disconnect themselves
        $userId = $request->input('user_id');

        if ($userId && auth()->user()->role === 'company_administrator') {
            // Admin disconnecting another user
            $user = User::findOrFail($userId);
        } else {
            // User disconnecting themselves
            $user = auth()->user();
        }

        // Delete the GoogleCredential record
        if ($user->googleCredential) {
            $user->googleCredential->delete();
            \Log::info('Google Calendar disconnected', [
                'user_id' => $user->id,
                'disconnected_by' => auth()->id(),
            ]);
        }

        // Also clear legacy fields if present
        if ($user->google_refresh_token) {
            $user->google_refresh_token = null;
            $user->google_calendar_id = null;
            $user->save();
            \Log::info('Legacy Google Calendar data cleared', [
                'user_id' => $user->id,
                'disconnected_by' => auth()->id(),
            ]);
        }

        $message = $userId ? "Google Calendar disconnected for {$user->name}." : 'Google calendar disconnected.';

        // Redirect back to the referring page or default to calendar index
        $redirectRoute = $request->header('referer') && str_contains($request->header('referer'), 'google-oauth')
            ? 'admin.google-oauth'
            : 'admin.calendar.index';

        return Redirect::route($redirectRoute)->with('status', $message);
    }

    public function selectCalendar(Request $request)
    {
        $user = auth()->user();

        if (! $user->googleCredential || ! $user->googleCredential->refresh_token) {
            return Redirect::route('admin.calendar.index')
                ->with('error', 'Please connect Google Calendar first.');
        }

        // Fetch available calendars
        try {
            // Check if Google API client is available
            if (! class_exists('\Google_Client')) {
                throw new \RuntimeException('Google API client not installed. Run: composer require google/apiclient');
            }

            $client = new \Google_Client;
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessType('offline');

            // Fetch access token using refresh token from GoogleCredential
            $token = $client->fetchAccessTokenWithRefreshToken($user->googleCredential->refresh_token);

            // Check for errors in token response
            if (isset($token['error'])) {
                throw new \RuntimeException('Token refresh failed: '.($token['error_description'] ?? $token['error']));
            }

            $service = new \Google_Service_Calendar($client);
            $calendarList = $service->calendarList->listCalendarList();

            $calendars = [];
            foreach ($calendarList->getItems() as $calendar) {
                $calendars[] = [
                    'id' => $calendar->getId(),
                    'summary' => $calendar->getSummary(),
                    'primary' => $calendar->getPrimary() ?? false,
                ];
            }

            \Log::info('Fetched calendar list successfully', [
                'user_id' => $user->id,
                'calendar_count' => count($calendars),
            ]);

            return view('admin.calendar.select', [
                'calendars' => $calendars,
                'currentCalendarId' => $user->googleCredential->calendar_id ?? 'primary',
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch calendar list', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::route('admin.calendar.index')
                ->with('error', 'Failed to fetch calendars: '.$e->getMessage());
        }
    }

    public function updateCalendar(Request $request)
    {
        $request->validate([
            'calendar_id' => 'required|string',
        ]);

        $user = auth()->user();

        if ($user->googleCredential) {
            $user->googleCredential->update([
                'calendar_id' => $request->calendar_id,
            ]);
        }

        return Redirect::route('admin.calendar.index')
            ->with('status', 'Calendar selection updated successfully!');
    }
}
