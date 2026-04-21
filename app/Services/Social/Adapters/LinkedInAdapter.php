<?php

namespace App\Services\Social\Adapters;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\SocialPostTarget;
use App\Services\Social\PublishException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Adapter for LinkedIn Pages / Member profile posting via the LinkedIn API v2.
 *
 * Supported post type: Share (text + optional single image)
 * Endpoint: POST https://api.linkedin.com/v2/ugcPosts
 */
class LinkedInAdapter implements SocialPlatformAdapter
{
    const API_BASE = 'https://api.linkedin.com/v2';

    const MAX_COMMENTARY_LENGTH = 3000;

    /** @return string[] */
    public function validate(SocialPost $post, SocialAccount $account): array
    {
        $errors = [];

        if (empty(trim($post->caption))) {
            $errors[] = 'Caption is required for LinkedIn posts.';
        }

        if (mb_strlen($post->caption) > self::MAX_COMMENTARY_LENGTH) {
            $errors[] = sprintf(
                'LinkedIn commentary exceeds the limit of %d characters.',
                self::MAX_COMMENTARY_LENGTH
            );
        }

        if ($post->media->count() > 9) {
            $errors[] = 'LinkedIn supports at most 9 images per post.';
        }

        return $errors;
    }

    public function refreshToken(SocialAccount $account): bool
    {
        // LinkedIn access tokens can be refreshed using the refresh_token grant.
        if (empty($account->refresh_token)) {
            Log::warning('[LinkedInAdapter] No refresh token available', ['account_id' => $account->id]);

            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $account->refresh_token,
                'client_id' => config('services.linkedin.client_id'),
                'client_secret' => config('services.linkedin.client_secret'),
            ]);

            if (! $response->successful()) {
                Log::warning('[LinkedInAdapter] Token refresh failed', ['body' => $response->body()]);

                return false;
            }

            $account->update([
                'access_token' => $response->json('access_token'),
                'refresh_token' => $response->json('refresh_token', $account->refresh_token),
                'token_expires_at' => now()->addSeconds($response->json('expires_in', 5183999)),
                'connection_status' => SocialAccount::STATUS_CONNECTED,
                'last_error' => null,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('[LinkedInAdapter] Exception during token refresh', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function publish(SocialPost $post, SocialPostTarget $target, SocialAccount $account): PublishResult
    {
        if ($account->isTokenExpired()) {
            if (! $this->refreshToken($account)) {
                throw new PublishException('LinkedIn access token is expired and could not be refreshed.', 'TOKEN_EXPIRED');
            }

            $account->refresh();
        }

        $authorUrn = $account->platform_page_id
            ? "urn:li:organization:{$account->platform_page_id}"
            : "urn:li:person:{$account->platform_user_id}";

        $mediaItems = $post->media->filter(fn ($m) => $m->getUrl())->values();

        $body = [
            'author' => $authorUrn,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $post->caption,
                    ],
                    'shareMediaCategory' => $mediaItems->isEmpty() ? 'NONE' : 'IMAGE',
                    'media' => $mediaItems->map(fn ($m) => $this->buildMediaObject($m, $account))->toArray(),
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        $response = Http::withToken($account->access_token)
            ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
            ->post(self::API_BASE.'/ugcPosts', $body);

        $responseBody = $response->body();

        if (! $response->successful()) {
            $errorMessage = $response->json('message') ?? 'LinkedIn publish failed.';
            $errorCode = $response->json('status') ?? 'LINKEDIN_API_ERROR';
            throw new PublishException($errorMessage, (string) $errorCode, $responseBody);
        }

        // LinkedIn returns the URN in X-RestLi-Id header
        $headerId = $response->header('X-RestLi-Id');
        $postId = $headerId !== '' ? $headerId : (string) ($response->json('id') ?? 'unknown');

        return PublishResult::ok(
            platformPostId: $postId,
            platformPostUrl: null, // LinkedIn does not return a URL from ugcPosts
            rawResponse: $responseBody,
        );
    }

    private function buildMediaObject(\App\Models\SocialPostMedia $media, SocialAccount $account): array
    {
        $assetUrn = $this->uploadImage($media->getUrl(), $account->access_token, $account->platform_user_id ?? $account->platform_page_id ?? '');

        return [
            'status' => 'READY',
            'media' => $assetUrn,
            'title' => ['text' => $media->caption ?? ''],
        ];
    }

    private function uploadImage(string $imageUrl, string $token, string $personUrn): string
    {
        // Step 1: Register upload
        $registerResponse = Http::withToken($token)
            ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
            ->post(self::API_BASE.'/assets?action=registerUpload', [
                'registerUploadRequest' => [
                    'recipes' => ['urn:li:digitalmediaRecipe:feedshare-image'],
                    'owner' => str_starts_with($personUrn, 'urn:') ? $personUrn : "urn:li:person:{$personUrn}",
                    'serviceRelationships' => [
                        [
                            'relationshipType' => 'OWNER',
                            'identifier' => 'urn:li:userGeneratedContent',
                        ],
                    ],
                ],
            ]);

        $uploadUrl = $registerResponse->json('value.uploadMechanism.com\.linkedin\.digitalmedia\.uploading\.MediaUploadHttpRequest.uploadUrl');
        $assetUrn = $registerResponse->json('value.asset');

        if (! $uploadUrl || ! $assetUrn) {
            throw new PublishException('LinkedIn image upload registration failed.', 'UPLOAD_REGISTER_FAILED', $registerResponse->body());
        }

        // Step 2: Upload binary
        $imageContents = file_get_contents($imageUrl);
        if ($imageContents === false) {
            throw new PublishException("Could not fetch image for LinkedIn upload: {$imageUrl}", 'IMAGE_FETCH_FAILED');
        }

        Http::withToken($token)
            ->withBody($imageContents, 'application/octet-stream')
            ->put($uploadUrl);

        return $assetUrn;
    }
}
