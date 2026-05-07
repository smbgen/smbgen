<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class BusinessSettingsController extends Controller
{
    public function index()
    {
        try {
            $settings = [
                'app_name' => BusinessSetting::get('app_name', config('app.name')),
            ];

            // Get admin users with their notification preferences
            $adminUsers = \App\Models\User::where('role', \App\Models\User::ROLE_ADMINISTRATOR)
                ->orderBy('name')
                ->get();

            return view('admin.business_settings.index', compact('settings', 'adminUsers'));
        } catch (\Exception $e) {
            \Log::error('Business settings page error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.dashboard')
                ->with('error', 'Unable to load business settings. Please try again or contact support.');
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'app_name' => 'required|string|max:255',
                'admin_notifications' => 'nullable|array',
                'admin_notifications.*.notify_on_new_leads' => 'boolean',
                'admin_notifications.*.notify_on_new_bookings' => 'boolean',
            ]);

            // Save to database
            BusinessSetting::set('app_name', $validated['app_name'], 'string');

            // Update admin notification preferences
            if (isset($validated['admin_notifications'])) {
                foreach ($validated['admin_notifications'] as $userId => $preferences) {
                    $user = \App\Models\User::find($userId);
                    if ($user && $user->role === \App\Models\User::ROLE_ADMINISTRATOR) {
                        $user->update([
                            'notify_on_new_leads' => $preferences['notify_on_new_leads'] ?? false,
                            'notify_on_new_bookings' => $preferences['notify_on_new_bookings'] ?? false,
                        ]);
                    }
                }
            }

            // Sync to .env file (may fail on some servers)
            $envUpdateFailed = false;
            try {
                $this->updateEnvFile('APP_NAME', $validated['app_name']);
            } catch (\Exception $e) {
                $envUpdateFailed = true;
                \Log::warning('Failed to update .env file', [
                    'error' => $e->getMessage(),
                    'data' => $validated,
                ]);
            }

            if ($envUpdateFailed) {
                return back()->with('success', 'Settings saved to database. Note: .env file could not be updated automatically. You may need to update it manually.');
            }

            return back()->with('success', 'Business settings updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show field errors
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Business settings update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update settings: '.$e->getMessage());
        }
    }

    /**
     * Update a value in the .env file
     *
     * @throws \Exception If file cannot be written
     */
    protected function updateEnvFile(string $key, string $value): void
    {
        $path = base_path('.env');

        if (! file_exists($path)) {
            throw new \Exception('.env file not found at: '.$path);
        }

        if (! is_writable($path)) {
            throw new \Exception('.env file is not writable. Check file permissions.');
        }

        $envContent = file_get_contents($path);

        if ($envContent === false) {
            throw new \Exception('Failed to read .env file');
        }

        // Escape special characters and wrap in quotes if contains spaces
        $escapedValue = $value;
        if (strpos($value, ' ') !== false || strpos($value, '#') !== false) {
            $escapedValue = '"'.str_replace('"', '\"', $value).'"';
        }

        // Check if key exists
        $pattern = "/^{$key}=.*/m";

        if (preg_match($pattern, $envContent)) {
            // Update existing key
            $envContent = preg_replace($pattern, "{$key}={$escapedValue}", $envContent);
        } else {
            // Add new key at the end
            $envContent .= "\n{$key}={$escapedValue}\n";
        }

        $result = file_put_contents($path, $envContent);

        if ($result === false) {
            throw new \Exception('Failed to write to .env file');
        }

        \Log::info('Updated .env file', ['key' => $key, 'value' => $value]);
    }

    public function onboarding(): \Illuminate\View\View
    {
        $services = [
            [
                'key' => 'google_oauth',
                'name' => 'Google OAuth',
                'icon' => 'fab fa-google',
                'color' => 'red',
                'description' => 'Allows users to sign in with Google and connect their accounts. Required for Google Calendar sync.',
                'connected' => ! empty(config('services.google.client_id')) && ! empty(config('services.google.client_secret')),
                'config_route' => 'admin.environment_settings.index',
                'manage_route' => 'admin.google-oauth',
                'env_keys' => ['GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET', 'GOOGLE_REDIRECT_URI'],
            ],
            [
                'key' => 'google_calendar',
                'name' => 'Google Calendar',
                'icon' => 'fas fa-calendar-alt',
                'color' => 'green',
                'description' => 'Syncs bookings automatically to Google Calendar for you and your clients.',
                'connected' => ! empty(config('services.google.client_id')) && ! empty(config('services.google.calendar_redirect')),
                'config_route' => 'admin.environment_settings.index',
                'manage_route' => 'admin.calendar.index',
                'env_keys' => ['GOOGLE_CALENDAR_REDIRECT_URI'],
            ],
            [
                'key' => 'email_smtp',
                'name' => 'Email (SMTP)',
                'icon' => 'fas fa-envelope',
                'color' => 'blue',
                'description' => 'Sends booking confirmations, invoices, lead notifications, and password reset emails to clients.',
                'connected' => config('mail.default') !== 'log' && ! empty(config('mail.mailers.smtp.username')),
                'config_route' => 'admin.environment_settings.index',
                'manage_route' => 'admin.email-logs.index',
                'env_keys' => ['MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_FROM_ADDRESS'],
            ],
            [
                'key' => 'stripe',
                'name' => 'Stripe Payments',
                'icon' => 'fab fa-stripe-s',
                'color' => 'indigo',
                'description' => 'Processes client payments and manages invoices through Stripe.',
                'connected' => ! empty(config('services.stripe.key')) && ! empty(config('services.stripe.secret')),
                'config_route' => 'admin.environment_settings.index',
                'manage_route' => 'admin.billing.index',
                'env_keys' => ['STRIPE_KEY', 'STRIPE_SECRET', 'STRIPE_WEBHOOK_SECRET'],
            ],
            [
                'key' => 'ai',
                'name' => 'AI Assistant',
                'icon' => 'fas fa-robot',
                'color' => 'purple',
                'description' => 'Generates CMS page content, email copy, and other AI-powered features using Anthropic Claude.',
                'connected' => (bool) config('ai.enabled') && ! empty(config('ai.providers.anthropic.api_key')),
                'config_route' => 'admin.ai.settings.index',
                'manage_route' => 'admin.ai.settings.index',
                'env_keys' => ['ANTHROPIC_API_KEY', 'AI_CONTENT_GENERATION_ENABLED'],
            ],
            [
                'key' => 'google_analytics',
                'name' => 'Google Analytics',
                'icon' => 'fas fa-chart-bar',
                'color' => 'orange',
                'description' => 'Tracks visitor behaviour and traffic on your public-facing website.',
                'connected' => ! empty(config('business.integrations.google_analytics')),
                'config_route' => 'admin.environment_settings.index',
                'manage_route' => null,
                'env_keys' => ['GOOGLE_ANALYTICS_ID'],
            ],
            [
                'key' => 'google_maps',
                'name' => 'Google Maps',
                'icon' => 'fas fa-map-marker-alt',
                'color' => 'teal',
                'description' => 'Embeds interactive maps on your location pages and contact sections.',
                'connected' => ! empty(config('business.integrations.google_maps')),
                'config_route' => 'admin.environment_settings.index',
                'manage_route' => null,
                'env_keys' => ['GOOGLE_MAPS_API_KEY'],
            ],
        ];

        $connectedCount = collect($services)->where('connected', true)->count();

        return view('admin.onboarding.index', compact('services', 'connectedCount'));
    }
}
