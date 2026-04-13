<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class InitializeTenantDatabase implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public string $tenantId
    ) {}

    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (! $tenant) {
            Log::error('Tenant not found for initialization', ['tenant_id' => $this->tenantId]);

            return;
        }

        try {
            Log::info('Initializing tenant database', ['tenant_id' => $this->tenantId]);

            Artisan::call('tenants:migrate', [
                '--tenants' => [$this->tenantId],
                '--force' => true,
                '--path' => database_path('migrations'),
                '--realpath' => true,
            ]);

            Log::info('Tenant database initialized', [
                'tenant_id' => $this->tenantId,
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initialize tenant database', [
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
