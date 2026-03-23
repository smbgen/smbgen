<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Business Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains business-specific configuration that can be
    | customized per deployment instance.
    |
    | Primary naming convention:
    |   - COMPANY_NAME: Your company/business name (login, admin, internal use)
    |   - BUSINESS_NAME: Optional override for public-facing CMS/navbar
    |                    (defaults to COMPANY_NAME if not set)
    |
    */

    'name' => env('BUSINESS_NAME', env('COMPANY_NAME', 'smbgen')),
    'company_name' => env('COMPANY_NAME', 'smbgen'),
    'tagline' => env('BUSINESS_TAGLINE', 'Client Management & AI Tools'),
    'description' => env('BUSINESS_DESCRIPTION', 'Professional client management platform with AI-powered tools'),

    // Contact Information
    'contact' => [
        'email' => env('BUSINESS_EMAIL', ''),
        'phone' => env('BUSINESS_PHONE', ''),
        'website' => env('BUSINESS_WEBSITE', env('APP_URL', '')),
        'address' => env('BUSINESS_ADDRESS', ''),
    ],

    // Social Media Links
    'social' => [
        'twitter' => env('BUSINESS_TWITTER', ''),
        'facebook' => env('BUSINESS_FACEBOOK', ''),
        'linkedin' => env('BUSINESS_LINKEDIN', ''),
        'instagram' => env('BUSINESS_INSTAGRAM', ''),
        'youtube' => env('BUSINESS_YOUTUBE', ''),
    ],

    // Branding
    'branding' => [
        'logo' => env('BUSINESS_LOGO', '/images/logo.png'),
        'favicon' => env('BUSINESS_FAVICON', '/favicon.ico'),
        'primary_color' => env('BUSINESS_PRIMARY_COLOR', '#3B82F6'),
        'secondary_color' => env('BUSINESS_SECONDARY_COLOR', '#8B5CF6'),
        'background_color' => env('BUSINESS_BG_COLOR', '#1f2937'),
    ],

    // Theme Configuration
    'theme' => [
        'mode' => env('THEME_MODE', 'dark'), // 'light' or 'dark'
    ],

    // Features Configuration
    'features' => [
        // Booking & appointment scheduling system
        'booking' => filter_var(env('FEATURE_BOOKING', true), FILTER_VALIDATE_BOOLEAN),

        // Billing, invoicing, and payment processing
        'billing' => filter_var(env('FEATURE_BILLING', false), FILTER_VALIDATE_BOOLEAN),

        // Internal messaging between clients and staff
        'messages' => filter_var(env('FEATURE_MESSAGES', true), FILTER_VALIDATE_BOOLEAN),

        // CMS editor for managing website content, pages, forms, and lead capture
        'cms' => filter_var(env('FEATURE_CMS', true), FILTER_VALIDATE_BOOLEAN),

        // Blog system with flexible content blocks and WordPress import
        'blog' => filter_var(env('FEATURE_BLOG', true), FILTER_VALIDATE_BOOLEAN),

        // File management and document storage
        'file_management' => filter_var(env('FEATURE_FILE_MANAGEMENT', true), FILTER_VALIDATE_BOOLEAN),

        // Inspection Reports - Generate and manage property inspection reports
        'inspection_reports' => filter_var(env('FEATURE_INSPECTION_REPORTS', false), FILTER_VALIDATE_BOOLEAN),

        // Phone System - AI-assisted phone calls with Bland AI / Twilio
        'phone_system' => filter_var(env('FEATURE_PHONE_SYSTEM', false), FILTER_VALIDATE_BOOLEAN),

        // Social Media - LinkedIn business page posting and scheduling
        'social_media' => filter_var(env('FEATURE_SOCIAL_MEDIA', false), FILTER_VALIDATE_BOOLEAN),
    ],

    // Business Type Configuration
    'business_type' => env('BUSINESS_TYPE', 'general'), // general, real_estate, financial, healthcare, legal

    // Custom Fields per Business Type
    'custom_fields' => [
        'real_estate' => [
            'client_fields' => ['property_interests', 'budget_range', 'preferred_locations'],
            'appointment_types' => ['property_viewing', 'consultation', 'closing'],
        ],
        'financial' => [
            'client_fields' => ['investment_goals', 'risk_tolerance', 'portfolio_size'],
            'appointment_types' => ['financial_planning', 'portfolio_review', 'consultation'],
        ],
        'healthcare' => [
            'client_fields' => ['medical_history', 'insurance_info', 'emergency_contact'],
            'appointment_types' => ['consultation', 'follow_up', 'emergency'],
        ],
        'legal' => [
            'client_fields' => ['case_type', 'court_dates', 'legal_requirements'],
            'appointment_types' => ['consultation', 'court_appearance', 'document_review'],
        ],
    ],

    // External Integrations
    'integrations' => [
        'google_analytics' => env('GOOGLE_ANALYTICS_ID', ''),
        'google_maps' => env('GOOGLE_MAPS_API_KEY', ''),
        'stripe' => [
            'public_key' => env('STRIPE_PUBLIC_KEY', ''),
            'secret_key' => env('STRIPE_SECRET_KEY', ''),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
        ],
        'mailchimp' => [
            'api_key' => env('MAILCHIMP_API_KEY', ''),
            'list_id' => env('MAILCHIMP_LIST_ID', ''),
        ],
    ],

    // Booking Settings
    'booking' => [
        'require_property_address' => filter_var(env('BOOKING_REQUIRE_PROPERTY_ADDRESS', false), FILTER_VALIDATE_BOOLEAN),
        'show_property_address_field' => filter_var(env('BOOKING_SHOW_PROPERTY_ADDRESS', true), FILTER_VALIDATE_BOOLEAN),
        'require_phone' => filter_var(env('BOOKING_REQUIRE_PHONE', false), FILTER_VALIDATE_BOOLEAN),
        'show_phone_field' => filter_var(env('BOOKING_SHOW_PHONE', true), FILTER_VALIDATE_BOOLEAN),
        'create_lead' => filter_var(env('BOOKING_CREATE_LEAD', true), FILTER_VALIDATE_BOOLEAN),
    ],

    // Billing Settings
    'billing' => [
        'hourly_rate_cents' => env('BILLING_HOURLY_RATE_CENTS', 20000), // $200.00/hour default
    ],

    // Deployment Information
    'deployment' => [
        'instance_name' => env('DEPLOYMENT_INSTANCE', 'production'),
        'environment' => env('APP_ENV', 'production'),
        'version' => env('APP_VERSION', '1.0.0'),
    ],
];
