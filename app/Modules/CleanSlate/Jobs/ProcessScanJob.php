<?php

namespace App\Modules\CleanSlate\Jobs;

use App\Modules\CleanSlate\Models\ScanJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ScanJob $scanJob) {}

    public function handle(): void
    {
        // TODO: run browser-based scan against $this->scanJob->dataBroker
        Log::info("ProcessScanJob: stub for scan_job {$this->scanJob->id}");
    }
}
