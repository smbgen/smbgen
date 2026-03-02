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
        $dbApiKey = BusinessSetting::get('ai_api_key');
        $envApiKey = config('ai.anthropic.api_key');

        // Decrypt database API key if it exists
        $decryptedDbKey = null;
        if ($dbApiKey) {
            try {
                $decryptedDbKey = Crypt::decryptString($dbApiKey);
            } catch (\Exception $e) {
                // If decryption fails, key might be stored unencrypted (legacy)
                $decryptedDbKey = $dbApiKey;
            }
        }

        $apiKeySet = ! empty($decryptedDbKey) || ! empty($envApiKey);

        // Get available models from cache or default list
        $availableModels = Cache::get('ai_available_models', $this->getDefaultModels());

        $settings = [
            'enabled' => config('ai.enabled', false),
            'api_key_set' => $apiKeySet,
            'api_key_in_db' => ! empty($dbApiKey),
            'api_key_last_4' => $decryptedDbKey ? '****'.substr($decryptedDbKey, -4) : ($envApiKey ? '****'.substr($envApiKey, -4) : null),
            'model' => BusinessSetting::get('ai_model', config('ai.anthropic.model')),
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

    /**
     * Fetch latest available models from Anthropic API.
     */
    public function fetchModels(Request $request)
    {
        try {
            $apiKey = $request->input('api_key');
            
            if (!$apiKey) {
                $apiKey = BusinessSetting::get('ai_api_key');
                if ($apiKey) {
                    try {
                        $apiKey = Crypt::decryptString($apiKey);
                    } catch (\Exception $e) {
                        $apiKey = $apiKey; // Already plaintext (legacy)
                    }
                }
            }

            if (!$apiKey) {
                $apiKey = config('ai.anthropic.api_key');
            }

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'No API key configured. Please set your Anthropic API key first.',
                ], 400);
            }

            // Fetch models from Anthropic API
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])->get('https://api.anthropic.com/v1/models');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch models from Anthropic. Please check your API key.',
                ], 400);
            }

            $models = $response->json('data', []);
            
            // Extract model IDs and sort
            $modelList = collect($models)
                ->pluck('id')
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            // Cache the models for 24 hours
            Cache::put('ai_available_models', $modelList, now()->addHours(24));

            return response()->json([
                'success' => true,
                'models' => $modelList,
                'message' => 'Models fetched successfully. '.count($modelList).' models available.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching models: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get default model list when API is unavailable.
     */
    private function getDefaultModels(): array
    {
        return [
            'claude-3-5-sonnet-20241022',
            'claude-3-5-haiku-20241022',
            'claude-3-opus-20240229',
            'claude-opus-4-1',
        ];
    }

    /**
     * Update AI settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_key' => ['nullable', 'string', 'regex:/^sk-ant-[a-zA-Z0-9_-]+$/'],
            'remove_api_key' => ['nullable', 'boolean'],
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
            // Handle API key
            if ($request->filled('remove_api_key') && $validated['remove_api_key']) {
                // Remove API key from database
                BusinessSetting::where('key', 'ai_api_key')->delete();
            } elseif (isset($validated['api_key']) && ! empty($validated['api_key'])) {
                // Encrypt API key before storing in database
                $encryptedKey = Crypt::encryptString($validated['api_key']);
                BusinessSetting::set('ai_api_key', $encryptedKey, 'string');
            }

            // Save settings to database
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
