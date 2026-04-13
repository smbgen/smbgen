@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">AI Content Generation Settings</h1>
            <p class="admin-page-subtitle">Configure Claude AI for content generation</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Settings -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.ai.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Configuration Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-center">
                                @if($settings['enabled'])
                                    <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">AI Enabled</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Content generation is active</p>
                                    </div>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">AI Disabled</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Set AI_CONTENT_GENERATION_ENABLED=true in .env</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-center">
                                @if($settings['api_key_set'])
                                    <i class="fas fa-key text-green-500 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">API Key Configured</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Anthropic API key is set</p>
                                    </div>
                                @else
                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">API Key Required</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Add ANTHROPIC_API_KEY to your .env file</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Key Configuration -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">API Key</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Your Anthropic API key. Get one from <a href="https://console.anthropic.com/settings/keys" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">console.anthropic.com</a>
                    </p>
                    
                    <div class="space-y-4" x-data="{ showKey: false }">
                        @if($settings['api_key_set'])
                            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        API Key configured 
                                        @if($settings['api_key_last_4'])
                                            <code class="ml-2 text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded">{{ $settings['api_key_last_4'] }}</code>
                                        @endif
                                        @if($settings['api_key_in_db'])
                                            <span class="ml-2 text-xs text-gray-500">(stored in database)</span>
                                        @else
                                            <span class="ml-2 text-xs text-gray-500">(from .env)</span>
                                        @endif
                                    </span>
                                </div>
                                @if($settings['api_key_in_db'])
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="remove_api_key" value="1" class="form-checkbox mr-2">
                                        <span class="text-sm text-red-600 dark:text-red-400">Remove</span>
                                    </label>
                                @endif
                            </div>
                        @endif
                        
                        <div>
                            <label class="form-label">
                                {{ $settings['api_key_set'] ? 'Update API Key' : 'Enter API Key' }}
                            </label>
                            <div class="relative">
                                <input :type="showKey ? 'text' : 'password'" 
                                       name="api_key" 
                                       value="" 
                                       class="form-input pr-10" 
                                       placeholder="sk-ant-api03-xxxxxxxxxxxxx"
                                       autocomplete="off"
                                       data-1p-ignore
                                       data-lpignore="true"
                                       data-form-type="other"
                                       data-bwignore="true"
                                       role="presentation"
                                       readonly
                                       onfocus="this.removeAttribute('readonly');">
                                <button type="button" 
                                        @click="showKey = !showKey"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <i :class="showKey ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @if($settings['api_key_set'])
                                    Leave blank to keep existing key. Enter a new key to update.
                                @else
                                    Starts with "sk-ant-". You can also set ANTHROPIC_API_KEY in your .env file.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Model Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" x-data="modelFetcher()">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Model Configuration</h3>
                        <button type="button" 
                                @click="fetchModels()" 
                                :disabled="isFetching"
                                class="text-sm px-3 py-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-500 text-white rounded transition-colors flex items-center gap-2">
                            <i :class="isFetching ? 'fas fa-spinner fa-spin' : 'fas fa-sync-alt'"></i>
                            <span x-text="isFetching ? 'Fetching...' : 'Fetch Latest'"></span>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Claude Model</label>
                            <select name="model" class="form-select">
                                @foreach($settings['available_models'] as $model)
                                    <option value="{{ $model }}" {{ $settings['model'] === $model ? 'selected' : '' }}>
                                        {{ $model }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Select the Claude model to use for content generation. Click "Fetch Latest" to refresh available models.
                            </p>
                        </div>

                        <div>
                            <label class="form-label">Max Tokens</label>
                            <input type="number" 
                                   name="max_tokens" 
                                   value="{{ old('max_tokens', (int) $settings['max_tokens']) }}" 
                                   class="form-input" 
                                   min="100" 
                                   max="8000" 
                                   step="100"
                                   autocomplete="off"
                                   data-1p-ignore
                                   data-lpignore="true"
                                   data-form-type="other"
                                   data-bwignore="true"
                                   role="presentation"
                                   readonly
                                   onfocus="this.removeAttribute('readonly');">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Maximum length of generated content (100-8000). Higher = longer content but more cost.
                            </p>
                        </div>

                        <div>
                            <label class="form-label">Temperature</label>
                            <input type="number" 
                                   name="temperature" 
                                   value="{{ old('temperature', $settings['temperature']) }}" 
                                   class="form-input" 
                                   min="0" 
                                   max="1" 
                                   step="0.1"
                                   autocomplete="off"
                                   data-1p-ignore
                                   data-lpignore="true">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Controls creativity (0-1). Lower = more focused, Higher = more creative. Recommended: 0.7
                            </p>
                        </div>
                    </div>
                </div>

                <!-- System Prompts -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Prompts</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Customize how the AI generates content. These prompts define the rules and guidelines for content generation.
                    </p>
                    
                    <div class="space-y-6" x-data="{ activePrompt: 'blog_post' }">
                        <!-- Prompt Tabs -->
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="-mb-px flex space-x-4 overflow-x-auto">
                                <button type="button" @click="activePrompt = 'blog_post'" 
                                        :class="activePrompt === 'blog_post' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    Blog Post
                                </button>
                                <button type="button" @click="activePrompt = 'seo'" 
                                        :class="activePrompt === 'seo' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    SEO Metadata
                                </button>
                                <button type="button" @click="activePrompt = 'improve'" 
                                        :class="activePrompt === 'improve' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    Content Improvement
                                </button>
                                <button type="button" @click="activePrompt = 'industry'" 
                                        :class="activePrompt === 'industry' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    Industry Variant
                                </button>
                                <button type="button" @click="activePrompt = 'brand'" 
                                        :class="activePrompt === 'brand' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    Brand Positioning
                                </button>
                                <button type="button" @click="activePrompt = 'landing'" 
                                        :class="activePrompt === 'landing' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    Landing Page
                                </button>
                                <button type="button" @click="activePrompt = 'home'" 
                                        :class="activePrompt === 'home' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    Home Page
                                </button>
                            </nav>
                        </div>

                        <!-- Blog Post Prompt -->
                        <div x-show="activePrompt === 'blog_post'" x-cloak>
                            <label class="form-label">Blog Post Generation Prompt</label>
                            <textarea name="prompt_blog_post" rows="8" class="form-input font-mono text-sm">{{ old('prompt_blog_post', $settings['prompts']['blog_post']) }}</textarea>
                        </div>

                        <!-- SEO Metadata Prompt -->
                        <div x-show="activePrompt === 'seo'" x-cloak>
                            <label class="form-label">SEO Metadata Generation Prompt</label>
                            <textarea name="prompt_seo_metadata" rows="8" class="form-input font-mono text-sm">{{ old('prompt_seo_metadata', $settings['prompts']['seo_metadata']) }}</textarea>
                        </div>

                        <!-- Content Improvement Prompt -->
                        <div x-show="activePrompt === 'improve'" x-cloak>
                            <label class="form-label">Content Improvement Prompt</label>
                            <textarea name="prompt_content_improvement" rows="8" class="form-input font-mono text-sm">{{ old('prompt_content_improvement', $settings['prompts']['content_improvement']) }}</textarea>
                        </div>

                        <!-- Industry Variant Prompt -->
                        <div x-show="activePrompt === 'industry'" x-cloak>
                            <label class="form-label">Industry Variant Prompt</label>
                            <textarea name="prompt_industry_variant" rows="8" class="form-input font-mono text-sm">{{ old('prompt_industry_variant', $settings['prompts']['industry_variant']) }}</textarea>
                        </div>

                        <!-- Brand Positioning Prompt -->
                        <div x-show="activePrompt === 'brand'" x-cloak>
                            <label class="form-label">Brand Positioning Prompt</label>
                            <textarea name="prompt_brand_positioning" rows="8" class="form-input font-mono text-sm">{{ old('prompt_brand_positioning', $settings['prompts']['brand_positioning']) }}</textarea>
                        </div>

                        <!-- Landing Page Prompt -->
                        <div x-show="activePrompt === 'landing'" x-cloak>
                            <label class="form-label">Landing Page Content Generation Prompt</label>
                            <textarea name="prompt_landing_page" rows="8" class="form-input font-mono text-sm">{{ old('prompt_landing_page', $settings['prompts']['landing_page']) }}</textarea>
                        </div>

                        <!-- Home Page Prompt -->
                        <div x-show="activePrompt === 'home'" x-cloak>
                            <label class="form-label">Home Page Content Generation Prompt</label>
                            <textarea name="prompt_home_page" rows="8" class="form-input font-mono text-sm">{{ old('prompt_home_page', $settings['prompts']['home_page']) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Guide -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>Quick Setup Guide
                </h3>
                <ol class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                    <li class="flex">
                        <span class="font-bold mr-2">1.</span>
                        <span>Get an API key from <a href="https://console.anthropic.com/" target="_blank" class="underline">console.anthropic.com</a></span>
                    </li>
                    <li class="flex">
                        <span class="font-bold mr-2">2.</span>
                        <span>Enter your API key in the form above</span>
                    </li>
                    <li class="flex">
                        <span class="font-bold mr-2">3.</span>
                        <span>Set <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">AI_CONTENT_GENERATION_ENABLED=true</code> in .env</span>
                    </li>
                    <li class="flex">
                        <span class="font-bold mr-2">4.</span>
                        <span>Customize model and prompts as needed</span>
                    </li>
                </ol>
            </div>

            <!-- Pricing Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-dollar-sign mr-2"></i>Pricing (Dec 2024)
                </h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">Claude 3.5 Sonnet</p>
                        <p class="text-gray-600 dark:text-gray-400">$3 / 1M input tokens</p>
                        <p class="text-gray-600 dark:text-gray-400">$15 / 1M output tokens</p>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-3">
                        Typical blog post: ~$0.02-$0.05<br>
                        SEO metadata: ~$0.001-$0.005
                    </div>
                </div>
            </div>

            <!-- Documentation -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-book mr-2"></i>Documentation
                </h3>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="https://docs.anthropic.com/claude/docs" target="_blank" 
                           class="text-blue-600 dark:text-blue-400 hover:underline">
                            Claude API Docs
                        </a>
                    </li>
                    <li>
                        <a href="https://console.anthropic.com/settings/keys" target="_blank" 
                           class="text-blue-600 dark:text-blue-400 hover:underline">
                            Manage API Keys
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('modelFetcher', () => ({
        isFetching: false,
        
        async fetchModels() {
            this.isFetching = true;
            
            try {
                const response = await fetch('{{ route("admin.ai.fetch-models") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update select options
                    const select = document.querySelector('select[name="model"]');
                    const currentValue = select.value;
                    
                    select.innerHTML = '';
                    data.models.forEach(model => {
                        const option = document.createElement('option');
                        option.value = model;
                        option.textContent = model;
                        option.selected = model === currentValue;
                        select.appendChild(option);
                    });
                    
                    // Show success message
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Failed to fetch models: ' + error.message);
            } finally {
                this.isFetching = false;
            }
        }
    }));
});
</script>
