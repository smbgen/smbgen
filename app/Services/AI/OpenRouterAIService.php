<?php

namespace App\Services\AI;

use App\Models\BusinessSetting;
use App\Models\CmsCompanyColors;
use App\Services\AI\Contracts\AIServiceInterface;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterAIService implements AIServiceInterface
{
    protected bool $available = false;

    protected string $apiKey;

    protected string $model;

    protected int $maxTokens;

    protected float $temperature;

    protected int $timeout;

    public function __construct()
    {
        try {
            $dbApiKey = BusinessSetting::get('ai_openrouter_api_key');

            if ($dbApiKey) {
                try {
                    $this->apiKey = Crypt::decryptString($dbApiKey);
                } catch (\Exception $e) {
                    Log::warning('Failed to decrypt OpenRouter API key, using as-is', ['error' => $e->getMessage()]);
                    $this->apiKey = $dbApiKey;
                }
            } else {
                $this->apiKey = config('ai.openrouter.api_key', '');
            }

            $this->model = BusinessSetting::get('ai_model', config('ai.openrouter.model'));
            $this->maxTokens = (int) BusinessSetting::get('ai_max_tokens', config('ai.openrouter.max_tokens'));
            $this->temperature = (float) BusinessSetting::get('ai_temperature', config('ai.openrouter.temperature'));
            $this->timeout = config('ai.generation.timeout');

            if (empty($this->apiKey)) {
                Log::warning('OpenRouter AI Service: API key not configured');
                $this->available = false;

                return;
            }

            $this->available = true;
        } catch (\Exception $e) {
            Log::error('OpenRouter AI Service initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->available = false;
        }
    }

    public function isAvailable(): bool
    {
        return $this->available && config('ai.enabled', false);
    }

    protected function getCssClassWhitelist(): string
    {
        $companyColors = CmsCompanyColors::getSettings();
        $classes = $companyColors->allowed_css_classes ?? CmsCompanyColors::getDefaultCssClassWhitelist();

        if (! is_array($classes)) {
            $classes = CmsCompanyColors::getDefaultCssClassWhitelist();
        }

        return implode(', ', array_map(fn ($class) => ".{$class}", $classes));
    }

    protected function injectWhitelist(string $systemPrompt): string
    {
        if (str_contains($systemPrompt, '{css_class_whitelist}')) {
            return str_replace('{css_class_whitelist}', $this->getCssClassWhitelist(), $systemPrompt);
        }

        return $systemPrompt;
    }

    public function complete(string $systemPrompt, string $userPrompt, array $options = []): string
    {
        if (! $this->isAvailable()) {
            throw new \RuntimeException('OpenRouter AI service is not available. Please check your configuration.');
        }

        $maxTokens = $options['max_tokens'] ?? $this->maxTokens;
        $temperature = $options['temperature'] ?? $this->temperature;

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $this->model,
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                ]);

            if (! $response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown API error';

                Log::error('OpenRouter API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'error_message' => $errorMessage,
                ]);

                $userMessage = match ($response->status()) {
                    401 => 'Invalid API key. Please check your OpenRouter configuration.',
                    402 => 'Insufficient OpenRouter credits. Please top up your account.',
                    429 => 'Rate limit exceeded. Please try again in a few moments.',
                    500, 502, 503 => 'OpenRouter service is temporarily unavailable. Please try again later.',
                    default => 'AI generation failed: '.$errorMessage,
                };

                throw new \RuntimeException($userMessage);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            if (empty($content)) {
                throw new \RuntimeException('OpenRouter API returned empty content. Please try again with a different prompt.');
            }

            if (config('ai.logging.log_tokens', true)) {
                Log::info('OpenRouter API usage', [
                    'model' => $this->model,
                    'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                    'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                ]);
            }

            return $content;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('OpenRouter API connection failed', [
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to connect to OpenRouter API. Please check your network connection.');
        } catch (\Exception $e) {
            Log::error('OpenRouter API request exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function generateBlogPost(string $prompt, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.blog_post');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, $prompt);
    }

    public function generateSEOMetadata(string $title, string $content, ?string $customSystemPrompt = null): array
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.seo_metadata');

        $cleanContent = strip_tags($content);
        $excerpt = substr($cleanContent, 0, 500);

        $userPrompt = "Generate SEO metadata for this blog post:\n\nTitle: {$title}\n\nContent excerpt: {$excerpt}\n\nProvide the output in this exact format:\nTITLE: [optimized title]\nDESCRIPTION: [meta description]\nKEYWORDS: [keyword1, keyword2, keyword3, ...]";

        $response = $this->complete($systemPrompt, $userPrompt, ['max_tokens' => 500]);

        return $this->parseSEOResponse($response);
    }

    public function improveContent(string $content, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.content_improvement');

        return $this->complete($systemPrompt, "Improve the following content:\n\n{$content}");
    }

    public function generateIndustryVariant(string $content, string $industry, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.industry_variant');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, "Adapt the following content for the {$industry} industry:\n\n{$content}");
    }

    public function generateLandingPage(string $prompt, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.landing_page');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, $prompt);
    }

    public function generateHomePage(string $prompt, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.home_page');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, $prompt);
    }

    public function generateBrandPositioning(string $prompt, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.brand_positioning');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, $prompt);
    }

    protected function parseSEOResponse(string $response): array
    {
        $title = '';
        $description = '';
        $keywords = '';

        if (preg_match('/TITLE:\s*(.+?)(?:\n|$)/i', $response, $matches)) {
            $title = trim($matches[1]);
        }

        if (preg_match('/DESCRIPTION:\s*(.+?)(?:\n|$)/i', $response, $matches)) {
            $description = trim($matches[1]);
        }

        if (preg_match('/KEYWORDS:\s*(.+?)(?:\n|$)/i', $response, $matches)) {
            $keywords = trim($matches[1]);
        }

        if (empty($title) && empty($description) && empty($keywords)) {
            Log::warning('Failed to parse SEO metadata from AI response', ['response' => $response]);
            throw new \RuntimeException('Failed to parse SEO metadata. Please try again.');
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
        ];
    }

    public function getUsageStats(): array
    {
        return [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];
    }
}
