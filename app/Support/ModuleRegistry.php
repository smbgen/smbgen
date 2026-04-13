<?php

namespace App\Support;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Schema;

class ModuleRegistry
{
    public static function definitions(): array
    {
        return config('modules.registry', []);
    }

    public static function isAvailable(string $moduleKey): bool
    {
        return array_key_exists($moduleKey, static::definitions());
    }

    public static function isEnabled(string $moduleKey): bool
    {
        $definition = static::definitions()[$moduleKey] ?? null;

        if (! $definition) {
            return false;
        }

        $override = static::setting("module_{$moduleKey}_enabled");

        if ($override !== null) {
            return (bool) $override;
        }

        return (bool) ($definition['default_enabled'] ?? false);
    }

    public static function selectedFrontend(): ?string
    {
        $selected = static::setting('deployment_frontend_module');

        if (is_string($selected) && static::isAvailable($selected)) {
            return $selected;
        }

        foreach (static::definitions() as $key => $definition) {
            if (($definition['category'] ?? null) === 'frontend' && ($definition['default_selected'] ?? false)) {
                return $key;
            }
        }

        return null;
    }

    public static function isSelectedFrontend(string $moduleKey): bool
    {
        $definition = static::definitions()[$moduleKey] ?? null;

        if (! $definition || ($definition['category'] ?? null) !== 'frontend') {
            return true;
        }

        return static::selectedFrontend() === $moduleKey;
    }

    public static function all(): array
    {
        $selectedFrontend = static::selectedFrontend();

        return collect(static::definitions())
            ->map(function (array $definition, string $key) use ($selectedFrontend): array {
                return [
                    'key' => $key,
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'category' => $definition['category'] ?? 'product',
                    'core' => (bool) ($definition['core'] ?? false),
                    'enabled' => static::isEnabled($key),
                    'selected_frontend' => $selectedFrontend === $key,
                ];
            })
            ->values()
            ->all();
    }

    public static function frontendOptions(): array
    {
        return array_values(array_filter(static::all(), fn (array $module): bool => $module['category'] === 'frontend'));
    }

    public static function persist(array $validated): void
    {
        $enabledModules = collect($validated['enabled_modules'] ?? [])->flip();
        $enabledModules->put($validated['frontend_module'], true);

        foreach (array_keys(static::definitions()) as $moduleKey) {
            BusinessSetting::set("module_{$moduleKey}_enabled", $enabledModules->has($moduleKey), 'boolean');
        }

        BusinessSetting::set('deployment_frontend_module', $validated['frontend_module'], 'string');
        BusinessSetting::set('deployment_name', $validated['deployment_name'], 'string');
        BusinessSetting::set('deployment_domain', $validated['deployment_domain'] ?? '', 'string');
        BusinessSetting::set('deployment_environment', $validated['deployment_environment'], 'string');
    }

    private static function setting(string $key): mixed
    {
        try {
            if (! Schema::hasTable('business_settings')) {
                return null;
            }

            return BusinessSetting::get($key);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
