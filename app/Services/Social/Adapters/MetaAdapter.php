<?php

namespace App\Services\Social\Adapters;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\SocialPostTarget;
use App\Services\Social\PublishException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Adapter for Facebook Pages and Instagram Business accounts via the Meta Graph API.
 *
 * Facebook: POST /{page-id}/feed  (with optional attached_media)
 * Instagram: POST /{ig-user-id}/media → /{ig-user-id}/media_publish
 *
 * Both platforms share the same page_access_token stored on the SocialAccount.
 */
class MetaAdapter implements SocialPlatformAdapter
{
    const GRAPH_BASE = 'https://graph.facebook.com/v19.0';

    const MAX_CAPTION_LENGTH = 63206; // Facebook feed post limit

    /** @return string[] */
    public function validate(SocialPost $post, SocialAccount $account): array
    {
        $errors = [];

        if (empty(trim($post->caption))) {
            $errors[] = 'Caption is required for Meta posts.';
        }

        if (mb_strlen($post->caption) > self::MAX_CAPTION_LENGTH) {
            $errors[] = sprintf(
                'Caption exceeds the Meta limit of %d characters.',
                self::MAX_CAPTION_LENGTH
            );
        }

        $mediaCount = $post->media->count();
        if ($account->platform === SocialAccount::PLATFORM_INSTAGRAM && $mediaCount === 0) {
            $errors[] = 'Instagram posts require at least one image or video.';
        }

        if ($mediaCount > 10) {
            $errors[] = 'Meta posts support at most 10 media items.';
        }

        return $errors;
    }

    public function refreshToken(SocialAccount $account): bool
    {
        // Meta page access tokens are long-lived (60 days).
        // We refresh by exchanging the user access token via the /{page-id} endpoint.
        if (empty($account->access_token)) {
            return false;
        }

        try {
            $response = Http::get(self::GRAPH_BASE.'/me/accounts', [
                'access_token' => $account->access_token,
            ]);

            if (! $response->successful()) {
                Log::warning('[MetaAdapter] Token refresh failed', ['body' => $response->body()]);

                return false;
            }

            $pages = collect($response->json('data', []));
            $page = $pages->firstWhere('id', $account->platform_page_id);

            if ($page && ! empty($page['access_token'])) {
                $account->update([
                    'page_access_token' => $page['access_token'],
                    'connection_status' => SocialAccount::STATUS_CONNECTED,
                    'last_error' => null,
                ]);

                return true;
            }
        } catch (\Throwable $e) {
            Log::error('[MetaAdapter] Exception during token refresh', ['error' => $e->getMessage()]);
        }

        return false;
    }

    public function publish(SocialPost $post, SocialPostTarget $target, SocialAccount $account): PublishResult
    {
        return match ($account->platform) {
            SocialAccount::PLATFORM_FACEBOOK => $this->publishToFacebook($post, $target, $account),
            SocialAccount::PLATFORM_INSTAGRAM => $this->publishToInstagram($post, $target, $account),
            default => throw new PublishException('Unsupported Meta platform: '.$account->platform),
        };
    }

    private function publishToFacebook(SocialPost $post, SocialPostTarget $target, SocialAccount $account): PublishResult
    {
        $token = $account->page_access_token ?: $account->access_token;
        if (empty($token)) {
            throw new PublishException('No page access token available for Facebook account.', 'NO_TOKEN');
        }

        $pageId = $account->platform_page_id ?: $account->platform_user_id;
        if (empty($pageId)) {
            throw new PublishException('No page ID configured for Facebook account.', 'NO_PAGE_ID');
        }

        $mediaItems = $post->media->map(fn ($m) => $m->getUrl())->filter()->values();

        $payload = ['message' => $post->caption, 'access_token' => $token];

        // Attach photos when present (multi-photo via attached_media)
        if ($mediaItems->isNotEmpty()) {
            $photoIds = $this->uploadFacebookPhotos($pageId, $token, $mediaItems->all());
            if (count($photoIds) === 1) {
                $payload['attached_media'] = [['media_fbid' => $photoIds[0]]];
            } elseif (count($photoIds) > 1) {
                $payload['attached_media'] = array_map(fn ($id) => ['media_fbid' => $id], $photoIds);
            }
        }

        $response = Http::post(self::GRAPH_BASE."/{$pageId}/feed", $payload);
        $body = $response->body();

        if (! $response->successful() || $response->json('error')) {
            $error = $response->json('error', []);
            throw new PublishException(
                $error['message'] ?? 'Facebook publish failed.',
                (string) ($error['code'] ?? 'GRAPH_ERROR'),
                $body,
            );
        }

        $postId = $response->json('id');

        return PublishResult::ok(
            platformPostId: $postId,
            platformPostUrl: "https://www.facebook.com/{$postId}",
            rawResponse: $body,
        );
    }

    /** @param  string[]  $urls */
    private function uploadFacebookPhotos(string $pageId, string $token, array $urls): array
    {
        $ids = [];
        foreach ($urls as $url) {
            $r = Http::post(self::GRAPH_BASE."/{$pageId}/photos", [
                'url' => $url,
                'published' => false,
                'access_token' => $token,
            ]);

            if ($r->successful() && $r->json('id')) {
                $ids[] = $r->json('id');
            }
        }

        return $ids;
    }

    private function publishToInstagram(SocialPost $post, SocialPostTarget $target, SocialAccount $account): PublishResult
    {
        $token = $account->page_access_token ?: $account->access_token;
        if (empty($token)) {
            throw new PublishException('No access token available for Instagram account.', 'NO_TOKEN');
        }

        $igUserId = $account->platform_page_id ?: $account->platform_user_id;
        if (empty($igUserId)) {
            throw new PublishException('No Instagram Business Account ID configured.', 'NO_IG_USER_ID');
        }

        $mediaItems = $post->media->filter(fn ($m) => $m->getUrl() !== null)->values();

        if ($mediaItems->isEmpty()) {
            throw new PublishException('Instagram requires at least one image.', 'NO_MEDIA');
        }

        if ($mediaItems->count() === 1) {
            return $this->publishIgSingle($igUserId, $token, $post->caption, $mediaItems->first());
        }

        return $this->publishIgCarousel($igUserId, $token, $post->caption, $mediaItems->all());
    }

    private function publishIgSingle(string $igUserId, string $token, string $caption, \App\Models\SocialPostMedia $media): PublishResult
    {
        $containerPayload = [
            'caption' => $caption,
            'access_token' => $token,
        ];

        if ($media->isVideo()) {
            $containerPayload['media_type'] = 'VIDEO';
            $containerPayload['video_url'] = $media->getUrl();
        } else {
            $containerPayload['image_url'] = $media->getUrl();
        }

        $r = Http::post(self::GRAPH_BASE."/{$igUserId}/media", $containerPayload);

        if (! $r->successful() || $r->json('error')) {
            $err = $r->json('error', []);
            throw new PublishException($err['message'] ?? 'IG container creation failed.', (string) ($err['code'] ?? 'IG_CONTAINER_ERROR'), $r->body());
        }

        return $this->igPublishContainer($igUserId, $token, $r->json('id'));
    }

    /** @param  \App\Models\SocialPostMedia[]  $mediaItems */
    private function publishIgCarousel(string $igUserId, string $token, string $caption, array $mediaItems): PublishResult
    {
        $childIds = [];
        foreach ($mediaItems as $media) {
            $p = ['is_carousel_item' => true, 'access_token' => $token];
            if ($media->isVideo()) {
                $p['media_type'] = 'VIDEO';
                $p['video_url'] = $media->getUrl();
            } else {
                $p['image_url'] = $media->getUrl();
            }

            $r = Http::post(self::GRAPH_BASE."/{$igUserId}/media", $p);
            if ($r->successful() && $r->json('id')) {
                $childIds[] = $r->json('id');
            }
        }

        $r = Http::post(self::GRAPH_BASE."/{$igUserId}/media", [
            'media_type' => 'CAROUSEL',
            'children' => implode(',', $childIds),
            'caption' => $caption,
            'access_token' => $token,
        ]);

        if (! $r->successful() || $r->json('error')) {
            $err = $r->json('error', []);
            throw new PublishException($err['message'] ?? 'IG carousel container failed.', (string) ($err['code'] ?? 'IG_CAROUSEL_ERROR'), $r->body());
        }

        return $this->igPublishContainer($igUserId, $token, $r->json('id'));
    }

    private function igPublishContainer(string $igUserId, string $token, string $containerId): PublishResult
    {
        $r = Http::post(self::GRAPH_BASE."/{$igUserId}/media_publish", [
            'creation_id' => $containerId,
            'access_token' => $token,
        ]);

        $body = $r->body();

        if (! $r->successful() || $r->json('error')) {
            $err = $r->json('error', []);
            throw new PublishException($err['message'] ?? 'IG publish failed.', (string) ($err['code'] ?? 'IG_PUBLISH_ERROR'), $body);
        }

        $mediaId = $r->json('id');

        return PublishResult::ok(
            platformPostId: $mediaId,
            platformPostUrl: "https://www.instagram.com/p/{$mediaId}/",
            rawResponse: $body,
        );
    }
}
