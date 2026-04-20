<?php

use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
    $this->user = User::factory()->create();
});

// Blog Search Tests
test('search returns relevant blog posts', function () {
    $post1 = BlogPost::factory()->create([
        'title' => 'Laravel Testing Best Practices',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $post2 = BlogPost::factory()->create([
        'title' => 'React Component Patterns',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->get(route('blog.search', ['q' => 'Laravel']));

    $response->assertOk();
    $response->assertSee($post1->title);
    $response->assertDontSee($post2->title);
});

// Comment Tests
test('authenticated user can post comment', function () {
    $post = BlogPost::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->actingAs($this->user);

    $this->post(route('blog.comments.store', $post), [
        'content' => 'Great article!',
    ])->assertRedirect();

    expect(BlogComment::count())->toBe(1);
});

// RSS Feed Tests
test('rss feed returns valid xml', function () {
    BlogPost::factory()->count(3)->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->get(route('blog.feed'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');
});

// Sitemap Tests
test('sitemap returns valid xml', function () {
    BlogPost::factory()->count(2)->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->get(route('sitemap'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');
});
