<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class EnvironmentSettingsController extends Controller
{
    public function index()
    {
        // Get all environment settings organized by category
        $settings = [
            'app' => [
                'title' => 'Application Settings',
                'description' => 'Core application configuration',
                'items' => [
                    'name' => [
                        'env_key' => 'APP_NAME',
                        'env_value' => env('APP_NAME'),
                        'db_value' => BusinessSetting::get('app_name'),
                        'current_value' => config('app.name'),
                        'label' => 'Application Name',
                        'description' => 'Displayed in emails and throughout the interface',
                        'type' => 'text',
                    ],
                    'url' => [
                        'env_key' => 'APP_URL',
                        'env_value' => env('APP_URL'),
                        'db_value' => null,
                        'current_value' => config('app.url'),
                        'label' => 'Application URL',
                        'description' => 'Base URL for generating links and redirects',
                        'type' => 'url',
                    ],
                    'env' => [
                        'env_key' => 'APP_ENV',
                        'env_value' => env('APP_ENV'),
                        'db_value' => null,
                        'current_value' => config('app.env'),
                        'label' => 'Environment',
                        'description' => 'Current environment (local, production, etc.)',
                        'type' => 'text',
                        'readonly' => true,
                    ],
                ],
            ],
            'business' => [
                'title' => 'Business Information',
                'description' => 'Company details and branding',
                'items' => [
                    'company_name' => [
                        'env_key' => 'BUSINESS_COMPANY_NAME',
                        'env_value' => env('BUSINESS_COMPANY_NAME'),
                        'db_value' => BusinessSetting::get('company_name'),
                        'current_value' => config('business.company_name'),
                        'label' => 'Company Name',
                        'description' => 'Your company name for branding and legal purposes',
                        'type' => 'text',
                    ],
                ],
            ],
            'mail' => [
                'title' => 'Mail Configuration',
                'description' => 'Email sending settings',
                'items' => [
                    'mailer' => [
                        'env_key' => 'MAIL_MAILER',
                        'env_value' => env('MAIL_MAILER'),
                        'db_value' => null,
                        'current_value' => config('mail.default'),
                        'label' => 'Mail Driver',
                        'description' => 'Email service provider (smtp, mailgun, etc.)',
                        'type' => 'text',
                        'readonly' => true,
                    ],
                    'from_address' => [
                        'env_key' => 'MAIL_FROM_ADDRESS',
                        'env_value' => env('MAIL_FROM_ADDRESS'),
                        'db_value' => null,
                        'current_value' => config('mail.from.address'),
                        'label' => 'From Email',
                        'description' => 'Default sender email address',
                        'type' => 'email',
                        'readonly' => true,
                    ],
                ],
            ],
            'storage' => [
                'title' => 'Storage Configuration',
                'description' => 'File storage and cloud settings',
                'items' => [
                    'filesystem' => [
                        'env_key' => 'FILESYSTEM_DISK',
                        'env_value' => env('FILESYSTEM_DISK'),
                        'db_value' => null,
                        'current_value' => config('filesystems.default'),
                        'label' => 'Default Storage Disk',
                        'description' => 'Default storage disk for file uploads',
                        'type' => 'text',
                        'readonly' => true,
                    ],
                    'cloud_connected' => [
                        'env_key' => 'LARAVEL_CLOUD_DISK_CONFIG',
                        'env_value' => ! empty(env('LARAVEL_CLOUD_DISK_CONFIG')) ? 'Configured' : null,
                        'db_value' => null,
                        'current_value' => ! empty(env('LARAVEL_CLOUD_DISK_CONFIG')) ? 'Connected' : 'Not Connected',
                        'label' => 'Laravel Cloud Storage',
                        'description' => 'Laravel Cloud object storage buckets status',
                        'type' => 'text',
                        'readonly' => true,
                    ],
                ],
            ],
            'oauth' => [
                'title' => 'OAuth & Authentication',
                'description' => 'Google OAuth and social login settings',
                'items' => [
                    'google_client_id' => [
                        'env_key' => 'GOOGLE_CLIENT_ID',
                        'env_value' => env('GOOGLE_CLIENT_ID') ? 'Configured' : null,
                        'db_value' => null,
                        'current_value' => env('GOOGLE_CLIENT_ID') ? 'Set' : 'Not Set',
                        'label' => 'Google OAuth Client ID',
                        'description' => 'For Google login and calendar integration',
                        'type' => 'text',
                        'readonly' => true,
                    ],
                ],
            ],
            'features' => [
                'title' => 'Feature Flags',
                'description' => 'Enable or disable application features',
                'items' => [
                    'cms' => [
                        'env_key' => 'FEATURE_CMS',
                        'env_value' => env('FEATURE_CMS'),
                        'db_value' => BusinessSetting::get('feature_cms'),
                        'current_value' => config('business.features.cms'),
                        'label' => 'CMS Editor',
                        'description' => 'Content management system, page editor, and lead forms',
                        'type' => 'boolean',
                    ],
                    'booking' => [
                        'env_key' => 'FEATURE_BOOKING',
                        'env_value' => env('FEATURE_BOOKING'),
                        'db_value' => BusinessSetting::get('feature_booking'),
                        'current_value' => config('business.features.booking'),
                        'label' => 'Appointments & Booking',
                        'description' => 'Calendar, appointment scheduling, and Google Calendar sync',
                        'type' => 'boolean',
                    ],
                    'messages' => [
                        'env_key' => 'FEATURE_MESSAGES',
                        'env_value' => env('FEATURE_MESSAGES'),
                        'db_value' => BusinessSetting::get('feature_messages'),
                        'current_value' => config('business.features.messages'),
                        'label' => 'Internal Messages',
                        'description' => 'In-app messaging system between admins and clients',
                        'type' => 'boolean',
                    ],
                    'file_management' => [
                        'env_key' => 'FEATURE_FILE_MANAGEMENT',
                        'env_value' => env('FEATURE_FILE_MANAGEMENT'),
                        'db_value' => BusinessSetting::get('feature_file_management'),
                        'current_value' => config('business.features.file_management'),
                        'label' => 'File Management',
                        'description' => 'Client file uploads, document management, and cloud storage',
                        'type' => 'boolean',
                    ],
                    'billing' => [
                        'env_key' => 'FEATURE_BILLING',
                        'env_value' => env('FEATURE_BILLING'),
                        'db_value' => BusinessSetting::get('feature_billing'),
                        'current_value' => config('business.features.billing'),
                        'label' => 'Billing & Invoices',
                        'description' => 'Stripe integration, invoicing, and payment processing',
                        'type' => 'boolean',
                    ],
                    'inspection_reports' => [
                        'env_key' => 'FEATURE_INSPECTION_REPORTS',
                        'env_value' => env('FEATURE_INSPECTION_REPORTS'),
                        'db_value' => BusinessSetting::get('feature_inspection_reports'),
                        'current_value' => config('business.features.inspection_reports'),
                        'label' => 'Inspection Reports',
                        'description' => 'Property inspection report generation and management',
                        'type' => 'boolean',
                    ],
                ],
            ],
        ];

        return view('admin.environment_settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        try {
            // Only update settings that don't have env values set
            $updated = [];

            // Handle app settings
            if (! env('APP_NAME')) {
                if ($request->has('app_name')) {
                    BusinessSetting::set('app_name', $request->input('app_name'), 'string');
                    $updated[] = 'Application Name';
                }
            }

            // Handle business settings
            if (! env('BUSINESS_COMPANY_NAME')) {
                if ($request->has('company_name')) {
                    BusinessSetting::set('company_name', $request->input('company_name'), 'string');
                    $updated[] = 'Company Name';
                }
            }

            // Handle feature flags
            $featureMap = [
                'cms' => 'FEATURE_CMS',
                'booking' => 'FEATURE_BOOKING',
                'messages' => 'FEATURE_MESSAGES',
                'file_management' => 'FEATURE_FILE_MANAGEMENT',
                'billing' => 'FEATURE_BILLING',
                'inspection_reports' => 'FEATURE_INSPECTION_REPORTS',
            ];

            foreach ($featureMap as $feature => $envKey) {
                if (! env($envKey)) {
                    $value = $request->has("feature_{$feature}") ? 'true' : 'false';
                    BusinessSetting::set("feature_{$feature}", $value, 'boolean');
                    $updated[] = ucwords(str_replace('_', ' ', $feature));
                }
            }

            if (empty($updated)) {
                return back()->with('info', 'No settings were updated. All values are controlled by .env file.');
            }

            return back()->with('success', 'Environment settings updated: '.implode(', ', $updated));
        } catch (\Exception $e) {
            \Log::error('Environment settings update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update settings: '.$e->getMessage());
        }
    }
}
