<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AISettingsController extends Controller
{
    /**
     * Display the AI settings page.
     */
    public function index()
    {
        $provider = BusinessSetting::get('ai_provider', config('ai.provider', 'anthropic'));

        // Anthropic key
        $dbAnthropicKey = BusinessSetting::get('ai_api_key');
        $envAnthropicKey = config('ai.anthropic.api_key');
        $decryptedAnthropicKey = $this->decryptKey($dbAnthropicKey);
        $anthropicKeySet = ! empty($decryptedAnthropicKey) || ! empty($envAnthropicKey);

        // OpenRouter key
        $dbOpenRouterKey = BusinessSetting::get('ai_openrouter_api_key');
        $envOpenRouterKey = config('ai.openrouter.api_key');
        $decryptedOpenRouterKey = $this->decryptKey($dbOpenRouterKey);
        $openRouterKeySet = ! empty($decryptedOpenRouterKey) || ! empty($envOpenRouterKey);

        $activeKey = $provider === 'openrouter' ? $decryptedOpenRouterKey : $decryptedAnthropicKey;
        $activeEnvKey = $provider === 'openrouter' ? $envOpenRouterKey : $envAnthropicKey;

        $defaultModel = $provider === 'openrouter'
            ? config('ai.openrouter.model')
            : config('ai.anthropic.model');

        $availableModels = Cache::get('ai_available_models_'.$provider, $this->getDefaultModels($provider));

        $settings = [
            'enabled' => config('ai.enabled', false),
            'provider' => $provider,
            'api_key_set' => $provider === 'openrouter' ? $openRouterKeySet : $anthropicKeySet,
            'api_key_in_db' => $provider === 'openrouter' ? ! empty($dbOpenRouterKey) : ! empty($dbAnthropicKey),
            'api_key_last_4' => $activeKey ? '****'.substr($activeKey, -4) : ($activeEnvKey ? '****'.substr($activeEnvKey, -4) : null),
            'anthropic_key_set' => $anthropicKeySet,
            'openrouter_key_set' => $openRouterKeySet,
            'model' => BusinessSetting::get('ai_model', $defaultModel),
            'max_tokens' => BusinessSetting::get('ai_max_tokens', config('ai.anthropic.max_tokens')),
            'temperature' => BusinessSetting::get('ai_temperature', config('ai.anthropic.temperature')),
            'available_models' => $availableModels,
            'prompts' => [
                'blog_post' => BusinessSetting::get('ai_prompt_blog_post', config('ai.prompts.blog_post')),
                'seo_metadata' => BusinessSetting::get('ai_prompt_seo_metadata', config('ai.prompts.seo_metadata')),
                'content_improvement' => BusinessSetting::get('ai_prompt_content_improvement', config('ai.prompts.content_improvement')),
                'industry_variant' => BusinessSetting::get('ai_prompt_industry_variant', config('ai.prompts.industry_variant')),
                'brand_positioning' => BusinessSetting::get('ai_prompt_brand_positioning', config('ai.prompts.brand_positioning')),
                'landing_page' => BusinessSetting::get('ai_prompt_landing_page', config('ai.prompts.landing_page')),
                'home_page' => BusinessSetting::get('ai_prompt_home_page', config('ai.prompts.home_page')),
            ],
        ];

        return view('admin.ai.settings', compact('settings'));
    }

    private function decryptKey(?string $key): ?string
    {
        if (! $key) {
            return null;
        }

        try {
            return Crypt::decryptString($key);
        } catch (\Exception $e) {
            return $key;
        }
    }

    /**
     * Fetch latest available models from the active provider's API.
     */
    public function fetchModels(Request $request)
    {
        try {
            $provider = $request->input('provider', BusinessSetting::get('ai_provider', config('ai.provider', 'anthropic')));

            if ($provider === 'openrouter') {
                return $this->fetchOpenRouterModels($request);
            }

            return $this->fetchAnthropicModels($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching models: '.$e->getMessage(),
            ], 500);
        }
    }

    private function fetchAnthropicModels(Request $request): \Illuminate\Http\JsonResponse
    {
        $apiKey = $request->input('api_key');

        if (! $apiKey) {
            $apiKey = $this->decryptKey(BusinessSetting::get('ai_api_key'));
        }

        if (! $apiKey) {
            $apiKey = config('ai.anthropic.api_key');
        }

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'No Anthropic API key configured. Please set your API key first.',
            ], 400);
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->get('https://api.anthropic.com/v1/models');

        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch models from Anthropic. Please check your API key.',
            ], 400);
        }

        $modelList = collect($response->json('data', []))
            ->pluck('id')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        Cache::put('ai_available_models_anthropic', $modelList, now()->addHours(24));

        return response()->json([
            'success' => true,
            'models' => $modelList,
            'message' => 'Models fetched successfully. '.count($modelList).' models available.',
        ]);
    }

    private function fetchOpenRouterModels(Request $request): \Illuminate\Http\JsonResponse
    {
        $apiKey = $request->input('api_key');

        if (! $apiKey) {
            $apiKey = $this->decryptKey(BusinessSetting::get('ai_openrouter_api_key'));
        }

        if (! $apiKey) {
            $apiKey = config('ai.openrouter.api_key');
        }

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'No OpenRouter API key configured. Please set your API key first.',
            ], 400);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
        ])->get('https://openrouter.ai/api/v1/models');

        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch models from OpenRouter. Please check your API key.',
            ], 400);
        }

        $modelList = collect($response->json('data', []))
            ->pluck('id')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        Cache::put('ai_available_models_openrouter', $modelList, now()->addHours(24));

        return response()->json([
            'success' => true,
            'models' => $modelList,
            'message' => 'Models fetched successfully. '.count($modelList).' models available.',
        ]);
    }

    /**
     * Get default model list when API is unavailable.
     */
    private function getDefaultModels(string $provider = 'anthropic'): array
    {
        return match ($provider) {
            'openrouter' => [
                'openai/gpt-4o',
                'openai/gpt-4o-mini',
                'openai/o1',
                'anthropic/claude-opus-4',
                'anthropic/claude-sonnet-4',
                'google/gemini-2.5-pro',
                'meta-llama/llama-3.3-70b-instruct',
            ],
            default => [
                'claude-3-5-sonnet-20241022',
                'claude-3-5-haiku-20241022',
                'claude-3-opus-20240229',
                'claude-opus-4-1',
            ],
        };
    }

    /**
     * Update AI settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => ['nullable', 'string', 'in:anthropic,openrouter'],
            'api_key' => ['nullable', 'string', 'regex:/^sk-ant-[a-zA-Z0-9_-]+$/'],
            'openrouter_api_key' => ['nullable', 'string', 'regex:/^sk-or-[a-zA-Z0-9_-]+$/'],
            'remove_api_key' => ['nullable', 'boolean'],
            'remove_openrouter_api_key' => ['nullable', 'boolean'],
            'model' => ['nullable', 'string', 'min:3', 'max:100'],
            'max_tokens' => ['nullable', 'integer', 'min:100', 'max:8000'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'prompt_blog_post' => ['nullable', 'string', 'max:5000'],
            'prompt_seo_metadata' => ['nullable', 'string', 'max:5000'],
            'prompt_content_improvement' => ['nullable', 'string', 'max:5000'],
            'prompt_industry_variant' => ['nullable', 'string', 'max:5000'],
            'prompt_brand_positioning' => ['nullable', 'string', 'max:5000'],
            'prompt_landing_page' => ['nullable', 'string', 'max:5000'],
            'prompt_home_page' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.ai.settings.index')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        try {
            // Save provider selection
            if (isset($validated['provider'])) {
                BusinessSetting::set('ai_provider', $validated['provider'], 'string');
            }

            // Handle Anthropic API key
            if ($request->filled('remove_api_key') && $validated['remove_api_key']) {
                BusinessSetting::where('key', 'ai_api_key')->delete();
            } elseif (! empty($validated['api_key'])) {
                BusinessSetting::set('ai_api_key', Crypt::encryptString($validated['api_key']), 'string');
            }

            // Handle OpenRouter API key
            if ($request->filled('remove_openrouter_api_key') && $validated['remove_openrouter_api_key']) {
                BusinessSetting::where('key', 'ai_openrouter_api_key')->delete();
            } elseif (! empty($validated['openrouter_api_key'])) {
                BusinessSetting::set('ai_openrouter_api_key', Crypt::encryptString($validated['openrouter_api_key']), 'string');
            }

            // Save shared settings
            if (isset($validated['model'])) {
                BusinessSetting::set('ai_model', $validated['model'], 'string');
            }

            if (isset($validated['max_tokens'])) {
                BusinessSetting::set('ai_max_tokens', $validated['max_tokens'], 'integer');
            }

            if (isset($validated['temperature'])) {
                BusinessSetting::set('ai_temperature', $validated['temperature'], 'float');
            }

            // Save custom prompts
            if (isset($validated['prompt_blog_post'])) {
                BusinessSetting::set('ai_prompt_blog_post', $validated['prompt_blog_post'], 'text');
            }

            if (isset($validated['prompt_seo_metadata'])) {
                BusinessSetting::set('ai_prompt_seo_metadata', $validated['prompt_seo_metadata'], 'text');
            }

            if (isset($validated['prompt_content_improvement'])) {
                BusinessSetting::set('ai_prompt_content_improvement', $validated['prompt_content_improvement'], 'text');
            }

            if (isset($validated['prompt_industry_variant'])) {
                BusinessSetting::set('ai_prompt_industry_variant', $validated['prompt_industry_variant'], 'text');
            }

            if (isset($validated['prompt_brand_positioning'])) {
                BusinessSetting::set('ai_prompt_brand_positioning', $validated['prompt_brand_positioning'], 'text');
            }

            if (isset($validated['prompt_landing_page'])) {
                BusinessSetting::set('ai_prompt_landing_page', $validated['prompt_landing_page'], 'text');
            }

            if (isset($validated['prompt_home_page'])) {
                BusinessSetting::set('ai_prompt_home_page', $validated['prompt_home_page'], 'text');
            }

            return redirect()
                ->route('admin.ai.settings.index')
                ->with('success', 'AI settings updated successfully.');
        } catch (\Exception $e) {
            \Log::error('[AI Settings] Failed to save AI settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.ai.settings.index')
                ->withErrors(['error' => 'Failed to save AI settings: '.$e->getMessage()])
                ->withInput();
        }
    }
}
