<?php

namespace App\Jobs;

use App\Enums\SocialPostStatus;
use App\Models\SocialPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishSocialPostJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly SocialPost $post)
    {
        $this->onQueue('signal');
    }

    public function handle(): void
    {
        // TODO: integrate platform-specific API (LinkedIn, Instagram, X, Facebook)
        // For now, mark as published to demonstrate the queue pipeline is wired.
        $this->post->update([
            'status' => SocialPostStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->post->update([
            'status' => SocialPostStatus::Failed,
            'failure_reason' => $exception->getMessage(),
        ]);
    }
}
