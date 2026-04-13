@extends('layouts.admin')

@section('content')
<div x-data="{}">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
                Create CMS Page
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Add a new content page</p>
        </div>
        <a href="{{ route('admin.cms.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Pages
        </a>
    </div>

    <div>
        {{-- Success Message --}}
        @if (session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg relative flex items-center justify-between shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 8000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg relative flex items-center justify-between shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg relative shadow-lg">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                            <span class="font-medium">Please fix the following errors:</span>
                        </div>
                        <ul class="ml-8 list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.cms.store') }}" method="POST" class="space-y-6" 
                          x-data="{ submitting: false }" 
                          @submit="submitting = true">
                        @csrf

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   placeholder="home" required>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">URL-friendly name (e.g., "home", "about", "contact")</p>
                            <div id="slug-warning" class="hidden mt-2 p-3 rounded bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 text-sm">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Reserved slug!</strong> This slug is reserved and cannot be used. Please choose a different slug.
                            </div>
                            <div class="mt-2 p-3 rounded bg-gradient-to-r from-green-400 to-blue-400 text-white text-xs font-bold shadow">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pages with slug <code>home</code> or <code>landing</code> will become the root route of the site.<br>
                                <span class="font-normal">See <a href="/app/docs/web-routes.md" target="_blank" class="underline">Web Routes Documentation</a> for details.</span>
                            </div>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Page Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   placeholder="Welcome to Our Site" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Head Content -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="head_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Head Content (Meta Tags, CSS, Scripts)
                                </label>
                                <button type="button" class="btn-secondary text-xs" data-editor-toggle="head_content" onclick="toggleEditorFullscreen('head_content', this)">
                                    <i class="fas fa-expand mr-1"></i>Expand
                                </button>
                            </div>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mb-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Company colors are automatically injected via <a href="{{ route('admin.cms.index') }}" class="underline hover:text-blue-800 dark:hover:text-blue-200">global settings</a>. Use CSS variables: --brand-primary, --brand-secondary, --brand-navbar, --brand-text, --brand-accent
                            </p>
                            <div class="relative">
                                <textarea name="head_content" id="head_content" rows="4" 
                                          class="code-editor w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white font-mono text-sm"
                                          placeholder="<meta name=&quot;description&quot; content=&quot;Your description&quot;>">{{ old('head_content') }}</textarea>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Optional HTML to inject in the &lt;head&gt; section</p>
                            @error('head_content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Body Content -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="body_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Body Content (HTML)
                                </label>
                                @if(config('ai.enabled'))
                                <div class="flex gap-2">
                                        <button type="button" 
                                            onclick="toggleEditorFullscreen('body_content', this)"
                                            data-editor-toggle="body_content"
                                            class="btn-secondary text-sm">
                                        <i class="fas fa-expand mr-2"></i>Expand
                                    </button>
                                    <button type="button" 
                                            @click.prevent="$dispatch('open-ai-modal', { target: 'body_content', contentType: 'content_improvement' })"
                                            class="btn-secondary text-sm">
                                        <i class="fas fa-robot mr-2"></i>Improve with AI
                                    </button>
                                    <button type="button" 
                                            @click.prevent="$dispatch('open-ai-modal', { target: 'body_content', contentType: 'landing_page' })"
                                            class="btn-secondary text-sm">
                                        <i class="fas fa-sparkles mr-2"></i>Generate with AI
                                    </button>
                                </div>
                                @endif
                            </div>
                            @if(config('ai.enabled'))
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 mb-2">
                                <i class="fas fa-exclamation-triangle mr-1"></i>AI-generated content will replace existing content
                            </p>
                            @endif
                            <div class="relative">
                                <textarea name="body_content" id="body_content" rows="20" 
                                          class="code-editor w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white font-mono text-sm"
                                          placeholder="<div class=&quot;container&quot;>Your HTML content here</div>">{{ old('body_content') }}</textarea>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Main page HTML content</p>
                            @error('body_content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Footer Scripts (after body loads) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="footer_scripts" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Footer Scripts (Runs After Page Load)
                                </label>
                                <button type="button" 
                                    onclick="toggleEditorFullscreen('footer_scripts', this)"
                                    data-editor-toggle="footer_scripts"
                                    class="btn-secondary text-sm">
                                    <i class="fas fa-expand mr-2"></i>Expand
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                <i class="fas fa-info-circle mr-1"></i>Scripts here run after the DOM is loaded - perfect for canvas animations, interactive elements, etc.
                            </p>
                            <div class="relative">
                                <textarea name="footer_scripts" id="footer_scripts" rows="12" 
                                          class="code-editor w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white font-mono text-sm"
                                          placeholder="<script>&#10;  // Your JavaScript here&#10;</script>">{{ old('footer_scripts') }}</textarea>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Scripts that need access to DOM elements</p>
                            @error('footer_scripts')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>



                        <!-- Form Builder Section -->
                        <div x-data="formBuilder()" class="border border-gray-300 dark:border-gray-600 rounded-lg p-6 bg-white dark:bg-gray-900">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Form Builder (Optional)</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Add a customizable form to this page</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="hidden" name="has_form" value="0">
                                    <input type="checkbox" name="has_form" id="has_form" value="1" 
                                           x-model="hasForm" {{ old('has_form') ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="has_form" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-100">
                                        Enable Form
                                    </label>
                                </div>
                            </div>

                            <div x-show="hasForm" x-transition class="space-y-6">
                                <!-- Field Mapping Info -->
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">
                                        <i class="fas fa-info-circle mr-1"></i>Form Field Mapping
                                    </h4>
                                    <div class="text-xs text-blue-800 dark:text-blue-300 space-y-1">
                                        <p><strong>Standard Fields</strong> (stored in database columns):</p>
                                        <ul class="list-disc list-inside ml-2">
                                            <li><code>name</code> or <code>full_name</code> → Lead Name</li>
                                            <li><code>email</code> → Lead Email</li>
                                            <li><code>message</code>, <code>comments</code>, or <code>inquiry</code> → Lead Message</li>
                                        </ul>
                                        <p class="mt-2"><strong>Custom Fields</strong> (stored in form_data JSON):</p>
                                        <ul class="list-disc list-inside ml-2">
                                            <li><code>phone</code>, <code>property_address</code>, and any other fields</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Form Fields Builder -->
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Form Fields
                                        </label>
                                        <button type="button" @click="addField()" class="btn-primary text-xs">
                                            <i class="fas fa-plus mr-1"></i>Add Field
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(field, index) in fields" :key="index">
                                            <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                                <div class="grid grid-cols-12 gap-3">
                                                    <!-- Field Type -->
                                                    <div class="col-span-3">
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                                        <select x-model="field.type" class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                            <option value="text">Text</option>
                                                            <option value="email">Email</option>
                                                            <option value="tel">Phone</option>
                                                            <option value="textarea">Textarea</option>
                                                            <option value="number">Number</option>
                                                            <option value="date">Date</option>
                                                            <option value="select">Select</option>
                                                            <option value="checkbox">Checkbox</option>
                                                            <option value="radio">Radio</option>
                                                        </select>
                                                    </div>

                                                    <!-- Field Name -->
                                                    <div class="col-span-3">
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                                        <input type="text" x-model="field.name" placeholder="field_name" 
                                                               class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                    </div>

                                                    <!-- Field Label -->
                                                    <div class="col-span-4">
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                                                        <input type="text" x-model="field.label" placeholder="Field Label" 
                                                               class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                    </div>

                                                    <!-- Required Checkbox -->
                                                    <div class="col-span-1 flex items-end">
                                                        <label class="flex items-center">
                                                            <input type="checkbox" x-model="field.required" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                            <span class="ml-1 text-xs text-gray-600 dark:text-gray-400">Req</span>
                                                        </label>
                                                    </div>

                                                    <!-- Delete Button -->
                                                    <div class="col-span-1 flex items-end">
                                                        <button type="button" @click="removeField(index)" class="btn-danger text-xs w-full">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>

                                                    <!-- Placeholder -->
                                                    <div class="col-span-6">
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Placeholder</label>
                                                        <input type="text" x-model="field.placeholder" placeholder="Placeholder text" 
                                                               class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Hidden input to store form_fields JSON -->
                                    <input type="hidden" name="form_fields" :value="JSON.stringify(fields)">
                                </div>

                                <!-- Form Submit Button Text -->
                                <div>
                                    <label for="form_submit_button_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Submit Button Text
                                    </label>
                                    <input type="text" name="form_submit_button_text" id="form_submit_button_text" 
                                           value="{{ old('form_submit_button_text', 'Submit') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                           placeholder="Submit">
                                </div>

                                <!-- Form Success Message -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="form_success_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Success Message
                                        </label>
                                        <button type="button" class="btn-secondary text-xs" data-textarea-toggle="form_success_message" onclick="toggleTextareaFullscreen('form_success_message', this)">
                                            <i class="fas fa-expand mr-1"></i>Expand
                                        </button>
                                    </div>
                                    <textarea name="form_success_message" id="form_success_message" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                              placeholder="Thank you for your submission!">{{ old('form_success_message', 'Thank you for your submission! We will get back to you soon.') }}</textarea>
                                </div>

                                <!-- Form Redirect URL -->
                                <div>
                                    <label for="form_redirect_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Redirect URL (Optional)
                                    </label>
                                    <input type="text" name="form_redirect_url" id="form_redirect_url" 
                                           value="{{ old('form_redirect_url') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                           placeholder="/thank-you">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to show success message on the same page</p>
                                </div>

                                <!-- Email Notifications Section -->
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 space-y-4">
                                    <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-200 mb-3">
                                        <i class="fas fa-envelope mr-1"></i>Email Notifications
                                    </h4>

                                    <!-- Notification Email -->
                                    <div>
                                        <label for="notification_email" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Admin Notification Email
                                        </label>
                                        <input type="email" name="notification_email" id="notification_email" 
                                               value="{{ old('notification_email') }}" 
                                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                               placeholder="admin@example.com">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email address to receive new form submissions</p>
                                    </div>

                                    <!-- Send Admin Notification -->
                                    <div class="flex items-center">
                                        <input type="checkbox" name="send_admin_notification" id="send_admin_notification" value="1" 
                                               {{ old('send_admin_notification') ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="send_admin_notification" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                            📧 Send admin notification email when form is submitted
                                        </label>
                                    </div>

                                    <!-- Send Client Confirmation -->
                                    <div class="flex items-center">
                                        <input type="checkbox" name="send_client_notification" id="send_client_notification" value="1" 
                                               {{ old('send_client_notification') ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="send_client_notification" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                            ✅ Send confirmation email to client
                                        </label>
                                    </div>

                                    <div class="text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700">
                                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                        <strong>Note:</strong> Both emails will be logged in Email Deliverability for tracking.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Published -->
                        <div class="flex items-center">
                            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_published" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                Publish this page immediately
                            </label>
                        </div>

                        <!-- Show Navbar -->
                        <div class="flex items-center">
                            <input type="hidden" name="show_navbar" value="0">
                            <input type="checkbox" name="show_navbar" id="show_navbar" value="1" {{ old('show_navbar', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="show_navbar" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                Show navigation bar on this page
                            </label>
                        </div>

                        <!-- Show Footer -->
                        <div class="flex items-center">
                            <input type="hidden" name="show_footer" value="0">
                            <input type="checkbox" name="show_footer" id="show_footer" value="1" {{ old('show_footer', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="show_footer" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                Show footer on this page
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.cms.index') }}" class="btn-secondary" :class="{ 'opacity-50 pointer-events-none': submitting }">
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary relative" :disabled="submitting">
                                <span :class="{ 'invisible': submitting }">
                                    <i class="fas fa-save mr-2"></i>Create Page
                                </span>
                                <span x-show="submitting" class="absolute inset-0 flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="ml-2">Creating...</span>
                                </span>
                            </button>
                        </div>
                        
                        {{-- Loading Overlay --}}
                        <div x-show="submitting" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 flex items-start justify-center pt-20">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl px-6 py-4 flex items-center space-x-4">
                                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">Creating page...</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Please wait while we set up your page</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        // Reserved slugs validation
        const reservedSlugs = [
            'login', 'register', 'logout', 'password', 'forgot-password', 'reset-password',
            'dashboard', 'admin', 'api', 'sanctum', 'livewire', 'telescope', 'horizon',
            'profile', 'settings', 'verify-email', 'email', 'auth', 'oauth',
            'messages', 'billing', 'payment', 'invoice', 'clients', 'users',
            'book', 'booking', 'schedule', 'availability', 'calendar',
            'track', 'webhook', 'magic-link', 'documents', 'files',
            'leads', 'leadform', 'landing2', 'cyber-audit-demo', 'seo-assistant',
            'status', 'health', 'debug', 'test', 'social-accounts', 'cms',
        ];

        const slugInput = document.getElementById('slug');
        const slugWarning = document.getElementById('slug-warning');
        const form = slugInput.closest('form');

        slugInput.addEventListener('input', function() {
            const slug = this.value.toLowerCase().trim();
            if (reservedSlugs.includes(slug)) {
                slugWarning.classList.remove('hidden');
                this.setCustomValidity('This slug is reserved and cannot be used');
            } else {
                slugWarning.classList.add('hidden');
                this.setCustomValidity('');
            }
        });

        form.addEventListener('submit', function(e) {
            const slug = slugInput.value.toLowerCase().trim();
            if (reservedSlugs.includes(slug)) {
                e.preventDefault();
                slugWarning.classList.remove('hidden');
                slugInput.focus();
                slugInput.setCustomValidity('This slug is reserved and cannot be used');
                slugInput.reportValidity();
            }
        });

        function formBuilder() {
            const defaultFields = [
                {
                    type: 'text',
                    name: 'name',
                    label: 'Full Name',
                    placeholder: 'John Doe',
                    required: true,
                    options: ''
                },
                {
                    type: 'email',
                    name: 'email',
                    label: 'Email Address',
                    placeholder: 'john@example.com',
                    required: true,
                    options: ''
                },
                {
                    type: 'tel',
                    name: 'phone',
                    label: 'Phone Number',
                    placeholder: '(555) 123-4567',
                    required: false,
                    options: ''
                },
                {
                    type: 'text',
                    name: 'property_address',
                    label: 'Property Address',
                    placeholder: '123 Main Street, City, State',
                    required: false,
                    options: ''
                },
                {
                    type: 'textarea',
                    name: 'message',
                    label: 'Message',
                    placeholder: 'Tell us about your project or inquiry...',
                    required: true,
                    options: ''
                }
            ];

            const oldFields = {!! old('form_fields') ? old('form_fields') : 'null' !!};

            return {
                hasForm: {{ old('has_form') ? 'true' : 'false' }},
                fields: oldFields || defaultFields,
                
                addField() {
                    this.fields.push({
                        type: 'text',
                        name: '',
                        label: '',
                        placeholder: '',
                        required: false,
                        options: ''
                    });
                },
                
                removeField(index) {
                    this.fields.splice(index, 1);
                }
            }
        }
    </script>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material-darker.min.css">
<style>
    .code-editor-wrapper {
        position: relative;
    }
    
    .code-editor {
        display: none;
    }
    
    .CodeMirror {
        height: auto;
        min-height: 100px;
        border: 1px solid rgb(75 85 99);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    
    .CodeMirror-focused {
        border-color: rgb(59 130 246);
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* VS Code-like colors */
    .cm-s-material-darker.CodeMirror {
        background-color: rgb(17 24 39);
        color: #e2e8f0;
    }
    
    .cm-s-material-darker .CodeMirror-gutters {
        background-color: rgb(17 24 39);
        border-right: 1px solid rgb(55 65 81);
    }
    
    .cm-s-material-darker .CodeMirror-linenumber {
        color: rgb(107 114 128);
    }
    
    /* Syntax highlighting colors matching VS Code */
    .cm-s-material-darker .cm-tag {
        color: #569cd6;
    }
    
    .cm-s-material-darker .cm-attribute {
        color: #9cdcfe;
    }
    
    .cm-s-material-darker .cm-string {
        color: #ce9178;
    }
    
    .cm-s-material-darker .cm-comment {
        color: #6a9955;
        font-style: italic;
    }
    
    .cm-s-material-darker .cm-keyword {
        color: #c586c0;
    }
    
    .cm-s-material-darker .cm-number {
        color: #b5cea8;
    }
    
    .cm-s-material-darker .cm-operator {
        color: #d4d4d4;
    }
    
    .cm-s-material-darker .cm-property {
        color: #9cdcfe;
    }
    
    .CodeMirror-cursor {
        border-left-color: #60a5fa;
    }
    
    .CodeMirror-selected {
        background: rgba(59, 130, 246, 0.2);
    }

    .editor-fullscreen {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        z-index: 90;
        background: rgba(15, 23, 42, 0.96);
        padding: 1rem 2rem 2rem;
    }

    .editor-fullscreen .CodeMirror {
        height: 85vh !important;
        border-radius: 0.5rem;
    }

    .textarea-fullscreen {
        position: fixed;
        inset: 0;
        z-index: 70;
        margin: 1.5rem;
        width: calc(100% - 3rem);
        height: 80vh;
        background: #0f172a;
        color: #e5e7eb;
        padding: 1rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editors = {};
        const editorWrappers = {};

        window.toggleEditorFullscreen = function(targetId, toggleButton = null) {
            const textarea = document.getElementById(targetId);
            if (!textarea) return;

            const cm = textarea._codeMirrorInstance;
            const wrapper = textarea._codeMirrorWrapper;
            if (!cm || !wrapper) return;

            const isFullscreen = wrapper.classList.toggle('editor-fullscreen');
            if (isFullscreen) {
                cm.setSize('100%', '80vh');
                cm.refresh();
                cm.focus();
                if (toggleButton) {
                    toggleButton.innerHTML = '<i class="fas fa-compress mr-2"></i>Close';
                }
            } else {
                const rows = parseInt(textarea.getAttribute('rows') || 10);
                cm.setSize(null, rows * 24);
                cm.refresh();
                if (toggleButton) {
                    toggleButton.innerHTML = '<i class="fas fa-expand mr-2"></i>Expand';
                }
            }
        };

        window.toggleTextareaFullscreen = function(targetId, toggleButton = null) {
            const textarea = document.getElementById(targetId);
            if (!textarea) return;

            const isFullscreen = textarea.classList.toggle('textarea-fullscreen');
            if (isFullscreen) {
                textarea.focus();
                if (toggleButton) {
                    toggleButton.innerHTML = '<i class="fas fa-compress mr-2"></i>Close';
                }
            } else {
                if (toggleButton) {
                    toggleButton.innerHTML = '<i class="fas fa-expand mr-2"></i>Expand';
                }
            }
        };

        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['head_content', 'body_content', 'footer_scripts'].forEach(id => {
                    const textarea = document.getElementById(id);
                    const wrapper = textarea?._codeMirrorWrapper;
                    const cm = textarea?._codeMirrorInstance;
                    if (wrapper && cm && wrapper.classList.contains('editor-fullscreen')) {
                        wrapper.classList.remove('editor-fullscreen');
                        const rows = parseInt(textarea.getAttribute('rows') || 10);
                        cm.setSize(null, rows * 24);
                        cm.refresh();
                        const toggleBtn = document.querySelector(`[data-editor-toggle="${id}"]`);
                        if (toggleBtn) {
                            toggleBtn.innerHTML = '<i class="fas fa-expand mr-2"></i>Expand';
                        }
                    }
                });

                const successMessage = document.getElementById('form_success_message');
                if (successMessage && successMessage.classList.contains('textarea-fullscreen')) {
                    successMessage.classList.remove('textarea-fullscreen');
                    const toggleBtn = document.querySelector('[data-textarea-toggle="form_success_message"]');
                    if (toggleBtn) {
                        toggleBtn.innerHTML = '<i class="fas fa-expand mr-1"></i>Expand';
                    }
                }
            }
        });
        
        // Initialize CodeMirror for head_content
        const headContent = document.getElementById('head_content');
        if (headContent) {
            const headWrapper = document.createElement('div');
            headWrapper.className = 'code-editor-wrapper';
            headContent.parentNode.insertBefore(headWrapper, headContent);
            headWrapper.appendChild(headContent);
            
            editors.head = CodeMirror.fromTextArea(headContent, {
                mode: 'htmlmixed',
                theme: 'material-darker',
                lineNumbers: true,
                lineWrapping: true,
                indentUnit: 2,
                tabSize: 2,
                indentWithTabs: false,
                autoCloseTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                extraKeys: {
                    'Ctrl-Space': 'autocomplete',
                    'Tab': function(cm) {
                        if (cm.somethingSelected()) {
                            cm.indentSelection('add');
                        } else {
                            cm.replaceSelection('  ', 'end');
                        }
                    }
                }
            });
            
            // Set height based on rows attribute
            const rows = parseInt(headContent.getAttribute('rows') || 4);
            editors.head.setSize(null, rows * 24);
            headContent._codeMirrorInstance = editors.head;
            headContent._codeMirrorWrapper = headWrapper;
            editorWrappers.head = headWrapper;
        }
        
        // Initialize CodeMirror for body_content
        const bodyContent = document.getElementById('body_content');
        if (bodyContent) {
            const bodyWrapper = document.createElement('div');
            bodyWrapper.className = 'code-editor-wrapper';
            bodyContent.parentNode.insertBefore(bodyWrapper, bodyContent);
            bodyWrapper.appendChild(bodyContent);
            
            editors.body = CodeMirror.fromTextArea(bodyContent, {
                mode: 'htmlmixed',
                theme: 'material-darker',
                lineNumbers: true,
                lineWrapping: true,
                indentUnit: 2,
                tabSize: 2,
                indentWithTabs: false,
                autoCloseTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                extraKeys: {
                    'Ctrl-Space': 'autocomplete',
                    'Tab': function(cm) {
                        if (cm.somethingSelected()) {
                            cm.indentSelection('add');
                        } else {
                            cm.replaceSelection('  ', 'end');
                        }
                    }
                }
            });
            
            // Set height based on rows attribute
            const rows = parseInt(bodyContent.getAttribute('rows') || 20);
            editors.body.setSize(null, rows * 24);
            bodyContent._codeMirrorInstance = editors.body;
            bodyContent._codeMirrorWrapper = bodyWrapper;
            editorWrappers.body = bodyWrapper;
        }
        
        // Initialize CodeMirror for footer_scripts
        const footerScripts = document.getElementById('footer_scripts');
        if (footerScripts) {
            const footerWrapper = document.createElement('div');
            footerWrapper.className = 'code-editor-wrapper';
            footerScripts.parentNode.insertBefore(footerWrapper, footerScripts);
            footerWrapper.appendChild(footerScripts);
            
            editors.footer = CodeMirror.fromTextArea(footerScripts, {
                mode: 'htmlmixed',
                theme: 'material-darker',
                lineNumbers: true,
                lineWrapping: true,
                indentUnit: 2,
                tabSize: 2,
                indentWithTabs: false,
                autoCloseTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                extraKeys: {
                    'Ctrl-Space': 'autocomplete',
                    'Tab': function(cm) {
                        if (cm.somethingSelected()) {
                            cm.indentSelection('add');
                        } else {
                            cm.replaceSelection('  ', 'end');
                        }
                    }
                }
            });
            
            // Set height based on rows attribute
            const footerRows = parseInt(footerScripts.getAttribute('rows') || 12);
            editors.footer.setSize(null, footerRows * 24);
            footerScripts._codeMirrorInstance = editors.footer;
            footerScripts._codeMirrorWrapper = footerWrapper;
            editorWrappers.footer = footerWrapper;
        }
        
        // Ensure CodeMirror values are synced on form submit
        const form = headContent?.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                Object.values(editors).forEach(editor => {
                    editor.save();
                });
            });
        }
    });
</script>
@endpush

{{-- Include AI Content Generation Modal --}}
@if(config('ai.enabled'))
    <x-ai-content-modal />
@endif
