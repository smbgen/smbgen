<?php

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\User;
use App\Services\LinkedIn\LinkedInService;
use Illuminate\Support\Facades\Http;

// ─── getAuthorizationUrl ──────────────────────────────────────────────────────

it('builds a valid linkedin authorization url', function () {
    config([
        'services.linkedin.client_id' => 'test-client-id',
        'services.linkedin.redirect' => 'http://localhost/callback',
    ]);

    $service = new LinkedInService;
    $url = $service->getAuthorizationUrl('random-state-123');

    expect($url)->toContain('https://www.linkedin.com/oauth/v2/authorization');
    expect($url)->toContain('client_id=test-client-id');
    expect($url)->toContain('state=random-state-123');
    expect($url)->toContain('w_member_social');
    expect($url)->toContain('w_organization_social');
});

// ─── exchangeCodeForToken ─────────────────────────────────────────────────────

it('exchanges code for token successfully', function () {
    Http::fake([
        'https://www.linkedin.com/oauth/v2/accessToken' => Http::response([
            'access_token' => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in' => 3600,
        ], 200),
    ]);

    config([
        'services.linkedin.client_id' => 'test-client-id',
        'services.linkedin.client_secret' => 'test-client-secret',
        'services.linkedin.redirect' => 'http://localhost/callback',
    ]);

    $service = new LinkedInService;
    $result = $service->exchangeCodeForToken('auth-code-123');

    expect($result['access_token'])->toBe('new-access-token');
    expect($result['refresh_token'])->toBe('new-refresh-token');
});

it('throws on token exchange failure', function () {
    Http::fake([
        'https://www.linkedin.com/oauth/v2/accessToken' => Http::response(['error' => 'invalid_grant'], 400),
    ]);

    config([
        'services.linkedin.client_id' => 'test-client-id',
        'services.linkedin.client_secret' => 'test-client-secret',
        'services.linkedin.redirect' => 'http://localhost/callback',
    ]);

    $service = new LinkedInService;
    expect(fn () => $service->exchangeCodeForToken('bad-code'))->toThrow(\Exception::class);
});

// ─── createPost ───────────────────────────────────────────────────────────────

it('creates a post on linkedin and returns post id', function () {
    Http::fake([
        'https://api.linkedin.com/v2/ugcPosts' => Http::response(
            ['id' => 'urn:li:ugcPost:12345'],
            201,
            ['x-restli-id' => 'urn:li:ugcPost:12345']
        ),
    ]);

    $user = User::factory()->create();
    $account = SocialAccount::factory()->create([
        'user_id' => $user->id,
        'platform' => 'linkedin',
        'page_id' => '98765',
        'credentials' => ['access_token' => 'valid-token'],
        'active' => true,
    ]);
    $post = SocialPost::factory()->create([
        'social_account_id' => $account->id,
        'user_id' => $user->id,
        'content' => 'Hello from tests!',
        'status' => 'scheduled',
    ]);

    $service = new LinkedInService;
    $linkedinId = $service->createPost($post);

    expect($linkedinId)->toBe('urn:li:ugcPost:12345');
});

// ─── refreshToken ─────────────────────────────────────────────────────────────

it('refreshes token and updates account', function () {
    Http::fake([
        'https://www.linkedin.com/oauth/v2/accessToken' => Http::response([
            'access_token' => 'refreshed-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in' => 3600,
        ], 200),
    ]);

    config([
        'services.linkedin.client_id' => 'test-client-id',
        'services.linkedin.client_secret' => 'test-client-secret',
    ]);

    $user = User::factory()->create();
    $account = SocialAccount::factory()->create([
        'user_id' => $user->id,
        'platform' => 'linkedin',
        'credentials' => ['access_token' => 'old-token', 'refresh_token' => 'old-refresh'],
        'active' => true,
    ]);

    $service = new LinkedInService;
    $service->refreshToken($account);

    $account->refresh();
    expect($account->getAccessToken())->toBe('refreshed-token');
});

it('throws when no refresh token available', function () {
    $user = User::factory()->create();
    $account = SocialAccount::factory()->create([
        'user_id' => $user->id,
        'platform' => 'linkedin',
        'credentials' => ['access_token' => 'only-access'],
    ]);

    $service = new LinkedInService;
    expect(fn () => $service->refreshToken($account))->toThrow(\Exception::class, 'No refresh token');
});
