<?php

namespace App\Services\LinkedIn;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkedInService
{
    const API_BASE = 'https://api.linkedin.com/v2';
    const AUTH_URL = 'https://www.linkedin.com/oauth/v2/authorization';
    const TOKEN_URL = 'https://www.linkedin.com/oauth/v2/accessToken';

    const SCOPES = [
        'openid',
        'profile',
        'email',
        'w_member_social',
        'r_organization_social',
        'w_organization_social',
    ];

    public function getAuthorizationUrl(string $state): string
    {
        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.linkedin.client_id'),
            'redirect_uri' => config('services.linkedin.redirect'),
            'state' => $state,
            'scope' => implode(' ', self::SCOPES),
        ]);

        return self::AUTH_URL . '?' . $params;
    }

    public function exchangeCodeForToken(string $code): array
    {
        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.linkedin.redirect'),
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
        ]);

        if (! $response->successful()) {
            throw new \Exception('LinkedIn token exchange failed: ' . $response->body());
        }

        return $response->json();
    }

    public function getMemberProfile(string $accessToken): array
    {
        $response = $this->client($accessToken)->get(self::API_BASE . '/userinfo');

        if (! $response->successful()) {
            throw new \Exception('LinkedIn profile fetch failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get LinkedIn organizations (pages) that the user manages.
     */
    public function getOrganizations(SocialAccount $account): array
    {
        $accessToken = $account->getAccessToken();

        $response = $this->client($accessToken)->get(self::API_BASE . '/organizationAcls', [
            'q' => 'roleAssignee',
            'role' => 'ADMINISTRATOR',
            'projection' => '(elements*(organization~(id,localizedName,logoV2(original~:playableStreams))))',
        ]);

        if (! $response->successful()) {
            Log::error('LinkedIn get organizations failed', ['response' => $response->body()]);

            return [];
        }

        return $response->json('elements', []);
    }

    /**
     * Create a text post on a LinkedIn organization page.
     */
    public function createPost(SocialPost $post): string
    {
        $account = $post->socialAccount;
        $accessToken = $account->getAccessToken();

        $payload = [
            'author' => 'urn:li:organization:' . $account->page_id,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $post->content,
                    ],
                    'shareMediaCategory' => 'NONE',
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        // Attach media if present
        if (! empty($post->media_paths)) {
            $mediaAssets = $this->uploadMediaAssets($account, $post->media_paths);
            if (! empty($mediaAssets)) {
                $payload['specificContent']['com.linkedin.ugc.ShareContent']['shareMediaCategory'] = 'IMAGE';
                $payload['specificContent']['com.linkedin.ugc.ShareContent']['media'] = $mediaAssets;
            }
        }

        $response = $this->client($accessToken)
            ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
            ->post(self::API_BASE . '/ugcPosts', $payload);

        if (! $response->successful()) {
            throw new \Exception('LinkedIn post creation failed: ' . $response->body());
        }

        // Extract post ID from response header or body
        $postId = $response->header('x-restli-id') ?? $response->json('id') ?? '';

        return $postId;
    }

    /**
     * Register and upload media assets to LinkedIn, returning media asset URNs.
     */
    private function uploadMediaAssets(SocialAccount $account, array $mediaPaths): array
    {
        $assets = [];

        foreach ($mediaPaths as $path) {
            try {
                $registerResponse = $this->client($account->getAccessToken())
                    ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
                    ->post(self::API_BASE . '/assets?action=registerUpload', [
                        'registerUploadRequest' => [
                            'recipes' => ['urn:li:digitalmediaRecipe:feedshare-image'],
                            'owner' => 'urn:li:organization:' . $account->page_id,
                            'serviceRelationships' => [[
                                'relationshipType' => 'OWNER',
                                'identifier' => 'urn:li:userGeneratedContent',
                            ]],
                        ],
                    ]);

                if (! $registerResponse->successful()) {
                    continue;
                }

                $uploadUrl = $registerResponse->json('value.uploadMechanism.com\.linkedin\.digitalmedia\.uploading\.MediaUploadHttpRequest.uploadUrl');
                $assetUrn = $registerResponse->json('value.asset');

                // Upload the actual file
                $fullPath = storage_path('app/public/' . ltrim($path, '/'));
                if (file_exists($fullPath)) {
                    Http::withHeaders([
                        'Authorization' => 'Bearer ' . $account->getAccessToken(),
                        'Content-Type' => 'application/octet-stream',
                    ])->put($uploadUrl, file_get_contents($fullPath));
                }

                $assets[] = [
                    'status' => 'READY',
                    'media' => $assetUrn,
                ];
            } catch (\Exception $e) {
                Log::warning('LinkedIn media upload failed', ['path' => $path, 'error' => $e->getMessage()]);
            }
        }

        return $assets;
    }

    /**
     * Delete a post from LinkedIn.
     */
    public function deletePost(SocialAccount $account, string $linkedinPostId): bool
    {
        $response = $this->client($account->getAccessToken())
            ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
            ->delete(self::API_BASE . '/ugcPosts/' . urlencode($linkedinPostId));

        return $response->successful();
    }

    /**
     * Refresh access token using refresh token.
     */
    public function refreshToken(SocialAccount $account): void
    {
        $credentials = $account->credentials ?? [];
        $refreshToken = $credentials['refresh_token'] ?? null;

        if (! $refreshToken) {
            throw new \Exception('No refresh token available for account ' . $account->id);
        }

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
        ]);

        if (! $response->successful()) {
            throw new \Exception('LinkedIn token refresh failed: ' . $response->body());
        }

        $data = $response->json();

        $account->update([
            'credentials' => array_merge($credentials, [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'] ?? $refreshToken,
            ]),
            'access_token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
        ]);
    }

    private function client(string $accessToken): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ]);
    }
}
