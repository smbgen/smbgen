<?php

use App\Models\BusinessSetting;
use App\Services\AI\ClaudeAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Clear any existing settings
    BusinessSetting::where('key', 'like', 'ai_%')->delete();

    // Set default config
    Config::set('ai.enabled', true);
    Config::set('ai.anthropic.api_key', 'sk-ant-test-key');
    Config::set('ai.anthropic.model', 'claude-3-5-sonnet-20241022');
    Config::set('ai.anthropic.api_version', '2023-06-01');
    Config::set('ai.anthropic.max_tokens', 4000);
    Config::set('ai.anthropic.temperature', 0.7);
    Config::set('ai.generation.timeout', 60);
});

test('service initializes with api key from config', function () {
    $service = new ClaudeAIService;

    expect($service->isAvailable())->toBeTrue();
});

test('service initializes with encrypted api key from database', function () {
    // Store encrypted API key in database
    BusinessSetting::set('ai_api_key', Crypt::encryptString('sk-ant-db-key'));

    $service = new ClaudeAIService;

    expect($service->isAvailable())->toBeTrue();
});

test('service handles legacy unencrypted api key from database', function () {
    Log::shouldReceive('warning')
        ->once()
        ->with('Failed to decrypt AI API key, using as-is', \Mockery::any());

    // Store plain text API key (legacy)
    BusinessSetting::set('ai_api_key', 'sk-ant-plain-key');

    $service = new ClaudeAIService;

    expect($service->isAvailable())->toBeTrue();
});

test('service is unavailable when ai is disabled in config', function () {
    Config::set('ai.enabled', false);

    $service = new ClaudeAIService;

    expect($service->isAvailable())->toBeFalse();
});

test('service is unavailable when api key is missing', function () {
    Config::set('ai.anthropic.api_key', '');

    Log::shouldReceive('warning')
        ->once()
        ->with('Claude AI Service: API key not configured');

    $service = new ClaudeAIService;

    expect($service->isAvailable())->toBeFalse();
});

test('complete method throws exception when service is unavailable', function () {
    Config::set('ai.enabled', false);

    $service = new ClaudeAIService;

    $service->complete('system prompt', 'user prompt');
})->throws(\RuntimeException::class, 'Claude AI service is not available');

test('complete method makes successful api call', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [
                ['text' => 'Generated content here'],
            ],
            'usage' => [
                'input_tokens' => 100,
                'output_tokens' => 200,
            ],
        ], 200),
    ]);

    $service = new ClaudeAIService;
    $result = $service->complete('You are a helpful assistant', 'Write a blog post');

    expect($result)->toBe('Generated content here');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.anthropic.com/v1/messages'
            && $request['model'] === 'claude-3-5-sonnet-20241022'
            && $request['system'] === 'You are a helpful assistant'
            && $request['messages'][0]['content'] === 'Write a blog post';
    });
});

test('complete method throws exception on api error', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'error' => ['message' => 'Invalid API key'],
        ], 401),
    ]);

    Log::shouldReceive('error')
        ->once()
        ->with('Claude API request failed', \Mockery::any());

    $service = new ClaudeAIService;

    $service->complete('system', 'user prompt');
})->throws(\RuntimeException::class);

test('complete method throws exception on connection error', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('Connection failed');
    });

    Log::shouldReceive('error')
        ->once()
        ->with('Claude API connection failed', \Mockery::any());

    $service = new ClaudeAIService;

    $service->complete('system', 'user prompt');
})->throws(\RuntimeException::class, 'Failed to connect to Claude API');

test('complete method throws exception when response has empty content', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [
                ['text' => ''],
            ],
            'usage' => [
                'input_tokens' => 100,
                'output_tokens' => 0,
            ],
        ], 200),
    ]);

    $service = new ClaudeAIService;

    $service->complete('system', 'user prompt');
})->throws(\RuntimeException::class, 'Claude API returned empty content');

test('generateBlogPost uses correct system prompt', function () {
    Config::set('ai.prompts.blog_post', 'You are a blog writer');

    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [['text' => 'Blog content']],
            'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
        ], 200),
    ]);

    $service = new ClaudeAIService;
    $result = $service->generateBlogPost('Write about Laravel');

    expect($result)->toBe('Blog content');

    Http::assertSent(function ($request) {
        return $request['system'] === 'You are a blog writer'
            && $request['messages'][0]['content'] === 'Write about Laravel';
    });
});

test('generateBlogPost accepts custom system prompt', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [['text' => 'Custom content']],
            'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
        ], 200),
    ]);

    $service = new ClaudeAIService;
    $result = $service->generateBlogPost('Write something', 'Custom system prompt');

    expect($result)->toBe('Custom content');

    Http::assertSent(function ($request) {
        return $request['system'] === 'Custom system prompt';
    });
});

test('generateSEOMetadata parses response correctly', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [[
                'text' => "TITLE: Optimized SEO Title\nDESCRIPTION: This is a great meta description\nKEYWORDS: laravel, php, testing",
            ]],
            'usage' => ['input_tokens' => 100, 'output_tokens' => 50],
        ], 200),
    ]);

    $service = new ClaudeAIService;
    $result = $service->generateSEOMetadata('My Blog Post', 'Some content here');

    expect($result)->toBeArray()
        ->and($result['title'])->toBe('Optimized SEO Title')
        ->and($result['description'])->toBe('This is a great meta description')
        ->and($result['keywords'])->toBe('laravel, php, testing');
});

test('generateSEOMetadata handles missing fields gracefully', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [[
                'text' => 'TITLE: Only Title Here',
            ]],
            'usage' => ['input_tokens' => 100, 'output_tokens' => 50],
        ], 200),
    ]);

    $service = new ClaudeAIService;
    $result = $service->generateSEOMetadata('My Blog Post', 'Some content');

    expect($result)->toBeArray()
        ->and($result['title'])->toBe('Only Title Here')
        ->and($result['description'])->toBe('')
        ->and($result['keywords'])->toBe('');
});

test('improveContent sends correct prompt', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [['text' => 'Improved content']],
            'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
        ], 200),
    ]);

    $service = new ClaudeAIService;
    $result = $service->improveContent('Original content');

    expect($result)->toBe('Improved content');

    Http::assertSent(function ($request) {
        return str_contains($request['messages'][0]['content'], 'Original content');
    });
});

test('generateIndustryVariant sends correct prompt with industry', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [['text' => 'Healthcare-specific content']],
            'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
        ], 200),
    ]);

    $service = new ClaudeAIService;
    $result = $service->generateIndustryVariant('Generic content', 'Healthcare');

    expect($result)->toBe('Healthcare-specific content');

    Http::assertSent(function ($request) {
        return str_contains($request['messages'][0]['content'], 'Healthcare industry');
    });
});

test('getUsageStats returns correct data', function () {
    BusinessSetting::set('ai_model', 'claude-3-opus-20240229');
    BusinessSetting::set('ai_max_tokens', 5000);
    BusinessSetting::set('ai_temperature', 0.9);

    $service = new ClaudeAIService;
    $stats = $service->getUsageStats();

    expect($stats)->toBeArray()
        ->and($stats['model'])->toBe('claude-3-opus-20240229')
        ->and($stats['max_tokens'])->toBe(5000)
        ->and($stats['temperature'])->toBe(0.9);
});
