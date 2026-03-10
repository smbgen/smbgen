<?php

namespace App\Helpers;

class BusinessHelper
{
    /**
     * Get business name
     */
    public static function name(): string
    {
        return config('business.name', 'smbgen');
    }

    /**
     * Get company name
     */
    public static function companyName(): string
    {
        return config('business.company_name', 'smbgen');
    }

    /**
     * Get business tagline
     */
    public static function tagline(): string
    {
        return config('business.tagline', 'Client Management & AI Tools');
    }

    /**
     * Get business description
     */
    public static function description(): string
    {
        return config('business.description', 'Professional client management platform with AI-powered tools');
    }

    /**
     * Get contact email
     */
    public static function contactEmail(): string
    {
        return config('business.contact.email', '');
    }

    /**
     * Get business website
     */
    public static function website(): string
    {
        return config('business.contact.website', config('app.url', ''));
    }

    /**
     * Get business type
     */
    public static function businessType(): string
    {
        return config('business.business_type', 'general');
    }

    /**
     * Check if a feature is enabled
     */
    public static function isFeatureEnabled(string $feature): bool
    {
        return config("business.features.{$feature}", true);
    }

    /**
     * Get custom fields for current business type
     */
    public static function getCustomFields(): array
    {
        $businessType = self::businessType();

        return config("business.custom_fields.{$businessType}", []);
    }

    /**
     * Get client fields for current business type
     */
    public static function getClientFields(): array
    {
        $customFields = self::getCustomFields();

        return $customFields['client_fields'] ?? [];
    }

    /**
     * Get appointment types for current business type
     */
    public static function getAppointmentTypes(): array
    {
        $customFields = self::getCustomFields();

        return $customFields['appointment_types'] ?? ['consultation', 'meeting', 'follow_up'];
    }

    /**
     * Get deployment instance name
     */
    public static function instanceName(): string
    {
        return config('business.deployment.instance_name', 'production');
    }

    /**
     * Get app version
     */
    public static function version(): string
    {
        return config('business.deployment.version', '1.0.0');
    }

    /**
     * Check if this is a specific business type
     */
    public static function isBusinessType(string $type): bool
    {
        return self::businessType() === $type;
    }

    /**
     * Get business branding configuration
     */
    public static function branding(): array
    {
        return config('business.branding', []);
    }

    /**
     * Get integrations configuration
     */
    public static function integrations(): array
    {
        return config('business.integrations', []);
    }

    /**
     * Get Google Analytics ID
     */
    public static function googleAnalyticsId(): ?string
    {
        return config('business.integrations.google_analytics', '');
    }

    /**
     * Get Stripe configuration
     */
    public static function stripeConfig(): array
    {
        return config('business.integrations.stripe', []);
    }
}
