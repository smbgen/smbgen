<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DemoReset extends Command
{
    protected $signature = 'app:demo-reset';

    protected $description = 'Reset demo data by re-running the DemoSeeder. Only runs when DEMO_MODE is enabled.';

    public function handle(): int
    {
        if (! config('app.demo_mode')) {
            $this->warn('Demo mode is not enabled. Skipping demo reset.');

            return self::SUCCESS;
        }

        $this->info('Resetting demo data...');

        Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--no-interaction' => true]);

        $this->info('Demo data reset successfully.');

        Log::info('Demo data reset completed via app:demo-reset command.');

        return self::SUCCESS;
    }
}
