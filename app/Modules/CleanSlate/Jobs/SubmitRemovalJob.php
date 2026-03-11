<?php

namespace App\Modules\CleanSlate\Jobs;

use App\Modules\CleanSlate\Models\RemovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubmitRemovalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public RemovalRequest $removalRequest) {}

    public function handle(): void
    {
        // TODO: submit opt-out request to $this->removalRequest->dataBroker
        Log::info("SubmitRemovalJob: stub for removal_request {$this->removalRequest->id}");
    }
}
