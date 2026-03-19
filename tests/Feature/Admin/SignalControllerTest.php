<?php

use App\Enums\SocialPlatform;
use App\Enums\SocialPostStatus;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view signal index', function () {
    SocialPost::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.signal.index'))
        ->assertOk()
        ->assertViewIs('admin.signal.index')
        ->assertViewHas('posts');
});

test('guest cannot access signal index', function () {
    $this->get(route('admin.signal.index'))
        ->assertRedirect(route('login'));
});

test('non-admin cannot access signal index', function () {
    $user = User::factory()->create(['role' => 'client']);

    $this->actingAs($user)
        ->get(route('admin.signal.index'))
        ->assertForbidden();
});

test('admin can create a social post', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.signal.store'), [
            'platform' => 'linkedin',
            'content' => 'Hello LinkedIn!',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('social_posts', [
        'platform' => 'linkedin',
        'content' => 'Hello LinkedIn!',
        'status' => SocialPostStatus::Draft->value,
    ]);
});

test('admin can create a scheduled post', function () {
    Queue::fake();
    $scheduledAt = now()->addDay()->format('Y-m-d H:i:s');

    $this->actingAs($this->admin)
        ->post(route('admin.signal.store'), [
            'platform' => 'instagram',
            'content' => 'Scheduled post!',
            'scheduled_at' => $scheduledAt,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('social_posts', [
        'platform' => 'instagram',
        'status' => SocialPostStatus::Scheduled->value,
    ]);
});

test('creating a post validates required fields', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.signal.store'), [])
        ->assertSessionHasErrors(['platform', 'content']);
});

test('admin can delete a post', function () {
    $post = SocialPost::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.signal.destroy', $post))
        ->assertRedirect();

    $this->assertModelMissing($post);
});

test('signal index can filter by platform', function () {
    SocialPost::factory()->create(['platform' => SocialPlatform::LinkedIn]);
    SocialPost::factory()->create(['platform' => SocialPlatform::Instagram]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.signal.index', ['platform' => 'linkedin']))
        ->assertOk();

    $posts = $response->viewData('posts');
    expect($posts->total())->toBe(1);
});
