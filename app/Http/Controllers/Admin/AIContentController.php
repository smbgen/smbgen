<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateAIContentRequest;
use App\Models\AIGeneration;
use App\Services\AI\ClaudeAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AIContentController extends Controller
{
    public function __construct(protected ClaudeAIService $claudeService) {}

    /**
     * Generate AI content based on user prompt.
     */
    public function generate(GenerateAIContentRequest $request): JsonResponse
    {
        $hasAIGenerationTable = Schema::hasTable('ai_generations');

        // Check if AI service is available
        if (! $this->claudeService->isAvailable()) {
            return response()->json([
                'success' => false,
                'error' => 'AI content generation is not available. Please check your configuration.',
            ], 503);
        }

        // Check rate limits
        if (config('ai.rate_limit.enabled', true) && $hasAIGenerationTable) {
            $hourlyLimit = config('ai.rate_limit.max_requests_per_hour', 60);
            $hourlyCount = AIGeneration::getGenerationCount(auth()->id(), 'hour');

            if ($hourlyCount >= $hourlyLimit) {
                $oldestGeneration = AIGeneration::forUser(auth()->id())
                    ->where('created_at', '>=', now()->subHour())
                    ->oldest()
                    ->first();

                $resetTime = $oldestGeneration ? $oldestGeneration->created_at->addHour()->diffForHumans() : 'soon';

                return response()->json([
                    'success' => false,
                    'error' => "You've reached the hourly limit of {$hourlyLimit} AI generations. Rate limit resets {$resetTime}.",
                    'retry_after' => $oldestGeneration ? $oldestGeneration->created_at->addHour()->timestamp : null,
                ], 429);
            }
        }

        $validated = $request->validated();
        $contentType = $validated['content_type'];
        $prompt = $validated['prompt'];
        $customSystemPrompt = $validated['custom_system_prompt'] ?? null;

        $options = [];
        if (isset($validated['max_tokens'])) {
            $options['max_tokens'] = $validated['max_tokens'];
        }
        if (isset($validated['temperature'])) {
            $options['temperature'] = $validated['temperature'];
        }

        try {
            $generatedContent = match ($contentType) {
                'blog_post' => $this->claudeService->generateBlogPost($prompt, $customSystemPrompt),
                'landing_page' => $this->claudeService->generateLandingPage($prompt, $customSystemPrompt),
                'home_page' => $this->claudeService->generateHomePage($prompt, $customSystemPrompt),
                'brand_positioning' => $this->claudeService->generateBrandPositioning($prompt, $customSystemPrompt),
                'content_improvement' => $this->claudeService->improveContent(
                    $validated['existing_content'] ?? '',
                    $customSystemPrompt
                ),
                'industry_variant' => $this->claudeService->generateIndustryVariant(
                    $validated['existing_content'] ?? '',
                    $validated['industry'] ?? '',
                    $customSystemPrompt
                ),
                default => throw new \InvalidArgumentException("Unsupported content type: {$contentType}"),
            };

            // Log the generation
            if (config('ai.logging.enabled', true) && $hasAIGenerationTable) {
                AIGeneration::create([
                    'user_id' => auth()->id(),
                    'type' => $contentType,
                    'prompt' => $prompt,
                    'generated_content' => $generatedContent,
                    'model' => config('ai.anthropic.model'),
                    'status' => 'success',
                    // Token counts would need to be extracted from API response
                    // For now, we'll update this when we capture usage data
                ]);
            }

            return response()->json([
                'success' => true,
                'content' => $generatedContent,
                'type' => $contentType,
            ]);
        } catch (\RuntimeException $e) {
            // Log the error
            Log::error('AI content generation failed', [
                'user_id' => auth()->id(),
                'content_type' => $contentType,
                'error' => $e->getMessage(),
            ]);

            if (config('ai.logging.enabled', true) && $hasAIGenerationTable) {
                AIGeneration::create([
                    'user_id' => auth()->id(),
                    'type' => $contentType,
                    'prompt' => $prompt,
                    'generated_content' => '',
                    'model' => config('ai.anthropic.model'),
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                ]);
            }

            $errorMessage = 'Failed to generate content. Please try again.';

            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'details' => app()->environment('local') ? $e->getTraceAsString() : null,
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error during AI generation', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Generate SEO metadata for a blog post.
     */
    public function generateSEO(GenerateAIContentRequest $request): JsonResponse
    {
        $hasAIGenerationTable = Schema::hasTable('ai_generations');

        // Check if AI service is available
        if (! $this->claudeService->isAvailable()) {
            return response()->json([
                'success' => false,
                'error' => 'AI content generation is not available. Please check your configuration.',
            ], 503);
        }

        $validated = $request->validated();
        $title = $validated['prompt']; // Using prompt as title
        $content = $validated['existing_content'] ?? '';

        if (empty($content)) {
            return response()->json([
                'success' => false,
                'error' => 'Content is required to generate SEO metadata.',
            ], 422);
        }

        try {
            $seoData = $this->claudeService->generateSEOMetadata(
                $title,
                $content,
                $validated['custom_system_prompt'] ?? null
            );

            // Log the generation
            if (config('ai.logging.enabled', true) && $hasAIGenerationTable) {
                AIGeneration::create([
                    'user_id' => auth()->id(),
                    'type' => 'seo_metadata',
                    'prompt' => "Generate SEO for: {$title}",
                    'generated_content' => json_encode($seoData),
                    'model' => config('ai.anthropic.model'),
                    'status' => 'success',
                ]);
            }

            return response()->json([
                'success' => true,
                'seo' => $seoData,
            ]);
        } catch (\Exception $e) {
            Log::error('SEO generation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if (config('ai.logging.enabled', true) && $hasAIGenerationTable) {
                AIGeneration::create([
                    'user_id' => auth()->id(),
                    'type' => 'seo_metadata',
                    'prompt' => "Generate SEO for: {$title}",
                    'generated_content' => '',
                    'model' => config('ai.anthropic.model'),
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate SEO metadata. Please try again.',
            ], 500);
        }
    }

    /**
     * Get AI usage statistics for the current user.
     */
    public function getUsageStats(): JsonResponse
    {
        $userId = auth()->id();

        if (! Schema::hasTable('ai_generations')) {
            return response()->json([
                'today' => ['count' => 0, 'tokens' => 0],
                'this_week' => ['count' => 0, 'tokens' => 0],
                'this_month' => ['count' => 0, 'tokens' => 0],
                'recent_generations' => [],
            ]);
        }

        $stats = [
            'today' => [
                'count' => AIGeneration::getGenerationCount($userId, 'today'),
                'tokens' => AIGeneration::getTotalTokensUsed($userId, 'today'),
            ],
            'this_week' => [
                'count' => AIGeneration::forUser($userId)
                    ->successful()
                    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                'tokens' => AIGeneration::getTotalTokensUsed($userId, 'this_week'),
            ],
            'this_month' => [
                'count' => AIGeneration::forUser($userId)
                    ->successful()
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'tokens' => AIGeneration::getTotalTokensUsed($userId, 'this_month'),
            ],
            'recent_generations' => AIGeneration::forUser($userId)
                ->latest()
                ->take(10)
                ->get(['id', 'type', 'created_at', 'status']),
        ];

        return response()->json($stats);
    }
}
