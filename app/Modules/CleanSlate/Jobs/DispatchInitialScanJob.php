<?php

namespace App\Modules\CleanSlate\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchInitialScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        // TODO: fan out ProcessScanJob per broker for $this->user
        Log::info("DispatchInitialScanJob: stub for user {$this->user->id}");
    }
}
