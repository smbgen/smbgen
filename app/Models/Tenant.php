<?php

declare(strict_types=1);

namespace App\Models;

use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains;

    /**
     * Real database columns (not stored in the JSON data blob).
     */
    public static function getCustomColumns(): array
    {
        return ['id', 'name', 'slug', 'plan', 'owner_email', 'modules_enabled', 'is_active'];
    }

    protected function casts(): array
    {
        return [
            'modules_enabled' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules_enabled ?? []);
    }

    /** All plans that exist. */
    public static function plans(): array
    {
        return ['starter', 'growth', 'scale', 'agency'];
    }

    /** The modules bundled with each plan. */
    public static function modulesForPlan(string $plan): array
    {
        return match ($plan) {
            'starter' => ['cast'],
            'growth' => ['cast', 'relay', 'signal'],
            'scale' => ['cast', 'relay', 'signal', 'surge', 'vault'],
            'agency' => ['cast', 'relay', 'signal', 'surge', 'vault', 'extreme'],
            default => [],
        };
    }
}
