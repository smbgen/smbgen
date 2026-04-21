<?php

namespace App\Services\Social;

use App\Models\ClientFile;
use App\Models\CmsImage;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\SocialPostMedia;
use App\Models\SocialPostTarget;
use App\Models\SocialPublishAttempt;
use App\Services\Social\Adapters\LinkedInAdapter;
use App\Services\Social\Adapters\MetaAdapter;
use App\Services\Social\Adapters\SocialPlatformAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SocialMediaService
{
    /** @var array<string, SocialPlatformAdapter> */
    private array $adapters;

    public function __construct()
    {
        $meta = new MetaAdapter;
        $this->adapters = [
            SocialAccount::PLATFORM_FACEBOOK => $meta,
            SocialAccount::PLATFORM_INSTAGRAM => $meta,
            SocialAccount::PLATFORM_LINKEDIN => new LinkedInAdapter,
        ];
    }

    /** Retrieve the correct adapter for a platform. */
    public function adapterFor(string $platform): SocialPlatformAdapter
    {
        return $this->adapters[$platform]
            ?? throw new \InvalidArgumentException("No adapter registered for platform: {$platform}");
    }

    /**
     * Create a social post draft.
     *
     * @param  array{
     *   caption: string,
     *   account_ids: int[],
     *   scheduled_at?: \DateTimeInterface|null,
     *   requires_approval?: bool,
     *   source_type?: string|null,
     *   source_id?: int|null,
     * }  $data
     */
    public function createPost(int $userId, array $data): SocialPost
    {
        return DB::transaction(function () use ($userId, $data) {
            $post = SocialPost::create([
                'user_id' => $userId,
                'caption' => $data['caption'],
                'status' => SocialPost::STATUS_DRAFT,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'requires_approval' => $data['requires_approval'] ?? false,
            ]);

            // Create targets for each selected account
            foreach ($data['account_ids'] as $accountId) {
                SocialPostTarget::create([
                    'social_post_id' => $post->id,
                    'social_account_id' => $accountId,
                    'status' => SocialPostTarget::STATUS_PENDING,
                ]);
            }

            return $post;
        });
    }

    /**
     * Attach a CmsImage (job photo) as media on a post.
     */
    public function attachCmsImage(SocialPost $post, CmsImage $image, ?string $caption = null): SocialPostMedia
    {
        return SocialPostMedia::create([
            'social_post_id' => $post->id,
            'mediable_type' => CmsImage::class,
            'mediable_id' => $image->id,
            'mime_type' => $image->mime_type,
            'original_name' => $image->original_name,
            'caption' => $caption ?? $image->alt_text,
            'sort_order' => $post->media()->count(),
        ]);
    }

    /**
     * Attach a ClientFile as media on a post.
     */
    public function attachClientFile(SocialPost $post, ClientFile $file, ?string $caption = null): SocialPostMedia
    {
        return SocialPostMedia::create([
            'social_post_id' => $post->id,
            'mediable_type' => ClientFile::class,
            'mediable_id' => $file->id,
            'original_name' => $file->original_name ?? $file->filename,
            'caption' => $caption,
            'sort_order' => $post->media()->count(),
        ]);
    }

    /**
     * Schedule a draft post to go live at a given time.
     */
    public function schedule(SocialPost $post, \DateTimeInterface $scheduledAt): SocialPost
    {
        $post->update([
            'status' => SocialPost::STATUS_SCHEDULED,
            'scheduled_at' => $scheduledAt,
        ]);

        return $post->fresh();
    }

    /**
     * Approve a post that requires approval before publishing.
     */
    public function approve(SocialPost $post, int $approvedByUserId): SocialPost
    {
        $post->update([
            'approved_at' => now(),
            'approved_by' => $approvedByUserId,
        ]);

        return $post->fresh();
    }

    /**
     * Publish a post to a single target (called from the queued job).
     * Returns the updated target.
     */
    public function publishTarget(SocialPostTarget $target): SocialPostTarget
    {
        $target->load(['socialPost.media', 'socialAccount']);
        $post = $target->socialPost ?? throw new \RuntimeException("Social post not found for target {$target->id}");
        $account = $target->socialAccount ?? throw new \RuntimeException("Social account not found for target {$target->id}");

        $idempotencyKey = 'post-'.$post->id.'-target-'.$target->id.'-attempt-'.($target->attempt_count + 1);

        // Guard: already published successfully
        if ($target->status === SocialPostTarget::STATUS_PUBLISHED) {
            return $target;
        }

        // Guard: duplicate detection via idempotency key
        $existing = SocialPublishAttempt::where('idempotency_key', $idempotencyKey)
            ->where('status', SocialPublishAttempt::STATUS_PUBLISHED)
            ->exists();
        if ($existing) {
            Log::warning('[SocialMediaService] Skipping duplicate publish attempt', ['idempotency_key' => $idempotencyKey]);

            return $target;
        }

        $adapter = $this->adapterFor($account->platform);

        // Platform-specific validation
        $errors = $adapter->validate($post, $account);
        if (! empty($errors)) {
            $this->recordAttempt($target, $account->platform, false, null, 'VALIDATION_FAILED', implode('; ', $errors), $idempotencyKey);
            $target->update([
                'status' => SocialPostTarget::STATUS_FAILED,
                'last_error' => implode('; ', $errors),
                'attempt_count' => $target->attempt_count + 1,
                'last_attempted_at' => now(),
            ]);

            return $target->fresh();
        }

        // Mark as publishing
        $target->update([
            'status' => SocialPostTarget::STATUS_PUBLISHING,
            'attempt_count' => $target->attempt_count + 1,
            'last_attempted_at' => now(),
        ]);

        try {
            $result = $adapter->publish($post, $target, $account);

            $this->recordAttempt($target, $account->platform, true, $result->rawResponse, null, null, $idempotencyKey);

            $target->update([
                'status' => SocialPostTarget::STATUS_PUBLISHED,
                'platform_post_id' => $result->platformPostId,
                'platform_post_url' => $result->platformPostUrl,
                'last_error' => null,
                'published_at' => now(),
            ]);

            $account->update(['last_used_at' => now(), 'connection_status' => SocialAccount::STATUS_CONNECTED]);
        } catch (PublishException $e) {
            Log::error('[SocialMediaService] Publish failed', [
                'target_id' => $target->id,
                'platform' => $account->platform,
                'error' => $e->getMessage(),
                'code' => $e->errorCode,
            ]);

            $this->recordAttempt($target, $account->platform, false, $e->rawResponse, $e->errorCode, $e->getMessage(), $idempotencyKey);

            $target->update([
                'status' => SocialPostTarget::STATUS_FAILED,
                'last_error' => $e->getMessage(),
            ]);

            $account->markError($e->getMessage());
        }

        // Update parent post status
        $this->syncPostStatus($post);

        return $target->fresh();
    }

    /**
     * Re-queue a failed target for another attempt.
     */
    public function retryTarget(SocialPostTarget $target): void
    {
        if (! $target->canRetry()) {
            throw new \LogicException('This target has reached the maximum retry limit or is not in a failed state.');
        }

        $target->update(['status' => SocialPostTarget::STATUS_PENDING]);

        \App\Jobs\PublishSocialPostJob::dispatch($target->id);
    }

    /**
     * Cancel a scheduled post.
     */
    public function cancel(SocialPost $post): void
    {
        $post->update(['status' => SocialPost::STATUS_CANCELLED]);
        $post->targets()->where('status', SocialPostTarget::STATUS_PENDING)->update(['status' => SocialPostTarget::STATUS_SKIPPED]);
    }

    /**
     * Return CMS images as a paginated media picker source.
     */
    public function getJobPhotoMediaPicker(int $perPage = 24): \Illuminate\Pagination\LengthAwarePaginator
    {
        return CmsImage::orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Return client files eligible to use as social media images.
     */
    public function getClientFileMediaPicker(int $userId, int $perPage = 24): \Illuminate\Pagination\LengthAwarePaginator
    {
        return ClientFile::where('user_id', $userId)
            ->where('mime_type', 'like', 'image/%')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Aggregate delivery metrics for the status dashboard.
     *
     * @return array{total: int, published: int, failed: int, scheduled: int, draft: int}
     */
    public function getMetrics(int $userId): array
    {
        $q = SocialPost::where('user_id', $userId);

        return [
            'total' => (clone $q)->count(),
            'published' => (clone $q)->published()->count(),
            'failed' => (clone $q)->failed()->count(),
            'scheduled' => (clone $q)->scheduled()->count(),
            'draft' => (clone $q)->draft()->count(),
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function recordAttempt(
        SocialPostTarget $target,
        string $platform,
        bool $success,
        ?string $rawResponse,
        ?string $errorCode,
        ?string $errorMessage,
        string $idempotencyKey
    ): void {
        SocialPublishAttempt::create([
            'social_post_target_id' => $target->id,
            'status' => $success ? SocialPublishAttempt::STATUS_PUBLISHED : SocialPublishAttempt::STATUS_FAILED,
            'platform' => $platform,
            'response_body' => $rawResponse,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'idempotency_key' => $idempotencyKey,
            'attempted_at' => now(),
        ]);
    }

    private function syncPostStatus(SocialPost $post): void
    {
        $post->load('targets');
        $targets = $post->targets;

        if ($targets->every(fn ($t) => $t->status === SocialPostTarget::STATUS_PUBLISHED)) {
            $post->update(['status' => SocialPost::STATUS_PUBLISHED, 'published_at' => now()]);
        } elseif ($targets->every(fn ($t) => in_array($t->status, [SocialPostTarget::STATUS_FAILED, SocialPostTarget::STATUS_SKIPPED]))) {
            $post->update(['status' => SocialPost::STATUS_FAILED]);
        }
    }
}
