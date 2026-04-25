<?php

use App\Models\BusinessSetting;
use App\Services\AI\OpenRouterAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function () {
    BusinessSetting::where('key', 'like', 'ai_%')->delete();

    Config::set('ai.enabled', true);
    Config::set('ai.openrouter.api_key', 'sk-or-v1-test-key');
    Config::set('ai.openrouter.model', 'openai/gpt-4o');
    Config::set('ai.openrouter.max_tokens', 4096);
    Config::set('ai.openrouter.temperature', 0.7);
    Config::set('ai.generation.timeout', 60);
    Config::set('ai.logging.log_tokens', false);
});

test('service initializes with api key from config', function () {
    $service = new OpenRouterAIService;

    expect($service->isAvailable())->toBeTrue();
});

test('service initializes with encrypted api key from database', function () {
    BusinessSetting::set('ai_openrouter_api_key', Crypt::encryptString('sk-or-v1-db-key'));

    $service = new OpenRouterAIService;

    expect($service->isAvailable())->toBeTrue();
});

test('service handles legacy unencrypted api key from database', function () {
    Log::shouldReceive('warning')
        ->once()
        ->with('Failed to decrypt OpenRouter API key, using as-is', \Mockery::any());

    BusinessSetting::set('ai_openrouter_api_key', 'sk-or-plain-key');

    $service = new OpenRouterAIService;

    expect($service->isAvailable())->toBeTrue();
});

test('service is unavailable when ai is disabled in config', function () {
    Config::set('ai.enabled', false);

    $service = new OpenRouterAIService;

    expect($service->isAvailable())->toBeFalse();
});

test('service is unavailable when api key is missing', function () {
    Config::set('ai.openrouter.api_key', '');

    Log::shouldReceive('warning')
        ->once()
        ->with('OpenRouter AI Service: API key not configured');

    $service = new OpenRouterAIService;

    expect($service->isAvailable())->toBeFalse();
});

test('complete method throws exception when service is unavailable', function () {
    Config::set('ai.enabled', false);

    $service = new OpenRouterAIService;

    $service->complete('system prompt', 'user prompt');
})->throws(\RuntimeException::class, 'OpenRouter AI service is not available');

test('complete method makes successful api call', function () {
    Http::fake([
        'openrouter.ai/*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'Generated content here']],
            ],
            'usage' => [
                'prompt_tokens' => 100,
                'completion_tokens' => 200,
            ],
        ], 200),
    ]);

    $service = new OpenRouterAIService;
    $result = $service->complete('You are a helpful assistant', 'Write a blog post');

    expect($result)->toBe('Generated content here');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://openrouter.ai/api/v1/chat/completions'
            && $request['model'] === 'openai/gpt-4o'
            && $request['messages'][0]['role'] === 'system'
            && $request['messages'][0]['content'] === 'You are a helpful assistant'
            && $request['messages'][1]['role'] === 'user'
            && $request['messages'][1]['content'] === 'Write a blog post';
    });
});

test('complete method sends bearer authorization header', function () {
    Http::fake([
        'openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => 'Content']]],
            'usage' => ['prompt_tokens' => 10, 'completion_tokens' => 20],
        ], 200),
    ]);

    $service = new OpenRouterAIService;
    $service->complete('system', 'user');

    Http::assertSent(function ($request) {
        return str_starts_with($request->header('Authorization')[0], 'Bearer ');
    });
});

test('complete method throws exception on 401 error', function () {
    Http::fake([
        'openrouter.ai/*' => Http::response([
            'error' => ['message' => 'Invalid API key'],
        ], 401),
    ]);

    Log::shouldReceive('error')
        ->once()
        ->with('OpenRouter API request failed', \Mockery::any());

    $service = new OpenRouterAIService;

    $service->complete('system', 'user prompt');
})->throws(\RuntimeException::class, 'Invalid API key');

test('complete method throws exception on 402 insufficient credits', function () {
    Http::fake([
        'openrouter.ai/*' => Http::response([
            'error' => ['message' => 'Insufficient credits'],
        ], 402),
    ]);

    Log::shouldReceive('error')
        ->once()
        ->with('OpenRouter API request failed', \Mockery::any());

    $service = new OpenRouterAIService;

    $service->complete('system', 'user prompt');
})->throws(\RuntimeException::class, 'Insufficient OpenRouter credits');

test('complete method throws exception on connection error', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('Connection failed');
    });

    Log::shouldReceive('error')
        ->once()
        ->with('OpenRouter API connection failed', \Mockery::any());

    $service = new OpenRouterAIService;

    $service->complete('system', 'user prompt');
})->throws(\RuntimeException::class, 'Failed to connect to OpenRouter API');

test('complete method throws exception when response has empty content', function () {
    Http::fake([
        'openrouter.ai/*' => Http::response([
            'choices' => [
                ['message' => ['content' => '']],
            ],
            'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 0],
        ], 200),
    ]);

    $service = new OpenRouterAIService;

    $service->complete('system', 'user prompt');
})->throws(\RuntimeException::class, 'OpenRouter API returned empty content');

test('generateBlogPost uses correct system prompt', function () {
    Config::set('ai.prompts.blog_post', 'You are a blog writer');

    Http::fake([
        'openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => 'Blog content']]],
            'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 200],
        ], 200),
    ]);

    $service = new OpenRouterAIService;
    $result = $service->generateBlogPost('Write about Laravel');

    expect($result)->toBe('Blog content');

    Http::assertSent(function ($request) {
        return $request['messages'][0]['content'] === 'You are a blog writer'
            && $request['messages'][1]['content'] === 'Write about Laravel';
    });
});

test('generateSEOMetadata parses response correctly', function () {
    Http::fake([
        'openrouter.ai/*' => Http::response([
            'choices' => [[
                'message' => ['content' => "TITLE: Optimized SEO Title\nDESCRIPTION: A great meta description\nKEYWORDS: laravel, php, testing"],
            ]],
            'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 50],
        ], 200),
    ]);

    $service = new OpenRouterAIService;
    $result = $service->generateSEOMetadata('My Blog Post', 'Some content here');

    expect($result)->toBeArray()
        ->and($result['title'])->toBe('Optimized SEO Title')
        ->and($result['description'])->toBe('A great meta description')
        ->and($result['keywords'])->toBe('laravel, php, testing');
});

test('getUsageStats returns correct data', function () {
    BusinessSetting::set('ai_model', 'openai/gpt-4o-mini');
    BusinessSetting::set('ai_max_tokens', 2000);
    BusinessSetting::set('ai_temperature', 0.5);

    $service = new OpenRouterAIService;
    $stats = $service->getUsageStats();

    expect($stats)->toBeArray()
        ->and($stats['model'])->toBe('openai/gpt-4o-mini')
        ->and($stats['max_tokens'])->toBe(2000)
        ->and($stats['temperature'])->toBe(0.5);
});
