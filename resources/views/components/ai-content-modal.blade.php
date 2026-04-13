{{-- AI Content Generation Modal Component --}}
<div x-data="aiContentGenerator()" 
     @open-ai-modal.window="openModal($event.detail)"
     x-show="isOpen" 
     x-cloak
     class="fixed inset-0 z-[100] overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" 
         @click="closeModal()"
         x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Modal Dialog -->
    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-2xl"
             x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-magic text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">AI Content Generator</h3>
                        <p class="text-sm text-gray-400">Powered by Claude AI</p>
                    </div>
                </div>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6">
                <!-- Content Type Selector -->
                <div>
                    <label class="form-label">Content Type</label>
                    <select x-model="contentType" class="form-select">
                        <option value="blog_post">Blog Post / Article</option>
                        <option value="landing_page">Landing Page</option>
                        <option value="home_page">Home Page</option>
                        <option value="brand_positioning">Brand Positioning</option>
                        <option value="content_improvement">Improve Existing Content</option>
                        <option value="industry_variant">Industry-Specific Variant</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">AI will return semantic HTML using the allowed CSS classes (hero, section, feature-grid, feature-card, btn-*). No inline styles.</p>
                </div>

                <!-- Prompt Input -->
                <div>
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <label class="form-label">
                            <span x-show="contentType === 'blog_post' || contentType === 'landing_page' || contentType === 'home_page' || contentType === 'brand_positioning'">
                                What would you like to create?
                            </span>
                            <span x-show="contentType === 'content_improvement'" x-cloak>
                                What should be improved?
                            </span>
                            <span x-show="contentType === 'industry_variant'" x-cloak>
                                Target Industry
                            </span>
                        </label>
                        <button type="button" 
                                @click="loadExamplePrompt()"
                                x-show="hasExamplePrompt()"
                                class="text-xs px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors"
                                :disabled="isGenerating">
                            <i class="fas fa-lightbulb mr-1"></i>Use Example
                        </button>
                    </div>
                    <textarea x-model="prompt" 
                              rows="4" 
                              class="form-input" 
                              :placeholder="getPlaceholder()"
                              :disabled="isGenerating"></textarea>
                </div>

                <!-- Existing Content (for improvement/variant) -->
                <div x-show="contentType === 'content_improvement' || contentType === 'industry_variant'" x-cloak>
                    <label class="form-label">Existing Content</label>
                    <textarea x-model="existingContent" 
                              rows="6" 
                              class="form-input font-mono text-sm" 
                              placeholder="Paste the content you want to improve or adapt..."
                              :disabled="isGenerating"></textarea>
                </div>

                <!-- Advanced Options (Collapsible) -->
                <div x-data="{ showAdvanced: false }" class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <button type="button" 
                            @click="showAdvanced = !showAdvanced"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white flex items-center gap-2">
                        <i class="fas fa-cog"></i>
                        Advanced Options
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': showAdvanced }"></i>
                    </button>
                    
                    <div x-show="showAdvanced" x-collapse class="mt-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label text-sm">Temperature (Creativity)</label>
                                <input type="range" 
                                       x-model="temperature" 
                                       min="0" 
                                       max="1" 
                                       step="0.1"
                                       class="w-full">
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>Precise</span>
                                    <span x-text="temperature"></span>
                                    <span>Creative</span>
                                </div>
                            </div>
                            <div>
                                <label class="form-label text-sm">Max Length (tokens)</label>
                                <input type="number" 
                                       x-model="maxTokens" 
                                       min="100" 
                                       max="4096" 
                                       step="100"
                                       class="form-input">
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-sm">Custom System Prompt (Optional)</label>
                            <textarea x-model="customSystemPrompt" 
                                      rows="3" 
                                      class="form-input text-sm font-mono" 
                                      placeholder="Override the default AI instructions..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Generated Content Preview -->
                <div x-show="generatedContent" x-cloak class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900 dark:text-white">Generated Content</h4>
                        <button type="button" 
                                @click="copyToClipboard()"
                                class="text-sm text-blue-400 hover:text-blue-300">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                    <div class="max-w-none max-h-96 overflow-y-auto">
                        <pre class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap font-mono" x-html="generatedContent"></pre>
                    </div>
                </div>

                <!-- Error Display -->
                <div x-show="error" x-cloak class="bg-red-900/20 border border-red-500 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-red-400">Error</p>
                            <p class="text-sm text-red-300" x-text="error"></p>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="isGenerating" x-cloak class="text-center py-8">
                    <div class="inline-flex items-center gap-3 text-blue-400">
                        <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                        <span class="text-lg">Generating content...</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">This may take 10-30 seconds</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-800/50">
                <div class="text-sm text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Content will inherit your theme styles
                </div>
                <div class="flex gap-3">
                    <button type="button" 
                            @click="closeModal()" 
                            class="btn-secondary"
                            :disabled="isGenerating">
                        Cancel
                    </button>
                    <button type="button" 
                            @click="generate()" 
                            class="btn-primary"
                            :disabled="isGenerating || !prompt"
                            x-show="!generatedContent">
                        <i class="fas fa-magic mr-2"></i>
                        Generate Content
                    </button>
                    <button type="button" 
                            @click="insertContent()" 
                            class="btn-primary"
                            x-show="generatedContent"
                            x-cloak>
                        <i class="fas fa-check mr-2"></i>
                        Insert Content
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Ensure this runs after Alpine is loaded
document.addEventListener('alpine:init', () => {
    Alpine.data('aiContentGenerator', () => ({
        isOpen: false,
        targetField: null,
        contentType: 'blog_post',
        prompt: '',
        existingContent: '',
        customSystemPrompt: '',
        temperature: 0.7,
        maxTokens: 2048,
        generatedContent: '',
        error: '',
        isGenerating: false,

        openModal(detail) {
            this.isOpen = true;
            this.targetField = detail?.target || 'body_content';
            this.contentType = detail?.contentType || 'blog_post';
            this.reset();
            
            // Pre-populate existing content if improving
            if (this.contentType === 'content_improvement' || this.contentType === 'industry_variant') {
                const targetElement = document.getElementById(this.targetField);
                if (targetElement) {
                    const cm = targetElement._codeMirrorInstance;
                    this.existingContent = cm ? cm.getValue() : targetElement.value;
                    if (!this.prompt) {
                        this.prompt = 'Improve the existing page body content while preserving its HTML structure and classes. Fix clarity, tone, flow, and microcopy. Keep length similar, enhance headings/CTA, and do not add new frameworks or inline styles.';
                    }
                }
            }
        },

        closeModal() {
            this.isOpen = false;
            this.reset();
        },

        reset() {
            this.prompt = '';
            this.generatedContent = '';
            this.error = '';
            this.isGenerating = false;
        },

        getPlaceholder() {
            const placeholders = {
                'blog_post': 'Example: Write a blog post about sustainable building practices in modern construction...',
                'landing_page': 'Example: Create a landing page for a local contractor (Home Pro Inc). Include seasonal services: snow removal (winter), decks (summer), gutters (fall), flowers/landscaping (spring). Tone: friendly, trustworthy, community-focused. Include hero, subheadline, bullets, CTA, testimonial.',
                'home_page': 'Example: Home page for Home Pro Inc covering seasonal services (snow, decks, gutters, flowers). Keep it clean, semantic HTML, hero + value props + CTA.',
                'brand_positioning': 'Example: Position Home Pro Inc as the friendly, year-round neighborhood contractor (seasonal services: snow, decks, gutters, flowers).',
                'content_improvement': 'Describe what you want to improve (tone, clarity, engagement, etc.)',
                'industry_variant': 'Example: Construction, Healthcare, Technology, Real Estate...'
            };
            return placeholders[this.contentType] || 'Describe what you want to create...';
        },

        hasExamplePrompt() {
            return ['landing_page', 'home_page', 'brand_positioning'].includes(this.contentType);
        },

        loadExamplePrompt() {
            const examples = {
                'landing_page': 'Create a landing page for a local contractor (Home Pro Inc). Include seasonal services: snow removal (winter), decks (summer), gutters (fall), flowers/landscaping (spring). Tone: friendly, trustworthy, community-focused. Include hero, subheadline, bullets, CTA, testimonial.',
                'home_page': 'Home page for Home Pro Inc covering seasonal services (snow, decks, gutters, flowers). Keep it clean, semantic HTML, hero + value props + CTA.',
                'brand_positioning': 'Position Home Pro Inc as the friendly, year-round neighborhood contractor (seasonal services: snow, decks, gutters, flowers).'
            };
            if (examples[this.contentType]) {
                this.prompt = examples[this.contentType];
            }
        },

        async generate() {
            if (!this.prompt.trim()) {
                this.error = 'Please enter a prompt';
                return;
            }

            this.isGenerating = true;
            this.error = '';
            this.generatedContent = '';

            const payload = {
                content_type: this.contentType,
                prompt: this.prompt,
                temperature: parseFloat(this.temperature),
                max_tokens: parseInt(this.maxTokens)
            };

            if (this.customSystemPrompt) {
                payload.custom_system_prompt = this.customSystemPrompt;
            }

            if (this.contentType === 'content_improvement' || this.contentType === 'industry_variant') {
                payload.existing_content = this.existingContent;
            }

            if (this.contentType === 'industry_variant') {
                payload.industry = this.prompt;
            }

            try {
                const response = await fetch('{{ route("admin.ai.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const rawBody = await response.text();
                let data;
                try {
                    data = JSON.parse(rawBody);
                } catch (parseErr) {
                    console.error('AI generate: failed to parse JSON', parseErr, rawBody);
                    data = null;
                }

                // Debug output for server responses
                console.debug('AI generate response', {
                    status: response.status,
                    ok: response.ok,
                    data,
                    rawBody
                });

                if (!response.ok) {
                    const message = data?.error || `AI generation failed (HTTP ${response.status})`;
                    this.error = message;
                    return;
                }

                if (data?.success) {
                    this.generatedContent = data.content;
                } else {
                    this.error = data?.error || 'Failed to generate content';
                }
            } catch (err) {
                this.error = 'Network error. Please check your connection and try again.';
                console.error('AI Generation Error:', err);
            } finally {
                this.isGenerating = false;
            }
        },

        insertContent() {
            if (!this.generatedContent) return;

            const targetElement = document.getElementById(this.targetField);
            if (!targetElement) return;

            // Check if there's existing content
            let hasExistingContent = false;
            const cm = targetElement._codeMirrorInstance;
            if (cm) {
                hasExistingContent = cm.getValue().trim().length > 0;
            } else {
                hasExistingContent = targetElement.value.trim().length > 0;
            }

            // Warn user about content replacement
            if (hasExistingContent) {
                if (!confirm('⚠️ Inserting AI content will REPLACE your current content.\n\nThis cannot be undone. Continue?')) {
                    return;
                }
            }

            // Insert content
            if (cm) {
                cm.setValue(this.generatedContent);
                cm.refresh();
                cm.focus();
                cm.save();
            } else {
                targetElement.value = this.generatedContent;
            }
            
            // Trigger change event for any listeners
            targetElement.dispatchEvent(new Event('input', { bubbles: true }));
            targetElement.dispatchEvent(new Event('change', { bubbles: true }));

            this.closeModal();
        },

        copyToClipboard() {
            navigator.clipboard.writeText(this.generatedContent).then(() => {
                // Could add a toast notification here
                alert('Content copied to clipboard!');
            });
        }
    }));
});
</script>
