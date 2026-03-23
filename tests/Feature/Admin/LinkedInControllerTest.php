<?php

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
    $this->actingAs($this->admin);
});

// ─── Index ────────────────────────────────────────────────────────────────────

it('redirects guests from linkedin index', function () {
    auth()->logout();
    $this->get(route('admin.linkedin.index'))->assertRedirect(route('login'));
});

it('shows linkedin index for admin', function () {
    $this->get(route('admin.linkedin.index'))->assertOk()->assertViewIs('admin.linkedin.index');
});

it('shows empty state when no accounts connected', function () {
    $this->get(route('admin.linkedin.index'))
        ->assertOk()
        ->assertSee('No LinkedIn accounts connected');
});

it('shows connected account on index', function () {
    SocialAccount::factory()->create([
        'user_id' => $this->admin->id,
        'platform' => 'linkedin',
        'account_name' => 'Test Corp',
        'page_name' => 'Test Corp Page',
        'active' => true,
    ]);

    $this->get(route('admin.linkedin.index'))
        ->assertOk()
        ->assertSee('Test Corp Page');
});

// ─── Disconnect ───────────────────────────────────────────────────────────────

it('can disconnect a linkedin account', function () {
    $account = SocialAccount::factory()->create([
        'user_id' => $this->admin->id,
        'platform' => 'linkedin',
    ]);

    $this->delete(route('admin.linkedin.disconnect', $account))
        ->assertRedirect(route('admin.linkedin.index'));

    $this->assertDatabaseMissing('social_accounts', ['id' => $account->id]);
});

it('cannot disconnect another users linkedin account', function () {
    $other = User::factory()->create();
    $account = SocialAccount::factory()->create([
        'user_id' => $other->id,
        'platform' => 'linkedin',
    ]);

    $this->delete(route('admin.linkedin.disconnect', $account))->assertForbidden();
});

// ─── Posts Index ──────────────────────────────────────────────────────────────

it('shows posts index', function () {
    $this->get(route('admin.linkedin.posts.index'))->assertOk()->assertViewIs('admin.linkedin.posts.index');
});

it('filters posts by status tab', function () {
    $account = SocialAccount::factory()->create(['user_id' => $this->admin->id, 'platform' => 'linkedin']);
    SocialPost::factory()->create(['social_account_id' => $account->id, 'user_id' => $this->admin->id, 'status' => 'draft', 'content' => 'Draft post content']);
    SocialPost::factory()->create(['social_account_id' => $account->id, 'user_id' => $this->admin->id, 'status' => 'published', 'content' => 'Published post content', 'published_at' => now()]);

    $this->get(route('admin.linkedin.posts.index', ['status' => 'draft']))
        ->assertOk()
        ->assertSee('Draft post content')
        ->assertDontSee('Published post content');
});

// ─── Posts Create ─────────────────────────────────────────────────────────────

it('redirects to index when no accounts connected on create', function () {
    $this->get(route('admin.linkedin.posts.create'))
        ->assertRedirect(route('admin.linkedin.index'));
});

it('shows create form when account is connected', function () {
    SocialAccount::factory()->create(['user_id' => $this->admin->id, 'platform' => 'linkedin', 'active' => true]);

    $this->get(route('admin.linkedin.posts.create'))->assertOk()->assertViewIs('admin.linkedin.posts.create');
});

// ─── Posts Store ──────────────────────────────────────────────────────────────

it('saves a draft post', function () {
    $account = SocialAccount::factory()->create(['user_id' => $this->admin->id, 'platform' => 'linkedin', 'active' => true]);

    $this->post(route('admin.linkedin.posts.store'), [
        'social_account_id' => $account->id,
        'content' => 'Hello LinkedIn world!',
        'status' => 'draft',
    ])->assertRedirect(route('admin.linkedin.posts.index'));

    $this->assertDatabaseHas('social_posts', [
        'user_id' => $this->admin->id,
        'content' => 'Hello LinkedIn world!',
        'status' => 'draft',
    ]);
});

it('validates content length on store', function () {
    $account = SocialAccount::factory()->create(['user_id' => $this->admin->id, 'platform' => 'linkedin', 'active' => true]);

    $this->post(route('admin.linkedin.posts.store'), [
        'social_account_id' => $account->id,
        'content' => str_repeat('a', 3001),
        'status' => 'draft',
    ])->assertSessionHasErrors('content');
});

it('saves a scheduled post', function () {
    $account = SocialAccount::factory()->create(['user_id' => $this->admin->id, 'platform' => 'linkedin', 'active' => true]);
    $future = now()->addDay()->format('Y-m-d H:i:s');

    $this->post(route('admin.linkedin.posts.store'), [
        'social_account_id' => $account->id,
        'content' => 'Scheduled post!',
        'status' => 'scheduled',
        'scheduled_at' => $future,
    ])->assertRedirect(route('admin.linkedin.posts.index'));

    $this->assertDatabaseHas('social_posts', [
        'content' => 'Scheduled post!',
        'status' => 'scheduled',
    ]);
});

it('requires scheduled_at when status is scheduled', function () {
    $account = SocialAccount::factory()->create(['user_id' => $this->admin->id, 'platform' => 'linkedin', 'active' => true]);

    $this->post(route('admin.linkedin.posts.store'), [
        'social_account_id' => $account->id,
        'content' => 'A post',
        'status' => 'scheduled',
    ])->assertSessionHasErrors('scheduled_at');
});

// ─── Posts Destroy ────────────────────────────────────────────────────────────

it('can delete own post', function () {
    $account = SocialAccount::factory()->create(['user_id' => $this->admin->id, 'platform' => 'linkedin']);
    $post = SocialPost::factory()->create(['social_account_id' => $account->id, 'user_id' => $this->admin->id]);

    $this->delete(route('admin.linkedin.posts.destroy', $post))
        ->assertRedirect(route('admin.linkedin.posts.index'));

    $this->assertDatabaseMissing('social_posts', ['id' => $post->id]);
});

it('cannot delete another users post', function () {
    $other = User::factory()->create();
    $account = SocialAccount::factory()->create(['user_id' => $other->id, 'platform' => 'linkedin']);
    $post = SocialPost::factory()->create(['social_account_id' => $account->id, 'user_id' => $other->id]);

    $this->delete(route('admin.linkedin.posts.destroy', $post))->assertForbidden();
});
