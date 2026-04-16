<?php

namespace App\Console\Commands;

use App\Jobs\PublishSocialPostJob;
use App\Models\SocialPost;
use App\Models\SocialPostTarget;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledSocialPostsCommand extends Command
{
    protected $signature = 'social:publish-scheduled';

    protected $description = 'Dispatch jobs for social media posts that are due to be published.';

    public function handle(): int
    {
        $due = SocialPost::due()->with('targets')->get();

        if ($due->isEmpty()) {
            $this->info('No social posts due for publishing.');

            return self::SUCCESS;
        }

        $dispatched = 0;

        foreach ($due as $post) {
            // Skip posts that still require approval
            if ($post->requires_approval && ! $post->approved_at) {
                $this->warn("Post #{$post->id} requires approval – skipping.");

                continue;
            }

            // Mark the post as publishing so we don't double-dispatch
            $post->update(['status' => SocialPost::STATUS_PUBLISHING]);

            foreach ($post->targets as $target) {
                if ($target->status === SocialPostTarget::STATUS_PENDING) {
                    PublishSocialPostJob::dispatch($target->id);
                    $dispatched++;
                }
            }
        }

        $this->info("Dispatched {$dispatched} publish job(s) for {$due->count()} post(s).");
        Log::info('[social:publish-scheduled] Dispatch complete', ['posts' => $due->count(), 'jobs' => $dispatched]);

        return self::SUCCESS;
    }
}
