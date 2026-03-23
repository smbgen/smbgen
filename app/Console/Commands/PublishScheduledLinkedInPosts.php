<?php

namespace App\Console\Commands;

use App\Models\SocialPost;
use App\Services\LinkedIn\LinkedInService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledLinkedInPosts extends Command
{
    protected $signature = 'linkedin:publish-scheduled';

    protected $description = 'Publish LinkedIn posts that are scheduled and due for publishing';

    public function handle(LinkedInService $linkedIn): int
    {
        $posts = SocialPost::dueForPublishing()
            ->with('socialAccount')
            ->get();

        if ($posts->isEmpty()) {
            return self::SUCCESS;
        }

        $this->info("Found {$posts->count()} post(s) to publish.");

        foreach ($posts as $post) {
            $this->publishPost($post, $linkedIn);
        }

        return self::SUCCESS;
    }

    private function publishPost(SocialPost $post, LinkedInService $linkedIn): void
    {
        $account = $post->socialAccount;

        if (! $account || ! $account->active) {
            $this->markFailed($post, 'Social account is inactive or missing.');
            return;
        }

        if ($account->isTokenExpired()) {
            try {
                $linkedIn->refreshToken($account);
                $account->refresh();
            } catch (\Exception $e) {
                $this->markFailed($post, 'Token refresh failed: ' . $e->getMessage());
                return;
            }
        }

        try {
            $linkedinPostId = $linkedIn->createPost($post);

            $post->update([
                'status' => SocialPost::STATUS_PUBLISHED,
                'published_at' => now(),
                'linkedin_post_id' => $linkedinPostId,
                'error_message' => null,
            ]);

            $this->info("  ✓ Post #{$post->id} published (LinkedIn ID: {$linkedinPostId})");

            Log::info('Scheduled LinkedIn post published', [
                'post_id' => $post->id,
                'account_id' => $account->id,
                'linkedin_post_id' => $linkedinPostId,
            ]);

        } catch (\Exception $e) {
            $this->markFailed($post, $e->getMessage());
        }
    }

    private function markFailed(SocialPost $post, string $reason): void
    {
        $post->update([
            'status' => SocialPost::STATUS_FAILED,
            'error_message' => $reason,
        ]);

        $this->warn("  ✗ Post #{$post->id} failed: {$reason}");

        Log::error('Scheduled LinkedIn post failed to publish', [
            'post_id' => $post->id,
            'reason' => $reason,
        ]);
    }
}
