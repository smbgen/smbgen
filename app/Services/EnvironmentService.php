<?php

namespace App\Services;

use App\Models\BusinessSetting;

class EnvironmentService
{
    /**
     * Get a feature flag value, checking env first, then database
     */
    public static function getFeature(string $feature, bool $default = false): bool
    {
        $envKey = 'FEATURE_'.strtoupper($feature);
        $envValue = env($envKey);

        // If env is explicitly set, use it
        if ($envValue !== null) {
            return filter_var($envValue, FILTER_VALIDATE_BOOLEAN);
        }

        // Otherwise check database
        $dbValue = BusinessSetting::get("feature_{$feature}");
        if ($dbValue !== null) {
            return filter_var($dbValue, FILTER_VALIDATE_BOOLEAN);
        }

        // Fall back to default
        return $default;
    }

    /**
     * Get an app setting, checking env first, then database
     */
    public static function getSetting(string $key, $default = null)
    {
        $envKey = strtoupper($key);
        $envValue = env($envKey);

        // If env is explicitly set, use it
        if ($envValue !== null) {
            return $envValue;
        }

        // Otherwise check database
        $dbValue = BusinessSetting::get($key);
        if ($dbValue !== null) {
            return $dbValue;
        }

        // Fall back to default
        return $default;
    }

    /**
     * Check if a setting is controlled by environment variable
     */
    public static function isEnvControlled(string $envKey): bool
    {
        return env($envKey) !== null;
    }
}
