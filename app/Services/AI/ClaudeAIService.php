<?php

namespace App\Services\AI;

use App\Models\BusinessSetting;
use App\Models\CmsCompanyColors;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeAIService
{
    protected bool $available = false;

    protected string $apiKey;

    protected string $model;

    protected string $apiVersion;

    protected int $maxTokens;

    protected float $temperature;

    protected int $timeout;

    public function __construct()
    {
        try {
            // Check database first, then fall back to .env
            $dbApiKey = BusinessSetting::get('ai_api_key');

            if ($dbApiKey) {
                // Decrypt the API key from database
                try {
                    $this->apiKey = Crypt::decryptString($dbApiKey);
                } catch (\Exception $e) {
                    // If decryption fails, treat as legacy unencrypted key
                    Log::warning('Failed to decrypt AI API key, using as-is', ['error' => $e->getMessage()]);
                    $this->apiKey = $dbApiKey;
                }
            } else {
                // Fall back to .env configuration
                $this->apiKey = config('ai.anthropic.api_key');
            }

            $this->model = BusinessSetting::get('ai_model', config('ai.anthropic.model'));
            $this->apiVersion = config('ai.anthropic.api_version');
            $this->maxTokens = (int) BusinessSetting::get('ai_max_tokens', config('ai.anthropic.max_tokens'));
            $this->temperature = (float) BusinessSetting::get('ai_temperature', config('ai.anthropic.temperature'));
            $this->timeout = config('ai.generation.timeout');

            // Check if service is properly configured
            if (empty($this->apiKey)) {
                Log::warning('Claude AI Service: API key not configured');
                $this->available = false;

                return;
            }

            $this->available = true;
        } catch (\Exception $e) {
            Log::error('Claude AI Service initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->available = false;
        }
    }

    /**
     * Check if the Claude AI service is available and configured.
     */
    public function isAvailable(): bool
    {
        return $this->available && config('ai.enabled', false);
    }

    /**
     * Get CSS class whitelist for AI content generation.
     */
    protected function getCssClassWhitelist(): string
    {
        $companyColors = CmsCompanyColors::getSettings();
        $classes = $companyColors->allowed_css_classes ?? CmsCompanyColors::getDefaultCssClassWhitelist();

        // Ensure $classes is an array
        if (! is_array($classes)) {
            $classes = CmsCompanyColors::getDefaultCssClassWhitelist();
        }

        return implode(', ', array_map(fn ($class) => ".{$class}", $classes));
    }

    /**
     * Inject CSS class whitelist into system prompt.
     */
    protected function injectWhitelist(string $systemPrompt): string
    {
        if (str_contains($systemPrompt, '{css_class_whitelist}')) {
            return str_replace('{css_class_whitelist}', $this->getCssClassWhitelist(), $systemPrompt);
        }

        return $systemPrompt;
    }

    /**
     * Generate content using Claude AI with a system and user prompt.
     *
     * @param  string  $systemPrompt  The system instructions for Claude
     * @param  string  $userPrompt  The user's content generation request
     * @param  array  $options  Optional overrides for max_tokens, temperature, etc.
     * @return string The generated content
     *
     * @throws \RuntimeException If the API call fails
     */
    public function complete(string $systemPrompt, string $userPrompt, array $options = []): string
    {
        if (! $this->isAvailable()) {
            throw new \RuntimeException('Claude AI service is not available. Please check your configuration.');
        }

        $maxTokens = $options['max_tokens'] ?? $this->maxTokens;
        $temperature = $options['temperature'] ?? $this->temperature;

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => $this->apiVersion,
                    'content-type' => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => $this->model,
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                    'system' => $systemPrompt,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $userPrompt,
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                $errorBody = $response->body();
                $errorData = $response->json();

                // Extract error message from API response
                $errorMessage = $errorData['error']['message'] ?? 'Unknown API error';

                Log::error('Claude API request failed', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                    'error_message' => $errorMessage,
                ]);

                // Provide user-friendly error messages based on status code
                $userMessage = match ($response->status()) {
                    401 => 'Invalid API key. Please check your AI configuration.',
                    429 => 'Rate limit exceeded. Please try again in a few moments.',
                    500, 502, 503 => 'Claude AI service is temporarily unavailable. Please try again later.',
                    default => 'AI generation failed: '.$errorMessage,
                };

                throw new \RuntimeException($userMessage);
            }

            $data = $response->json();

            // Extract the text content from the response
            $content = $data['content'][0]['text'] ?? '';

            if (empty($content)) {
                throw new \RuntimeException('Claude API returned empty content. Please try again with a different prompt.');
            }

            // Log token usage if enabled
            if (config('ai.logging.log_tokens', true)) {
                Log::info('Claude API usage', [
                    'model' => $this->model,
                    'input_tokens' => $data['usage']['input_tokens'] ?? 0,
                    'output_tokens' => $data['usage']['output_tokens'] ?? 0,
                ]);
            }

            return $content;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Claude API connection failed', [
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to connect to Claude API. Please check your network connection.');
        } catch (\Exception $e) {
            Log::error('Claude API request exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate blog post content.
     *
     * @param  string  $prompt  The user's content generation request
     * @param  string|null  $customSystemPrompt  Optional custom system prompt
     * @return string The generated HTML content
     */
    public function generateBlogPost(string $prompt, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.blog_post');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, $prompt);
    }

    /**
     * Generate SEO metadata (title, description, keywords).
     *
     * @param  string  $title  The blog post title
     * @param  string  $content  The blog post content
     * @param  string|null  $customSystemPrompt  Optional custom system prompt
     * @return array{title: string, description: string, keywords: string}
     */
    public function generateSEOMetadata(string $title, string $content, ?string $customSystemPrompt = null): array
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.seo_metadata');

        // Strip HTML tags from content for cleaner processing
        $cleanContent = strip_tags($content);
        $excerpt = substr($cleanContent, 0, 500);

        $userPrompt = "Generate SEO metadata for this blog post:\n\nTitle: {$title}\n\nContent excerpt: {$excerpt}\n\nProvide the output in this exact format:\nTITLE: [optimized title]\nDESCRIPTION: [meta description]\nKEYWORDS: [keyword1, keyword2, keyword3, ...]";

        $response = $this->complete($systemPrompt, $userPrompt, ['max_tokens' => 500]);

        // Parse the structured response
        return $this->parseSEOResponse($response);
    }

    /**
     * Improve existing content.
     *
     * @param  string  $content  The existing content to improve
     * @param  string|null  $customSystemPrompt  Optional custom system prompt
     * @return string The improved content
     */
    public function improveContent(string $content, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.content_improvement');

        $userPrompt = "Improve the following content:\n\n{$content}";

        return $this->complete($systemPrompt, $userPrompt);
    }

    /**
     * Generate industry-specific variant of existing content.
     *
     * @param  string  $content  The original content
     * @param  string  $industry  The target industry
     * @param  string|null  $customSystemPrompt  Optional custom system prompt
     * @return string The adapted content
     */
    public function generateIndustryVariant(string $content, string $industry, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.industry_variant');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        $userPrompt = "Adapt the following content for the {$industry} industry:\n\n{$content}";

        return $this->complete($systemPrompt, $userPrompt);
    }

    /**
     * Generate landing page content.
     */
    public function generateLandingPage(string $prompt, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.landing_page');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, $prompt);
    }

    /**
     * Generate home page content.
     */
    public function generateHomePage(string $prompt, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.home_page');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, $prompt);
    }

    /**
     * Generate brand positioning content.
     */
    public function generateBrandPositioning(string $prompt, ?string $customSystemPrompt = null): string
    {
        $systemPrompt = $customSystemPrompt ?? config('ai.prompts.brand_positioning');
        $systemPrompt = $this->injectWhitelist($systemPrompt);

        return $this->complete($systemPrompt, $prompt);
    }

    /**
     * Parse SEO metadata from Claude's response.
     */
    protected function parseSEOResponse(string $response): array
    {
        $title = '';
        $description = '';
        $keywords = '';

        // Extract TITLE
        if (preg_match('/TITLE:\s*(.+?)(?:\n|$)/i', $response, $matches)) {
            $title = trim($matches[1]);
        }

        // Extract DESCRIPTION
        if (preg_match('/DESCRIPTION:\s*(.+?)(?:\n|$)/i', $response, $matches)) {
            $description = trim($matches[1]);
        }

        // Extract KEYWORDS
        if (preg_match('/KEYWORDS:\s*(.+?)(?:\n|$)/i', $response, $matches)) {
            $keywords = trim($matches[1]);
        }

        // Validate that at least one field was extracted
        if (empty($title) && empty($description) && empty($keywords)) {
            Log::warning('Failed to parse SEO metadata from AI response', [
                'response' => $response,
            ]);
            throw new \RuntimeException('Failed to parse SEO metadata. Please try again.');
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
        ];
    }

    /**
     * Get current token usage statistics.
     */
    public function getUsageStats(): array
    {
        return [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];
    }
}
