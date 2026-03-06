<?php

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays published blog posts on index page', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $publishedPost = BlogPost::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'author_id' => $user->id,
    ]);
    $draftPost = BlogPost::factory()->draft()->create(['author_id' => $user->id]);

    $response = $this->get('/blog');

    $response->assertOk();
    $response->assertSee($publishedPost->title);
    $response->assertDontSee($draftPost->title);
});

it('displays a published blog post', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $post = BlogPost::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'author_id' => $user->id,
        'slug' => 'test-post',
    ]);

    $response = $this->get('/blog/test-post');

    $response->assertOk();
    $response->assertSee($post->title);
    $response->assertSee($post->author->name);
});

it('returns 404 for unpublished blog post', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $post = BlogPost::factory()->draft()->create([
        'author_id' => $user->id,
        'slug' => 'draft-post',
    ]);

    $response = $this->get('/blog/draft-post');

    $response->assertNotFound();
});

it('allows admin to create blog post', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $category = BlogCategory::factory()->create();
    $tag = BlogTag::factory()->create();

    $response = $this->actingAs($admin)->post('/admin/blog/posts', [
        'title' => 'New Blog Post',
        'excerpt' => 'This is an excerpt',
        'content' => 'This is the content',
        'status' => 'published',
        'categories' => [$category->id],
        'tags' => [$tag->id],
    ]);

    $response->assertRedirect('/admin/blog/posts');
    $this->assertDatabaseHas('blog_posts', [
        'title' => 'New Blog Post',
        'status' => 'published',
    ]);
});

it('prevents non-admin from creating blog post', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $response = $this->actingAs($user)->post('/admin/blog/posts', [
        'title' => 'Unauthorized Post',
        'status' => 'published',
    ]);

    $response->assertForbidden();
});

it('filters posts by category', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $category = BlogCategory::factory()->create(['slug' => 'tech']);

    $techPost = BlogPost::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'author_id' => $user->id,
    ]);
    $techPost->categories()->attach($category);

    $otherPost = BlogPost::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'author_id' => $user->id,
    ]);

    $response = $this->get('/blog/category/tech');

    $response->assertOk();
    $response->assertSee($techPost->title);
    $response->assertDontSee($otherPost->title);
});

it('filters posts by tag', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $tag = BlogTag::factory()->create(['slug' => 'laravel']);

    $taggedPost = BlogPost::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'author_id' => $user->id,
    ]);
    $taggedPost->tags()->attach($tag);

    $otherPost = BlogPost::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'author_id' => $user->id,
    ]);

    $response = $this->get('/blog/tag/laravel');

    $response->assertOk();
    $response->assertSee($taggedPost->title);
    $response->assertDontSee($otherPost->title);
});
