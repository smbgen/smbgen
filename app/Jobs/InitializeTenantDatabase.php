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

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $tenantId
    ) {}

    /**
     * Execute the job.
     */
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
            ]);

            Log::info('Tenant database initialized successfully', ['tenant_id' => $this->tenantId]);
        } catch (\Exception $e) {
            Log::error('Failed to initialize tenant database', [
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Will retry up to $tries times
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Tenant database initialization failed permanently', [
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage(),
        ]);

        // Optionally: Send notification to admin
        // Optionally: Mark tenant as needing manual setup
    }
}
