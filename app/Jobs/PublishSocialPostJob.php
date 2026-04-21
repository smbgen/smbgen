<?php

namespace App\Jobs;

use App\Models\SocialPostTarget;
use App\Services\Social\SocialMediaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishSocialPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maximum attempts before giving up. */
    public int $tries = 3;

    /** Exponential back-off in seconds (5 min, 15 min, 45 min). */
    public array $backoff = [300, 900, 2700];

    public function __construct(public readonly int $targetId) {}

    public function handle(SocialMediaService $service): void
    {
        $target = SocialPostTarget::with(['socialPost', 'socialAccount'])->find($this->targetId);

        if (! $target) {
            Log::warning('[PublishSocialPostJob] Target not found', ['target_id' => $this->targetId]);

            return;
        }

        if ($target->status === SocialPostTarget::STATUS_PUBLISHED) {
            Log::info('[PublishSocialPostJob] Target already published – skipping', ['target_id' => $this->targetId]);

            return;
        }

        Log::info('[PublishSocialPostJob] Publishing target', [
            'target_id' => $this->targetId,
            'platform' => $target->socialAccount?->platform,
            'post_id' => $target->social_post_id,
        ]);

        $service->publishTarget($target);
    }

    /** On final failure, mark the target as failed in DB. */
    public function failed(\Throwable $exception): void
    {
        Log::error('[PublishSocialPostJob] Job exhausted all retries', [
            'target_id' => $this->targetId,
            'error' => $exception->getMessage(),
        ]);

        $target = SocialPostTarget::find($this->targetId);
        if ($target && $target->status !== SocialPostTarget::STATUS_PUBLISHED) {
            $target->update([
                'status' => SocialPostTarget::STATUS_FAILED,
                'last_error' => $exception->getMessage(),
            ]);
        }
    }
}
