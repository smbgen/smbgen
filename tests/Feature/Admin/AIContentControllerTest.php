<?php

use App\Models\AIGeneration;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // Create admin user for testing
    $this->admin = User::factory()->admin()->create();

    // Enable AI features
    Config::set('ai.enabled', true);
    Config::set('ai.logging.enabled', true);
    Config::set('ai.rate_limit.enabled', true);
    Config::set('ai.rate_limit.max_requests_per_hour', 60);
    Config::set('ai.anthropic.api_key', 'sk-ant-test-key');
});

test('generate endpoint requires authentication', function () {
    $response = $this->postJson(route('admin.ai.generate'), [
        'prompt' => 'Test prompt',
        'content_type' => 'blog_post',
    ]);

    $response->assertUnauthorized();
});

test('generate endpoint requires administrator role', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'Test prompt',
            'content_type' => 'blog_post',
        ]);

    $response->assertForbidden();
});

test('generate endpoint validates required fields', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), []);

    $response->assertJsonValidationErrors(['prompt', 'content_type']);
});

test('generate endpoint validates prompt length', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'short',
            'content_type' => 'blog_post',
        ]);

    $response->assertJsonValidationErrors(['prompt']);
});

test('generate endpoint validates content type', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'Valid prompt that is long enough',
            'content_type' => 'invalid_type',
        ]);

    $response->assertJsonValidationErrors(['content_type']);
});

test('generate endpoint returns error when ai is disabled', function () {
    Config::set('ai.enabled', false);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'Valid prompt that is long enough',
            'content_type' => 'blog_post',
        ]);

    $response->assertStatus(503)
        ->assertJson([
            'success' => false,
            'error' => 'AI content generation is not available. Please check your configuration.',
        ]);
});

test('generate endpoint enforces hourly rate limit', function () {
    // Create 60 successful generations in the last hour
    AIGeneration::factory()->count(60)->create([
        'user_id' => $this->admin->id,
        'status' => 'success',
        'created_at' => now()->subMinutes(30),
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'Valid prompt that is long enough',
            'content_type' => 'blog_post',
        ]);

    $response->assertStatus(429)
        ->assertJson([
            'success' => false,
        ]);
});

test('generate endpoint successfully generates blog post', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [
                ['text' => '<h1>Generated Blog Post</h1><p>Content here</p>'],
            ],
            'usage' => [
                'input_tokens' => 100,
                'output_tokens' => 200,
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'Write a blog post about Laravel testing',
            'content_type' => 'blog_post',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'type' => 'blog_post',
        ])
        ->assertJsonPath('content', '<h1>Generated Blog Post</h1><p>Content here</p>');

    // Verify generation was logged
    $this->assertDatabaseHas('ai_generations', [
        'user_id' => $this->admin->id,
        'type' => 'blog_post',
        'status' => 'success',
    ]);
});

test('generate endpoint successfully improves content', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [
                ['text' => '<p>Improved content with better structure</p>'],
            ],
            'usage' => [
                'input_tokens' => 150,
                'output_tokens' => 100,
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'Improve this content',
            'content_type' => 'content_improvement',
            'existing_content' => '<p>Original content</p>',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'type' => 'content_improvement',
        ]);
});

test('generate endpoint requires industry for industry variants', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'Create industry variant',
            'content_type' => 'industry_variant',
            'existing_content' => '<p>Original content</p>',
        ]);

    $response->assertJsonValidationErrors(['industry']);
});

test('generate endpoint handles api errors gracefully', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'error' => ['message' => 'Invalid API key'],
        ], 401),
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.generate'), [
            'prompt' => 'Valid prompt that is long enough',
            'content_type' => 'blog_post',
        ]);

    $response->assertStatus(500)
        ->assertJson([
            'success' => false,
            'error' => 'Failed to generate content. Please try again.',
        ]);

    // Verify error was logged
    $this->assertDatabaseHas('ai_generations', [
        'user_id' => $this->admin->id,
        'type' => 'blog_post',
        'status' => 'error',
    ]);
});

test('generateSEO endpoint requires authentication', function () {
    $response = $this->postJson(route('admin.ai.seo'), [
        'prompt' => 'My Blog Title',
        'existing_content' => 'Content here',
    ]);

    $response->assertUnauthorized();
});

test('generateSEO endpoint requires content', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.seo'), [
            'prompt' => 'My Blog Title',
            'content_type' => 'seo_metadata',
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'error' => 'Content is required to generate SEO metadata.',
        ]);
});

test('generateSEO endpoint successfully generates metadata', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [[
                'text' => "TITLE: Optimized Title\nDESCRIPTION: Great description\nKEYWORDS: laravel, testing, php",
            ]],
            'usage' => ['input_tokens' => 100, 'output_tokens' => 50],
        ], 200),
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.seo'), [
            'prompt' => 'My Blog Post',
            'content_type' => 'seo_metadata',
            'existing_content' => '<p>Blog content here with enough text</p>',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'seo' => [
                'title' => 'Optimized Title',
                'description' => 'Great description',
                'keywords' => 'laravel, testing, php',
            ],
        ]);

    // Verify generation was logged
    $this->assertDatabaseHas('ai_generations', [
        'user_id' => $this->admin->id,
        'type' => 'seo_metadata',
        'status' => 'success',
    ]);
});

test('generateSEO endpoint handles api errors', function () {
    Http::fake(function () {
        throw new \RuntimeException('API error');
    });

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.ai.seo'), [
            'prompt' => 'My Blog Post',
            'content_type' => 'seo_metadata',
            'existing_content' => '<p>Content here</p>',
        ]);

    $response->assertStatus(500)
        ->assertJson([
            'success' => false,
            'error' => 'Failed to generate SEO metadata. Please try again.',
        ]);
});

test('getUsageStats endpoint returns user statistics', function () {
    // Create some generations for the user
    AIGeneration::factory()->count(5)->create([
        'user_id' => $this->admin->id,
        'status' => 'success',
        'created_at' => today(),
    ]);

    AIGeneration::factory()->count(3)->create([
        'user_id' => $this->admin->id,
        'status' => 'success',
        'created_at' => now()->subDays(2),
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.ai.stats'));

    $response->assertOk()
        ->assertJsonStructure([
            'today' => ['count', 'tokens'],
            'this_week' => ['count', 'tokens'],
            'this_month' => ['count', 'tokens'],
            'recent_generations',
        ])
        ->assertJsonPath('today.count', 5);
});
