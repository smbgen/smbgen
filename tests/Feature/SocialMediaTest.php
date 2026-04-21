<?php

use App\Jobs\PublishSocialPostJob;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\SocialPostTarget;
use App\Models\User;
use App\Services\Social\Adapters\PublishResult;
use App\Services\Social\Adapters\SocialPlatformAdapter;
use App\Services\Social\SocialMediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

// ---------------------------------------------------------------------------
// SocialAccount CRUD
// ---------------------------------------------------------------------------

it('admin can list social accounts', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    SocialAccount::factory()->count(3)->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->get('/admin/social-media/accounts')
        ->assertOk()
        ->assertSee('Connected Accounts');
});

it('admin can create a social account', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);

    $this->actingAs($admin)
        ->post('/admin/social-media/accounts', [
            'platform' => 'linkedin',
            'account_name' => 'My Company',
            'account_url' => 'https://www.linkedin.com/company/my-company',
        ])
        ->assertRedirect('/admin/social-media/accounts');

    $this->assertDatabaseHas('social_accounts', [
        'platform' => 'linkedin',
        'account_name' => 'My Company',
        'connection_status' => SocialAccount::STATUS_CONNECTED,
    ]);
});

it('rejects unsupported platform on account creation', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);

    $this->actingAs($admin)
        ->post('/admin/social-media/accounts', [
            'platform' => 'tiktok',
            'account_name' => 'My TikTok',
        ])
        ->assertSessionHasErrors('platform');
});

it('admin can toggle a social account', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id, 'active' => true]);

    $this->actingAs($admin)
        ->patch("/admin/social-media/accounts/{$account->id}/toggle")
        ->assertRedirect();

    expect($account->fresh()->active)->toBeFalse();
});

it('admin can delete a social account', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->delete("/admin/social-media/accounts/{$account->id}")
        ->assertRedirect('/admin/social-media/accounts');

    $this->assertDatabaseMissing('social_accounts', ['id' => $account->id]);
});

it('non-admin cannot access social accounts', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($user)
        ->get('/admin/social-media/accounts')
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// SocialPost draft → schedule flow
// ---------------------------------------------------------------------------

it('admin can create a draft post', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->post('/admin/social-media/posts', [
            'caption' => 'Hello world! This is a test post.',
            'account_ids' => [$account->id],
        ])
        ->assertRedirect('/admin/social-media/posts');

    $this->assertDatabaseHas('social_posts', [
        'caption' => 'Hello world! This is a test post.',
        'status' => SocialPost::STATUS_DRAFT,
        'user_id' => $admin->id,
    ]);

    $post = SocialPost::where('user_id', $admin->id)->first();
    $this->assertDatabaseHas('social_post_targets', [
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
        'status' => SocialPostTarget::STATUS_PENDING,
    ]);
});

it('admin can schedule a post', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);
    $scheduledAt = now()->addHour()->format('Y-m-d\TH:i');

    $this->actingAs($admin)
        ->post('/admin/social-media/posts', [
            'caption' => 'Scheduled post content.',
            'account_ids' => [$account->id],
            'scheduled_at' => $scheduledAt,
        ])
        ->assertRedirect('/admin/social-media/posts');

    $this->assertDatabaseHas('social_posts', [
        'status' => SocialPost::STATUS_SCHEDULED,
    ]);
});

it('scheduled_at must be in the future', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->post('/admin/social-media/posts', [
            'caption' => 'Past time post.',
            'account_ids' => [$account->id],
            'scheduled_at' => now()->subHour()->format('Y-m-d\TH:i'),
        ])
        ->assertSessionHasErrors('scheduled_at');
});

it('admin can view a post', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $post = SocialPost::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->get("/admin/social-media/posts/{$post->id}")
        ->assertOk();
});

it('admin can cancel a scheduled post', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);
    $post = SocialPost::factory()->scheduled()->create(['user_id' => $admin->id]);
    SocialPostTarget::factory()->create([
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
        'status' => SocialPostTarget::STATUS_PENDING,
    ]);

    $this->actingAs($admin)
        ->post("/admin/social-media/posts/{$post->id}/cancel")
        ->assertRedirect();

    expect($post->fresh()->status)->toBe(SocialPost::STATUS_CANCELLED);
});

it('admin can delete a draft post', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $post = SocialPost::factory()->draft()->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->delete("/admin/social-media/posts/{$post->id}")
        ->assertRedirect('/admin/social-media/posts');

    $this->assertDatabaseMissing('social_posts', ['id' => $post->id]);
});

it('admin can approve a post requiring approval', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $post = SocialPost::factory()->scheduled()->requiresApproval()->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->post("/admin/social-media/posts/{$post->id}/approve")
        ->assertRedirect();

    expect($post->fresh()->approved_at)->not->toBeNull();
    expect($post->fresh()->approved_by)->toBe($admin->id);
});

// ---------------------------------------------------------------------------
// SocialMediaService – publish flow
// ---------------------------------------------------------------------------

it('service creates post with targets', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);

    $service = new SocialMediaService;
    $post = $service->createPost($admin->id, [
        'caption' => 'Service test post',
        'account_ids' => [$account->id],
    ]);

    expect($post->caption)->toBe('Service test post');
    expect($post->targets)->toHaveCount(1);
    expect($post->targets->first()->social_account_id)->toBe($account->id);
});

it('service publishes a target successfully', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->facebook()->create(['user_id' => $admin->id]);
    $post = SocialPost::factory()->create(['user_id' => $admin->id]);
    $target = SocialPostTarget::factory()->create([
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
        'status' => SocialPostTarget::STATUS_PENDING,
    ]);

    // Mock the adapter to return a successful result
    $mockAdapter = Mockery::mock(SocialPlatformAdapter::class);
    $mockAdapter->shouldReceive('validate')->andReturn([]);
    $mockAdapter->shouldReceive('publish')->andReturn(
        PublishResult::ok('1234567890', 'https://www.facebook.com/1234567890')
    );

    $service = Mockery::mock(SocialMediaService::class)->makePartial();
    $service->shouldReceive('adapterFor')->andReturn($mockAdapter);

    $updatedTarget = $service->publishTarget($target);

    expect($updatedTarget->status)->toBe(SocialPostTarget::STATUS_PUBLISHED);
});

it('service marks target as failed on publish exception', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->facebook()->create(['user_id' => $admin->id]);
    $post = SocialPost::factory()->create(['user_id' => $admin->id]);
    $target = SocialPostTarget::factory()->create([
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
        'status' => SocialPostTarget::STATUS_PENDING,
    ]);

    $mockAdapter = Mockery::mock(SocialPlatformAdapter::class);
    $mockAdapter->shouldReceive('validate')->andReturn([]);
    $mockAdapter->shouldReceive('publish')->andThrow(
        new \App\Services\Social\PublishException('API rate limit exceeded.', 'RATE_LIMIT')
    );

    $service = Mockery::mock(SocialMediaService::class)->makePartial();
    $service->shouldReceive('adapterFor')->andReturn($mockAdapter);

    $updatedTarget = $service->publishTarget($target);

    expect($updatedTarget->status)->toBe(SocialPostTarget::STATUS_FAILED);
    expect($updatedTarget->last_error)->toContain('rate limit');
});

it('service skips already published target', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);
    $post = SocialPost::factory()->create(['user_id' => $admin->id]);
    $target = SocialPostTarget::factory()->create([
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
        'status' => SocialPostTarget::STATUS_PUBLISHED,
        'platform_post_id' => 'abc123',
    ]);

    $mockAdapter = Mockery::mock(SocialPlatformAdapter::class);
    $mockAdapter->shouldNotReceive('publish');

    $service = Mockery::mock(SocialMediaService::class)->makePartial();
    $service->shouldReceive('adapterFor')->andReturn($mockAdapter);

    $result = $service->publishTarget($target);

    expect($result->status)->toBe(SocialPostTarget::STATUS_PUBLISHED);
});

it('service marks failed target as retriable when under retry limit', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);
    $post = SocialPost::factory()->create(['user_id' => $admin->id]);
    $target = SocialPostTarget::factory()->create([
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
        'status' => SocialPostTarget::STATUS_FAILED,
        'attempt_count' => 1,
    ]);

    expect($target->canRetry())->toBeTrue();
});

it('service does not allow retry when attempt limit reached', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);
    $post = SocialPost::factory()->create(['user_id' => $admin->id]);
    $target = SocialPostTarget::factory()->create([
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
        'status' => SocialPostTarget::STATUS_FAILED,
        'attempt_count' => 3,
    ]);

    expect($target->canRetry())->toBeFalse();
});

// ---------------------------------------------------------------------------
// PublishSocialPostJob
// ---------------------------------------------------------------------------

it('dispatch queues a publish job', function () {
    Queue::fake();

    PublishSocialPostJob::dispatch(42);

    Queue::assertPushed(PublishSocialPostJob::class, function ($job) {
        return $job->targetId === 42;
    });
});

// ---------------------------------------------------------------------------
// Artisan command
// ---------------------------------------------------------------------------

it('social:publish-scheduled dispatches jobs for due posts', function () {
    Queue::fake();

    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);

    $post = SocialPost::factory()->due()->create(['user_id' => $admin->id]);
    $target = SocialPostTarget::factory()->create([
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
        'status' => SocialPostTarget::STATUS_PENDING,
    ]);

    $this->artisan('social:publish-scheduled')
        ->assertSuccessful();

    Queue::assertPushed(PublishSocialPostJob::class);
});

it('social:publish-scheduled skips posts requiring approval', function () {
    Queue::fake();

    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $account = SocialAccount::factory()->create(['user_id' => $admin->id]);

    // Post is due but not approved
    $post = SocialPost::factory()->due()->requiresApproval()->create(['user_id' => $admin->id]);
    SocialPostTarget::factory()->create([
        'social_post_id' => $post->id,
        'social_account_id' => $account->id,
    ]);

    $this->artisan('social:publish-scheduled')
        ->assertSuccessful();

    Queue::assertNotPushed(PublishSocialPostJob::class);
});
