<?php

namespace App\Services\Social\Adapters;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\SocialPostTarget;

/**
 * Contract every platform adapter must satisfy.
 * All adapters are responsible for:
 * - Validating platform-specific constraints before publish
 * - Posting text + media to the platform
 * - Refreshing tokens when needed
 * - Returning a normalised PublishResult
 */
interface SocialPlatformAdapter
{
    /**
     * Attempt to publish the post through the given account.
     *
     * On success, the adapter must populate `platform_post_id` and `platform_post_url`
     * on the returned result. On failure it must throw a PublishException.
     *
     * @throws \App\Services\Social\PublishException
     */
    public function publish(SocialPost $post, SocialPostTarget $target, SocialAccount $account): PublishResult;

    /**
     * Refresh the stored OAuth access token.
     *
     * @return bool True if the token was refreshed successfully.
     */
    public function refreshToken(SocialAccount $account): bool;

    /**
     * Validate the post content against platform-specific constraints.
     * Return an array of error messages. Empty array = valid.
     *
     * @return string[]
     */
    public function validate(SocialPost $post, SocialAccount $account): array;
}
