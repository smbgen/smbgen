@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">CMS Pages</h1>
            <p class="admin-page-subtitle">Manage content pages for your website</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.cms.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Create New Page
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Missing Required Pages Warnings --}}
    @if($missingPages->isNotEmpty())
        @if($missingPages->contains('home'))
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded mb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Missing Home Page - Your root route (/) won't display properly
                        </p>
                        <p class="mt-1 text-xs text-yellow-700 dark:text-yellow-300">
                            <a href="{{ route('admin.cms.create') }}?slug=home" class="underline hover:text-yellow-900 dark:hover:text-yellow-100">Create home page now</a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Optional Pages Info (Collapsible) --}}
    @if(!$hasContactPage)
        <div x-data="{ showInfo: false }" class="mb-4">
            <button @click="showInfo = !showInfo" type="button" class="w-full text-left px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 rounded text-sm font-medium text-blue-800 dark:text-blue-200 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors flex items-center justify-between">
                <span>
                    <i class="fas fa-info-circle mr-2"></i>
                    1 optional page customization available
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showInfo }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="showInfo" x-collapse class="mt-2 space-y-2">
                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-3 rounded">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-xs font-medium text-blue-800 dark:text-blue-200">
                                Contact Page - Using built-in fallback
                            </p>
                            <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                                <a href="{{ route('admin.cms.create') }}?slug=contact" class="underline hover:text-blue-900 dark:hover:text-blue-100">Create custom contact page</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Navbar Settings Section --}}
    <div class="admin-card mb-6" x-data="{ ...navbarBuilder(), navbarOpen: {{ session('open_navbar') ? 'true' : 'false' }} }">
        <button type="button" class="admin-card-header cursor-pointer hover:bg-gray-700/70 transition-colors w-full text-left" @click="navbarOpen = !navbarOpen">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white">Navigation Bar Settings</h2>
                    <p class="text-sm text-gray-400 mt-1">Configure the global navigation bar that appears on CMS pages</p>
                </div>
                <div class="ml-4">
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': navbarOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </button>
        <div class="admin-card-body" x-show="navbarOpen" x-collapse>
            <form action="{{ route('admin.cms.navbar.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Logo Settings --}}
                    <div>
                        <label for="logo_text" class="form-label">Logo Text</label>
                        <input type="text" 
                               id="logo_text" 
                               name="logo_text" 
                               value="{{ old('logo_text', $navbarSettings->logo_text) }}"
                               class="form-input"
                               placeholder="Your Brand Name">
                    </div>

                    <div>
                        <label for="logo_image_url" class="form-label">Logo Image URL (optional)</label>
                        <input type="text" 
                               id="logo_image_url" 
                               name="logo_image_url" 
                               value="{{ old('logo_image_url', $navbarSettings->logo_image_url) }}"
                               class="form-input"
                               placeholder="https://example.com/logo.png">
                    </div>
                </div>

                {{-- Color Settings --}}
                <div class="border-t pt-4 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" 
                               id="use_business_colors" 
                               name="use_business_colors" 
                               value="1"
                               x-model="useBusinessColors"
                               {{ old('use_business_colors', $navbarSettings->use_business_colors) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <label for="use_business_colors" class="ml-2 text-sm font-medium">
                            Use Company Colors
                            <span class="text-xs text-gray-500 ml-1">(from <a href="#company-colors" class="underline hover:text-blue-600">Company Colors settings</a> below)</span>
                        </label>
                    </div>

                    <div class="flex items-center mb-4">
                        <input type="checkbox" 
                               id="is_sticky" 
                               name="is_sticky" 
                               value="1"
                               {{ old('is_sticky', $navbarSettings->is_sticky ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <label for="is_sticky" class="ml-2 text-sm font-medium">
                            Sticky Navbar
                            <span class="text-xs text-gray-500 ml-1">(Navbar stays at top of screen when scrolling)</span>
                        </label>
                    </div>

                    <div x-show="!useBusinessColors" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-cloak x-data="{ 
                        bgColor: '{{ old('custom_bg_color', $navbarSettings->custom_bg_color ?? '#1f2937') }}',
                        textColor: '{{ old('custom_text_color', $navbarSettings->custom_text_color ?? '#ffffff') }}'
                    }">
                        <div>
                            <label for="custom_bg_color" class="form-label">Navbar Background Color</label>
                            <div class="flex gap-3 items-center">
                                <input type="color" 
                                       id="custom_bg_color" 
                                       name="custom_bg_color" 
                                       x-model="bgColor"
                                       class="form-input h-12 w-20 cursor-pointer">
                                <input type="text" 
                                       x-model="bgColor"
                                       class="form-input flex-1"
                                       placeholder="#1f2937"
                                       pattern="^#[0-9A-Fa-f]{6}$">
                                <div class="w-12 h-12 rounded border-2 border-gray-600 shadow-sm" 
                                     :style="{ backgroundColor: bgColor }"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Choose the background color for your navbar</p>
                        </div>

                        <div>
                            <label for="custom_text_color" class="form-label">Navbar Text Color</label>
                            <div class="flex gap-3 items-center">
                                <input type="color" 
                                       id="custom_text_color" 
                                       name="custom_text_color" 
                                       x-model="textColor"
                                       class="form-input h-12 w-20 cursor-pointer">
                                <input type="text" 
                                       x-model="textColor"
                                       class="form-input flex-1"
                                       placeholder="#ffffff"
                                       pattern="^#[0-9A-Fa-f]{6}$">
                                <div class="w-12 h-12 rounded border-2 border-gray-600 shadow-sm flex items-center justify-center" 
                                     :style="{ backgroundColor: bgColor, color: textColor }">
                                    <span class="text-xs font-bold">Aa</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Choose the text/link color for your navbar</p>
                        </div>
                    </div>
                </div>

                {{-- Menu Items --}}
                <div class="border-t pt-4 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-md font-semibold">Menu Items</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Add links and reorder items (drag or use arrows)</p>
                        </div>
                        <button type="button" @click="addMenuItem()" class="btn-secondary text-sm">
                            <i class="fas fa-plus mr-1"></i> Add Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in menuItems" :key="index">
                            <div class="menu-item-card border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-md transition-all">
                                <div class="flex items-start gap-4">
                                    {{-- Reorder & Delete Controls --}}
                                    <div class="flex flex-col gap-2">
                                        <button type="button" 
                                                @click="moveMenuItem(index, -1)"
                                                :disabled="index === 0"
                                                :class="index === 0 ? 'opacity-30 cursor-not-allowed' : ''"
                                                class="px-3 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded text-sm font-medium transition-colors disabled:hover:bg-gray-200 dark:disabled:hover:bg-gray-700"
                                                title="Move Up">
                                            ↑ Up
                                        </button>
                                        <button type="button" 
                                                @click="moveMenuItem(index, 1)"
                                                :disabled="index === menuItems.length - 1"
                                                :class="index === menuItems.length - 1 ? 'opacity-30 cursor-not-allowed' : ''"
                                                class="px-3 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded text-sm font-medium transition-colors disabled:hover:bg-gray-200 dark:disabled:hover:bg-gray-700"
                                                title="Move Down">
                                            ↓ Down
                                        </button>
                                        <button type="button" 
                                                @click="removeMenuItem(index)" 
                                                class="px-3 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-800 dark:text-red-200 rounded text-sm font-medium transition-colors"
                                                title="Delete">
                                            🗑 Delete
                                        </button>
                                    </div>

                                    {{-- Form Fields --}}
                                    <div class="flex-1 space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">
                                                    Label <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" 
                                                       x-model="item.label"
                                                       placeholder="e.g., Home, About, Contact"
                                                       class="form-input text-sm w-full"
                                                       required>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">
                                                    URL <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" 
                                                       x-model="item.url"
                                                       placeholder="e.g., /, /about, #services"
                                                       class="form-input text-sm w-full"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">
                                                    Open In
                                                </label>
                                                <select x-model="item.target" class="form-input text-sm w-full">
                                                    <option value="_self">Same Tab</option>
                                                    <option value="_blank">New Tab</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">
                                                    Button Style
                                                </label>
                                                <select x-model="item.style" class="form-input text-sm w-full">
                                                    <option value="">Default Link</option>
                                                    <option value="btn-accent">🎨 Accent Button (Theme Accent Color)</option>
                                                    <option value="btn-primary">🔵 Primary Button (Theme Primary Color)</option>
                                                    <option value="btn-secondary">🟣 Secondary Button (Theme Secondary Color)</option>
                                                    <option value="btn-success">🟢 Success Button (Green)</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        {{-- Preview Badge --}}
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="text-gray-700 dark:text-gray-300 font-medium">Preview:</span>
                                            <span x-text="`Position ${index + 1}`" class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded font-medium"></span>
                                            <span x-show="item.label" x-text="item.label" class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded font-medium"></span>
                                            <span x-show="item.style" x-text="item.style.replace('btn-', '').toUpperCase()" class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded font-medium"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty State --}}
                        <div x-show="menuItems.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                            <i class="fa-solid fa-bars text-3xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400">No menu items yet. Click "Add Item" to get started.</p>
                        </div>
                    </div>

                    <input type="hidden" name="menu_items" x-model="menuItemsJson">
                </div>

                <div class="flex justify-end pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Navbar Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Theme Settings Section --}}
    <div id="company-colors" class="admin-card mb-6" x-data="{ 
        colorsOpen: false,
        selectedTheme: '{{ old('theme_preset', $companyColors->theme_preset ?? 'default') }}',
        themes: {{ json_encode(\App\Models\CmsCompanyColors::getThemePresets()) }},
        primaryColor: '{{ $companyColors->primary_color }}',
        secondaryColor: '{{ $companyColors->secondary_color }}',
        accentColor: '{{ $companyColors->accent_color }}',
        applyTheme(preset) {
            this.selectedTheme = preset;
            const theme = this.themes[preset];
            this.primaryColor = theme.primary;
            this.secondaryColor = theme.secondary;
            this.accentColor = theme.accent;
            document.getElementById('primary_color').value = theme.primary;
            document.getElementById('primary_color_picker').value = theme.primary;
            document.getElementById('secondary_color').value = theme.secondary;
            document.getElementById('secondary_color_picker').value = theme.secondary;
            document.getElementById('background_color').value = theme.background;
            document.getElementById('background_color_picker').value = theme.background;
            document.getElementById('body_background_color').value = theme.body_background;
            document.getElementById('body_background_color_picker').value = theme.body_background;
            document.getElementById('text_color').value = theme.text;
            document.getElementById('text_color_picker').value = theme.text;
            document.getElementById('accent_color').value = theme.accent;
            document.getElementById('accent_color_picker').value = theme.accent;
        }
    }">
        <button type="button" class="admin-card-header cursor-pointer hover:bg-gray-700/70 transition-colors w-full text-left" @click="colorsOpen = !colorsOpen">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">Theme Settings</h2>
                    <p class="text-sm text-gray-400 mt-1">Choose a theme preset, customize colors, and enable effects for navbar, body, and footer</p>
                </div>
                <div class="ml-4">
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': colorsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </button>
        <div class="admin-card-body" x-show="colorsOpen" x-collapse>
            <form action="{{ route('admin.cms.colors.update') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- Theme Preset Selector --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <label for="theme_preset" class="form-label">Theme Preset</label>
                    <select id="theme_preset" 
                            name="theme_preset" 
                            x-model="selectedTheme"
                            @change="applyTheme($event.target.value)"
                            class="form-select mb-2">
                        @foreach(\App\Models\CmsCompanyColors::getThemePresets() as $key => $theme)
                            <option value="{{ $key }}">{{ $theme['name'] }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-blue-900 dark:text-blue-100" x-text="themes[selectedTheme]?.description"></p>
                </div>

                {{-- Effects Checkboxes --}}
                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                    <label class="form-label mb-3">Visual Effects</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @php
                            $enabledEffects = is_array($companyColors->enabled_effects) 
                                ? $companyColors->enabled_effects 
                                : (is_string($companyColors->enabled_effects) ? json_decode($companyColors->enabled_effects, true) ?? [] : []);
                        @endphp
                        @foreach(\App\Models\CmsCompanyColors::getAvailableEffects() as $key => $effect)
                            <div class="flex items-start">
                                <input type="checkbox" 
                                       id="effect_{{ $key }}" 
                                       name="enabled_effects[]" 
                                       value="{{ $key }}"
                                       {{ in_array($key, $enabledEffects) ? 'checked' : '' }}
                                       class="mt-1 rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500">
                                <div class="ml-2">
                                    <label for="effect_{{ $key }}" class="text-sm font-medium text-gray-900 dark:text-white cursor-pointer">{{ $effect }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Color Pickers --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="primary_color" class="form-label">Primary Brand Color</label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="primary_color_picker" 
                                   x-model="primaryColor"
                                   @change="document.getElementById('primary_color').value = primaryColor"
                                   class="h-10 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                            <input type="text" 
                                   id="primary_color" 
                                   name="primary_color" 
                                   x-model="primaryColor"
                                   @input="document.getElementById('primary_color_picker').value = primaryColor"
                                   class="form-input flex-1"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   placeholder="#3B82F6"
                                   required>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Main buttons, links, headings (.btn-primary, .text-brand)</p>
                    </div>

                    <div>
                        <label for="secondary_color" class="form-label">Secondary Brand Color</label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="secondary_color_picker" 
                                   value="{{ $companyColors->secondary_color }}"
                                   class="h-10 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer"
                                   onchange="document.getElementById('secondary_color').value = this.value">
                            <input type="text" 
                                   id="secondary_color" 
                                   name="secondary_color" 
                                   value="{{ old('secondary_color', $companyColors->secondary_color) }}"
                                   class="form-input flex-1"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   placeholder="#10B981"
                                   onchange="document.getElementById('secondary_color_picker').value = this.value"
                                   required>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Secondary buttons, accents (.btn-secondary)</p>
                    </div>

                    <div>
                        <label for="background_color" class="form-label">Navbar Background Color</label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="background_color_picker" 
                                   value="{{ $companyColors->background_color }}"
                                   class="h-10 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer"
                                   onchange="document.getElementById('background_color').value = this.value">
                            <input type="text" 
                                   id="background_color" 
                                   name="background_color" 
                                   value="{{ old('background_color', $companyColors->background_color) }}"
                                   class="form-input flex-1"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   placeholder="#1f2937"
                                   onchange="document.getElementById('background_color_picker').value = this.value"
                                   required>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Navigation bar background (text auto-contrasts)</p>
                    </div>

                    <div>
                        <label for="body_background_color" class="form-label">Body Background Color</label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="body_background_color_picker" 
                                   value="{{ $companyColors->body_background_color ?? '#ffffff' }}"
                                   class="h-10 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer"
                                   onchange="document.getElementById('body_background_color').value = this.value">
                            <input type="text" 
                                   id="body_background_color" 
                                   name="body_background_color" 
                                   value="{{ old('body_background_color', $companyColors->body_background_color ?? '#ffffff') }}"
                                   class="form-input flex-1"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   placeholder="#ffffff"
                                   onchange="document.getElementById('body_background_color_picker').value = this.value"
                                   required>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Main page background color for public pages</p>
                    </div>

                    <div>
                        <label for="text_color" class="form-label">Body Text Color</label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="text_color_picker" 
                                   value="{{ $companyColors->text_color }}"
                                   class="h-10 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer"
                                   onchange="document.getElementById('text_color').value = this.value">
                            <input type="text" 
                                   id="text_color" 
                                   name="text_color" 
                                   value="{{ old('text_color', $companyColors->text_color) }}"
                                   class="form-input flex-1"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   placeholder="#1f2937"
                                   onchange="document.getElementById('text_color_picker').value = this.value"
                                   required>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Default text color for page content</p>
                    </div>

                    <div>
                        <label for="accent_color" class="form-label">Accent Color</label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="accent_color_picker" 
                                   x-model="accentColor"
                                   @change="document.getElementById('accent_color').value = accentColor"
                                   class="h-10 w-16 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                            <input type="text" 
                                   id="accent_color" 
                                   name="accent_color" 
                                   x-model="accentColor"
                                   @input="document.getElementById('accent_color_picker').value = accentColor"
                                   class="form-input flex-1"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   placeholder="#F59E0B"
                                   required>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Highlights, badges, special elements (.text-accent, .bg-accent)</p>
                    </div>
                </div>

                <div class="border-t pt-4 dark:border-gray-700">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="auto_inject_css" 
                               name="auto_inject_css" 
                               value="1"
                               {{ old('auto_inject_css', $companyColors->auto_inject_css) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <label for="auto_inject_css" class="ml-2 text-sm font-medium">
                            Auto-inject CSS into all CMS pages
                            <span class="text-xs text-gray-500 ml-1">(recommended)</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        When enabled, color variables and utility classes (.btn-primary, .btn-secondary, .text-brand, .bg-brand) are automatically available on all CMS pages.
                    </p>
                </div>

                <div class="border-t pt-4 dark:border-gray-700">
                    <label class="form-label">CSS Preview (Read-Only)</label>
                    <div class="relative">
                        <textarea id="css_preview_field" readonly class="hidden">{{ $companyColors->generateCSS() }}</textarea>
                        <div id="css_preview_placeholder" class="absolute inset-0 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm z-10 cursor-pointer rounded-lg border border-gray-600" style="min-height: 300px;">
                            <div class="text-center">
                                <i class="fas fa-eye text-blue-400 text-3xl mb-2"></i>
                                <p class="text-blue-400 font-medium">Click to view generated CSS</p>
                                <p class="text-gray-400 text-sm mt-1">(Read-only preview)</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mt-2 space-y-2 bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="flex items-start gap-2">
                            <i class="fas fa-info-circle text-blue-400 mt-0.5 flex-shrink-0"></i>
                            <span>This CSS is <strong class="text-white">automatically generated</strong> from your theme colors and will be injected into all CMS pages.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-edit text-green-400 mt-0.5 flex-shrink-0"></i>
                            <span>To <strong class="text-white">override these styles</strong>, use the <strong class="text-white">Custom CSS</strong> editor in the CSS Editor tab above. Custom CSS loads after theme CSS and takes precedence.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-code text-purple-400 mt-0.5 flex-shrink-0"></i>
                            <span>Example: <code class="bg-gray-900 px-2 py-1 rounded text-blue-300">.hero { background: linear-gradient(...); }</code></span>
                        </p>
                    </div>
                </div>

                {{-- Navbar Preview --}}
                <div class="border-t pt-4 dark:border-gray-700">
                    <label class="form-label">Navbar Preview</label>
                    @php
                        // Determine actual navbar colors based on settings (matches public-navbar.blade.php)
                        $previewBgColor = $navbarSettings->getBackgroundColor();
                        $previewTextColor = $navbarSettings->getTextColor();
                    @endphp
                    <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                        <nav class="shadow-lg relative z-10" style="background-color: {{ $previewBgColor }}">
                            <div class="px-4 sm:px-6 lg:px-8">
                                <div class="flex items-center justify-between h-16">
                                    <div class="flex items-center">
                                        <span class="font-bold text-xl" style="color: {{ $previewTextColor }}">Your Brand</span>
                                    </div>
                                    <div class="hidden md:block">
                                        <div class="ml-10 flex items-baseline space-x-4">
                                            <a href="#" class="hover:opacity-80 px-3 py-2 rounded-md text-sm font-medium transition-colors" style="color: {{ $previewTextColor }}">Home</a>
                                            <a href="#" class="hover:opacity-80 px-3 py-2 rounded-md text-sm font-medium transition-colors" style="color: {{ $previewTextColor }}">About</a>
                                            <a href="#" class="hover:opacity-80 px-3 py-2 rounded-md text-sm font-medium transition-colors" style="color: {{ $previewTextColor }}">Services</a>
                                            <a href="#" 
                                               :style="`background-color: ${accentColor}`"
                                               class="text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm hover:shadow-md transition-all">
                                                Contact
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        This preview shows your current navbar appearance based on Navbar Settings above. The Contact button uses the theme's accent color.
                    </p>
                </div>

                {{-- Advanced Header Concerns Tabs --}}
                <div class="mt-8 border-t border-gray-700 pt-6" 
                     x-data="{ activeTab: 'css-editor' }">
                    
                    {{-- Tab Navigation --}}
                    <div class="flex space-x-4 border-b border-gray-700 mb-6 overflow-x-auto">
                        <button type="button" 
                                @click="activeTab = 'css-editor'"
                                :class="activeTab === 'css-editor' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400'"
                                class="pb-3 px-4 border-b-2 font-medium transition-colors whitespace-nowrap">
                            <i class="fas fa-code mr-2"></i>CSS Editor
                        </button>
                        <button type="button" 
                                @click="activeTab = 'css-classes'"
                                :class="activeTab === 'css-classes' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400'"
                                class="pb-3 px-4 border-b-2 font-medium transition-colors whitespace-nowrap">
                            <i class="fas fa-list mr-2"></i>CSS Class Whitelist
                        </button>
                        <button type="button" 
                                @click="activeTab = 'seo'"
                                :class="activeTab === 'seo' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400'"
                                class="pb-3 px-4 border-b-2 font-medium transition-colors whitespace-nowrap">
                            <i class="fas fa-search mr-2"></i>SEO & Meta Tags
                        </button>
                    </div>

                    {{-- CSS Editor Tab --}}
                    <div x-show="activeTab === 'css-editor'" style="display: none;" x-transition>
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Custom CSS</label>
                                <p class="text-sm text-gray-400 mb-3">Write custom CSS that will be injected into all CMS pages. This CSS will load after theme CSS.</p>
                                <textarea 
                                    name="custom_css" 
                                    id="custom_css_field"
                                    class="form-input font-mono text-sm">{{ old('custom_css', $companyColors->custom_css) }}</textarea>
                            </div>
                            
                            <div class="bg-yellow-900/20 border border-yellow-600 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-400 mb-2">
                                    <i class="fas fa-lightbulb mr-2"></i>Tips:
                                </h4>
                                <ul class="text-sm text-yellow-200 space-y-1">
                                    <li>• Use CSS variables: <code class="bg-gray-800 px-2 py-1 rounded">var(--brand-primary)</code></li>
                                    <li>• Define semantic classes for AI: <code class="bg-gray-800 px-2 py-1 rounded">.hero</code>, <code class="bg-gray-800 px-2 py-1 rounded">.section</code>, <code class="bg-gray-800 px-2 py-1 rounded">.cta-button</code></li>
                                    <li>• Changes apply immediately on page refresh</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- CSS Class Whitelist Tab --}}
                    <div x-show="activeTab === 'css-classes'" style="display: none;" x-transition>
                        <div class="space-y-4" x-data="{ 
                            classes: @js($companyColors->allowed_css_classes && count($companyColors->allowed_css_classes) > 0 ? $companyColors->allowed_css_classes : \App\Models\CmsCompanyColors::getDefaultCssClassWhitelist()),
                            newClass: '',
                            addClass() {
                                if (this.newClass.trim() && !this.classes.includes(this.newClass.trim())) {
                                    this.classes.push(this.newClass.trim());
                                    this.newClass = '';
                                    this.updateHiddenField();
                                }
                            },
                            removeClass(index) {
                                this.classes.splice(index, 1);
                                this.updateHiddenField();
                            },
                            updateHiddenField() {
                                document.getElementById('allowed_css_classes_field').value = JSON.stringify(this.classes);
                            }
                        }">
                            <div class="bg-blue-900/20 border border-blue-600 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-300 mb-2">
                                    <i class="fas fa-info-circle mr-2"></i>About CSS Classes
                                </h4>
                                <div class="text-sm text-blue-200 space-y-2">
                                    <p>
                                        Define CSS classes that AI-generated content can use. This application uses <strong>Tailwind CSS v3</strong> for utility classes.
                                    </p>
                                    <p>
                                        You can add custom semantic classes (like <code class="bg-gray-800 px-2 py-1 rounded">hero</code>, <code class="bg-gray-800 px-2 py-1 rounded">section</code>) 
                                        or Tailwind utility classes (like <code class="bg-gray-800 px-2 py-1 rounded">grid</code>, <code class="bg-gray-800 px-2 py-1 rounded">p-4</code>).
                                    </p>
                                    <p>
                                        <a href="https://tailwindcss.com/docs" target="_blank" class="text-blue-400 hover:text-blue-300 underline">
                                            <i class="fas fa-external-link-alt mr-1"></i>View Tailwind CSS Documentation
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Add CSS Class</label>
                                <div class="flex gap-2">
                                    <input 
                                        type="text" 
                                        x-model="newClass"
                                        @keydown.enter.prevent="addClass()"
                                        placeholder="e.g., hero, section, cta-button"
                                        class="form-input flex-1">
                                    <button 
                                        type="button" 
                                        @click="addClass()"
                                        class="btn-primary">
                                        <i class="fas fa-plus mr-2"></i>Add
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Allowed Classes</label>
                                <div class="space-y-2">
                                    <template x-for="(cssClass, index) in classes" :key="index">
                                        <div class="flex items-center justify-between bg-gray-800 rounded-lg p-3 border border-gray-700">
                                            <code class="text-blue-400" x-text="cssClass"></code>
                                            <button 
                                                type="button" 
                                                @click="removeClass(index)"
                                                class="text-red-400 hover:text-red-300">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <div x-show="classes.length === 0" class="text-gray-400 text-sm text-center py-8">
                                        No CSS classes added yet. Add classes that AI can use in generated content.
                                    </div>
                                </div>
                            </div>

                            <input 
                                type="hidden" 
                                name="allowed_css_classes" 
                                id="allowed_css_classes_field"
                                :value="JSON.stringify(classes)">
                        </div>
                    </div>

                    {{-- SEO Tab --}}
                    <div x-show="activeTab === 'seo'" style="display: none;" x-transition>
                        <div class="space-y-6">
                            <div class="bg-blue-900/20 border border-blue-600 rounded-lg p-4">
                                <p class="text-sm text-blue-200">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    These are default SEO settings. Individual pages can override these values.
                                </p>
                            </div>

                            {{-- Title Template --}}
                            <div>
                                <label for="seo_title_template" class="form-label">SEO Title Template</label>
                                <input type="text" 
                                       id="seo_title_template" 
                                       name="seo_title_template" 
                                       value="{{ old('seo_title_template', $companyColors->seo_title_template ?? '{page_title}') }}"
                                       placeholder="{page_title} | Your Company Name"
                                       class="form-input">
                                <p class="text-xs text-gray-400 mt-1">Use <code class="bg-gray-800 px-1 rounded">{page_title}</code> placeholder</p>
                            </div>

                            {{-- Meta Description --}}
                            <div>
                                <label for="seo_meta_description" class="form-label">Default Meta Description</label>
                                <textarea 
                                    id="seo_meta_description" 
                                    name="seo_meta_description" 
                                    rows="3"
                                    maxlength="160"
                                    class="form-input">{{ old('seo_meta_description', $companyColors->seo_meta_description) }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Recommended: 150-160 characters</p>
                            </div>

                            {{-- Meta Keywords --}}
                            <div>
                                <label for="seo_meta_keywords" class="form-label">Default Meta Keywords</label>
                                <input type="text" 
                                       id="seo_meta_keywords" 
                                       name="seo_meta_keywords" 
                                       value="{{ old('seo_meta_keywords', $companyColors->seo_meta_keywords) }}"
                                       placeholder="keyword1, keyword2, keyword3"
                                       class="form-input">
                                <p class="text-xs text-gray-400 mt-1">Comma-separated list</p>
                            </div>

                            <hr class="border-gray-700">

                            {{-- Open Graph Settings --}}
                            <h3 class="text-lg font-semibold text-white">Open Graph (Facebook, LinkedIn)</h3>
                            
                            <div>
                                <label for="og_site_name" class="form-label">OG Site Name</label>
                                <input type="text" 
                                       id="og_site_name" 
                                       name="og_site_name" 
                                       value="{{ old('og_site_name', $companyColors->og_site_name ?? config('app.name')) }}"
                                       class="form-input">
                            </div>

                            <div>
                                <label for="og_type" class="form-label">OG Type</label>
                                <select id="og_type" name="og_type" class="form-select">
                                    <option value="website" {{ ($companyColors->og_type ?? 'website') === 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="article" {{ ($companyColors->og_type ?? '') === 'article' ? 'selected' : '' }}>Article</option>
                                    <option value="business" {{ ($companyColors->og_type ?? '') === 'business' ? 'selected' : '' }}>Business</option>
                                </select>
                            </div>

                            <div>
                                <label for="og_image_url" class="form-label">Default OG Image URL</label>
                                <input type="url" 
                                       id="og_image_url" 
                                       name="og_image_url" 
                                       value="{{ old('og_image_url', $companyColors->og_image_url) }}"
                                       placeholder="https://example.com/og-image.jpg"
                                       class="form-input">
                                <p class="text-xs text-gray-400 mt-1">Recommended: 1200x630px</p>
                            </div>

                            <hr class="border-gray-700">

                            {{-- Twitter Card Settings --}}
                            <h3 class="text-lg font-semibold text-white">Twitter Card</h3>
                            
                            <div>
                                <label for="twitter_card_type" class="form-label">Twitter Card Type</label>
                                <select id="twitter_card_type" name="twitter_card_type" class="form-select">
                                    <option value="summary" {{ ($companyColors->twitter_card_type ?? 'summary_large_image') === 'summary' ? 'selected' : '' }}>Summary</option>
                                    <option value="summary_large_image" {{ ($companyColors->twitter_card_type ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                                </select>
                            </div>

                            <div>
                                <label for="twitter_site_handle" class="form-label">Twitter Site Handle</label>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400">@</span>
                                    <input type="text" 
                                           id="twitter_site_handle" 
                                           name="twitter_site_handle" 
                                           value="{{ old('twitter_site_handle', $companyColors->twitter_site_handle) }}"
                                           placeholder="yourcompany"
                                           class="form-input">
                                </div>
                            </div>

                            <hr class="border-gray-700">

                            {{-- Custom Head Scripts --}}
                            <div>
                                <label for="custom_head_scripts" class="form-label">Custom Head Scripts</label>
                                <p class="text-sm text-gray-400 mb-2">Additional scripts or styles to inject in <code class="bg-gray-800 px-1 rounded">&lt;head&gt;</code></p>
                                <textarea 
                                    id="custom_head_scripts" 
                                    name="custom_head_scripts" 
                                    rows="6"
                                    class="form-input font-mono text-sm">{{ old('custom_head_scripts', $companyColors->custom_head_scripts) }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Example: Google Analytics, custom fonts, etc.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                            @click="colorsOpen = false"
                            class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Theme Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Footer Settings Section --}}
    <div class="admin-card mb-6" x-data="{ footerOpen: {{ session('open_footer') ? 'true' : 'false' }} }" x-init="$watch('footerOpen', value => { if (value && window.footerEditor) { setTimeout(() => window.footerEditor.refresh(), 100); } })">
        <button type="button" class="admin-card-header cursor-pointer hover:bg-gray-700/70 transition-colors w-full text-left" @click="footerOpen = !footerOpen">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white">Footer Settings</h2>
                    <p class="text-sm text-gray-400 mt-1">Configure the global footer that appears on all public pages</p>
                </div>
                <div class="ml-4">
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': footerOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </button>
        <div class="admin-card-body" x-show="footerOpen" x-collapse>
            <form action="{{ route('admin.cms.footer.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" 
                           id="use_default_footer" 
                           name="use_default" 
                           value="1"
                           {{ old('use_default', $footerSettings->use_default ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="use_default_footer" class="ml-2 text-sm text-gray-300">
                        Use default footer template (uncheck to customize)
                    </label>
                </div>

                <div>
                    <label for="footer_html" class="form-label">Footer HTML</label>
                    <p class="text-sm text-gray-400 mb-2">Customize your footer HTML. The default template includes Quick Links and social media icons.</p>
                    <textarea 
                        id="footer_html" 
                        name="footer_html" 
                        rows="15"
                        class="form-input font-mono text-sm"
                        placeholder="Enter custom footer HTML...">{{ old('footer_html', $footerSettings->footer_html ?? '') }}</textarea>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-white mb-2">Available Variables</h3>
                    <ul class="text-sm text-gray-400 space-y-1">
                        <li><code class="text-blue-400">{{ '{{' }} config('business.company_name') }}</code> - Company name</li>
                        <li><code class="text-blue-400">{{ '{{' }} config('business.tagline') }}</code> - Company tagline</li>
                        <li><code class="text-blue-400">{{ '{{' }} config('business.social.twitter') }}</code> - Twitter URL</li>
                        <li><code class="text-blue-400">{{ '{{' }} config('business.social.facebook') }}</code> - Facebook URL</li>
                        <li><code class="text-blue-400">{{ '{{' }} config('business.social.linkedin') }}</code> - LinkedIn URL</li>
                        <li><code class="text-blue-400">{{ '{{' }} date('Y') }}</code> - Current year</li>
                    </ul>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                            @click="footerOpen = false"
                            class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Footer Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- CMS Pages Section --}}

    <div class="admin-card">
        <div class="admin-card-body">
                    @if($pages->isEmpty())
                        <div class="text-center py-8">
                            <div class="text-6xl mb-4">📄</div>
                            <h3 class="text-xl font-semibold mb-2">No pages yet</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Create your first CMS page to get started</p>
                            <a href="{{ route('admin.cms.create') }}" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Create Page
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>
                                            Slug
                                            <span class="ml-2 text-xs text-blue-400 font-semibold">Page with slug <code>home</code> becomes the Homepage of your website</span>
                                        </th>
                                        <th>
                                            Title
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th>
                                            CTA
                                        </th>
                                        <th>
                                            Updated
                                        </th>
                                        <th class="text-right">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pages as $page)
                                        @php
                                            $isRoot = in_array($page->slug, ['home', 'landing']);
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    @if($page->is_published)
                                                        <span class="flex h-2 w-2">
                                                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                        </span>
                                                    @else
                                                        <span class="inline-flex h-2 w-2 rounded-full bg-gray-400"></span>
                                                    @endif
                                                    <a href="{{ route('cms.show', $page->slug) }}" 
                                                       target="_blank"
                                                       class="text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 hover:underline inline-flex items-center gap-1">
                                                        /{{ $page->slug }}
                                                        <i class="fas fa-external-link-alt text-xs"></i>
                                                        @if($isRoot)
                                                            <span class="ml-2 px-2 py-1 rounded bg-gradient-to-r from-green-400 to-blue-400 text-white text-xs font-bold shadow inline-block">
                                                                Root Route
                                                            </span>
                                                        @endif
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm">
                                                    {{ $page->title }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($page->is_published)
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Live
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        <i class="fas fa-file-alt mr-1"></i>
                                                        Draft
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if($page->has_form)
                                                    <span class="inline-flex items-center gap-1">
                                                        <i class="fas fa-wpforms text-blue-500"></i>
                                                        Form ({{ $page->leads()->count() }})
                                                    </span>
                                                @elseif($page->cta_text)
                                                    {{ $page->cta_text }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $page->updated_at->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($page->is_published)
                                                    <a href="{{ route('cms.show', $page->slug) }}" 
                                                       target="_blank"
                                                       class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3"
                                                       title="View live page">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.cms.edit', $page) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.cms.duplicate', $page) }}" method="POST" class="inline" title="Create a draft copy of this page">
                                                    @csrf
                                                    <button type="submit" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 mr-3">
                                                        <i class="fas fa-copy"></i> Duplicate
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.cms.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

    {{-- CMS Documentation Section --}}
    <div class="admin-card mt-6" x-data="{ docsOpen: false }">
        <button type="button" class="admin-card-header cursor-pointer hover:bg-gray-700/70 transition-colors w-full text-left" @click="docsOpen = !docsOpen">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i class="fas fa-book text-blue-400"></i>
                        CMS Documentation
                    </h2>
                    <p class="text-sm text-gray-400 mt-1">Learn how to use the CMS system and available features</p>
                </div>
                <div class="ml-4">
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': docsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </button>
        <div class="admin-card-body" x-show="docsOpen" x-collapse>
            <div class="prose prose-invert max-w-none space-y-6">
                
                {{-- Overview --}}
                <div class="bg-gradient-to-r from-blue-900/30 to-purple-900/30 border border-blue-600 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-white mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-400"></i>
                        Overview
                    </h3>
                    <p class="text-gray-200 mb-4">
                        This CMS is designed to be <strong class="text-blue-300">opinionated yet flexible</strong>. It provides sensible defaults and structures 
                        while giving you complete control over HTML, CSS, and JavaScript when you need it.
                    </p>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span><strong>Opinionated:</strong> Uses Tailwind CSS, provides theme system, sanitizes content for security</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span><strong>Flexible:</strong> Full HTML/CSS/JS control, custom head content, footer scripts, raw code support</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span><strong>Secure:</strong> Built-in HTML sanitizer protects against XSS while preserving legitimate code</span>
                        </li>
                    </ul>
                </div>

                {{-- Page Structure --}}
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-layer-group text-purple-400"></i>
                        Page Structure
                    </h3>
                    <p class="text-gray-300 mb-4">Each CMS page has three main content areas:</p>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-900 rounded-lg p-4 border-l-4 border-blue-500">
                            <h4 class="font-semibold text-blue-300 mb-2">
                                <i class="fas fa-code mr-2"></i>Head Content
                            </h4>
                            <p class="text-sm text-gray-400 mb-2">
                                Loads in the <code class="bg-gray-800 px-2 py-1 rounded text-blue-300">&lt;head&gt;</code> section. Perfect for:
                            </p>
                            <ul class="text-sm text-gray-400 space-y-1 ml-6">
                                <li>• Custom <code class="bg-gray-800 px-1 rounded">&lt;style&gt;</code> tags with CSS</li>
                                <li>• External stylesheets with <code class="bg-gray-800 px-1 rounded">&lt;link&gt;</code></li>
                                <li>• Meta tags for SEO</li>
                                <li>• Scripts that don't need DOM access (analytics, config)</li>
                            </ul>
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4 border-l-4 border-green-500">
                            <h4 class="font-semibold text-green-300 mb-2">
                                <i class="fas fa-file-code mr-2"></i>Body Content
                            </h4>
                            <p class="text-sm text-gray-400 mb-2">
                                Your main page HTML. Use any valid HTML including:
                            </p>
                            <ul class="text-sm text-gray-400 space-y-1 ml-6">
                                <li>• Semantic HTML5 elements</li>
                                <li>• Tailwind CSS utility classes (see below)</li>
                                <li>• Canvas elements for animations</li>
                                <li>• Forms, tables, SVGs, and more</li>
                            </ul>
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4 border-l-4 border-orange-500">
                            <h4 class="font-semibold text-orange-300 mb-2">
                                <i class="fas fa-terminal mr-2"></i>Footer Scripts
                            </h4>
                            <p class="text-sm text-gray-400 mb-2">
                                Loads before <code class="bg-gray-800 px-2 py-1 rounded text-orange-300">&lt;/body&gt;</code>. Best for:
                            </p>
                            <ul class="text-sm text-gray-400 space-y-1 ml-6">
                                <li>• Scripts that need DOM access (animations, interactions)</li>
                                <li>• Canvas/WebGL scripts</li>
                                <li>• Page-specific JavaScript</li>
                                <li>• No <code class="bg-gray-800 px-1 rounded">DOMContentLoaded</code> wrapper needed - DOM is ready!</li>
                            </ul>
                            <div class="mt-3 bg-yellow-900/30 border border-yellow-600 rounded p-3">
                                <p class="text-xs text-yellow-200">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    <strong>Tip:</strong> Footer scripts run after the body is loaded, so you can directly access elements without waiting for DOMContentLoaded.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- HTML Sanitizer --}}
                <div class="bg-red-900/20 border border-red-600 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-red-400"></i>
                        HTML Sanitizer
                    </h3>
                    <p class="text-gray-300 mb-4">
                        The CMS includes a <strong class="text-red-300">built-in HTML sanitizer</strong> that protects against XSS attacks 
                        while preserving your legitimate HTML, CSS, and JavaScript.
                    </p>
                    
                    <div class="bg-gray-900 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-white mb-2">How It Works</h4>
                        <ul class="text-sm text-gray-400 space-y-2">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-green-400 mt-1"></i>
                                <span>Allows safe HTML tags: <code class="bg-gray-800 px-1 rounded">div, span, p, h1-h6, img, a, table, form, canvas, svg</code> and more</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-green-400 mt-1"></i>
                                <span>Preserves <code class="bg-gray-800 px-1 rounded">&lt;style&gt;</code> and <code class="bg-gray-800 px-1 rounded">&lt;script&gt;</code> tags completely intact</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-green-400 mt-1"></i>
                                <span>Protects JavaScript code from being mangled (no more broken arrow functions!)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-times text-red-400 mt-1"></i>
                                <span>Blocks dangerous tags and attributes that could pose security risks</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-yellow-900/30 border border-yellow-600 rounded p-4">
                        <p class="text-sm text-yellow-200">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Important:</strong> The sanitizer is designed for trusted users (admins). It allows scripts and styles, 
                            so only grant CMS access to trusted team members.
                        </p>
                    </div>
                </div>

                {{-- Tailwind CSS --}}
                <div class="bg-gradient-to-r from-cyan-900/30 to-blue-900/30 border border-cyan-600 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fab fa-css3-alt text-cyan-400"></i>
                        Tailwind CSS v3
                    </h3>
                    <p class="text-gray-300 mb-4">
                        This CMS uses <strong class="text-cyan-300">Tailwind CSS v3</strong> for styling. All Tailwind utility classes are available in your pages.
                    </p>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-900 rounded-lg p-4">
                            <h4 class="font-semibold text-white mb-3">Common Layout Classes</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">container mx-auto</code>
                                    <span class="text-gray-400 text-xs">Centered container</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">flex justify-center items-center</code>
                                    <span class="text-gray-400 text-xs">Center content</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">grid grid-cols-3 gap-4</code>
                                    <span class="text-gray-400 text-xs">3-column grid</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">p-4 md:p-8</code>
                                    <span class="text-gray-400 text-xs">Responsive padding</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4">
                            <h4 class="font-semibold text-white mb-3">Theme Colors</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">text-brand</code>
                                    <span class="text-gray-400 text-xs">Primary brand color</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">bg-brand</code>
                                    <span class="text-gray-400 text-xs">Primary background</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">text-accent</code>
                                    <span class="text-gray-400 text-xs">Accent color text</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">bg-accent</code>
                                    <span class="text-gray-400 text-xs">Accent background</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4">
                            <h4 class="font-semibold text-white mb-3">Button Styles</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">btn-primary</code>
                                    <span class="text-gray-400 text-xs">Primary button</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">btn-secondary</code>
                                    <span class="text-gray-400 text-xs">Secondary button</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">btn-accent</code>
                                    <span class="text-gray-400 text-xs">Accent button</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">btn-success</code>
                                    <span class="text-gray-400 text-xs">Green success button</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4">
                            <h4 class="font-semibold text-white mb-3">Typography</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">text-xs sm md lg xl 2xl</code>
                                    <span class="text-gray-400 text-xs">Font sizes</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">font-bold font-semibold</code>
                                    <span class="text-gray-400 text-xs">Font weights</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">text-center text-left</code>
                                    <span class="text-gray-400 text-xs">Alignment</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="bg-gray-800 px-2 py-1 rounded text-blue-300 flex-1">leading-tight leading-loose</code>
                                    <span class="text-gray-400 text-xs">Line height</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-blue-900/30 border border-blue-600 rounded p-4">
                        <i class="fas fa-external-link-alt text-blue-400 text-xl"></i>
                        <div class="flex-1">
                            <p class="text-white font-semibold mb-1">Full Tailwind Documentation</p>
                            <a href="https://tailwindcss.com/docs" target="_blank" class="text-blue-300 hover:text-blue-200 text-sm underline">
                                Visit tailwindcss.com/docs for complete reference
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Custom CSS Variables --}}
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-palette text-purple-400"></i>
                        CSS Variables
                    </h3>
                    <p class="text-gray-300 mb-4">
                        The theme system injects CSS variables that you can use in your custom styles:
                    </p>
                    
                    <div class="bg-gray-900 rounded-lg p-4 font-mono text-sm">
                        <div class="space-y-1 text-gray-300">
                            <div class="flex items-center gap-3">
                                <code class="text-blue-300">--brand-primary</code>
                                <span class="text-gray-500">→</span>
                                <span class="text-gray-400">Primary brand color</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <code class="text-blue-300">--brand-secondary</code>
                                <span class="text-gray-500">→</span>
                                <span class="text-gray-400">Secondary brand color</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <code class="text-blue-300">--brand-accent</code>
                                <span class="text-gray-500">→</span>
                                <span class="text-gray-400">Accent color</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <code class="text-blue-300">--brand-bg</code>
                                <span class="text-gray-500">→</span>
                                <span class="text-gray-400">Background color</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <code class="text-blue-300">--brand-text</code>
                                <span class="text-gray-500">→</span>
                                <span class="text-gray-400">Text color</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 bg-green-900/20 border border-green-600 rounded p-4">
                        <p class="text-sm text-green-200 mb-2">
                            <i class="fas fa-code mr-2"></i>
                            <strong>Example Usage:</strong>
                        </p>
                        <pre class="bg-gray-900 rounded p-3 text-xs text-gray-300 overflow-x-auto"><code>.my-custom-button {
    background: var(--brand-primary);
    color: white;
    border: 2px solid var(--brand-accent);
}</code></pre>
                    </div>
                </div>

                {{-- Best Practices --}}
                <div class="bg-gradient-to-r from-green-900/30 to-emerald-900/30 border border-green-600 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-thumbs-up text-green-400"></i>
                        Best Practices
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-semibold text-white mb-1">Use Semantic HTML</h4>
                                <p class="text-sm text-gray-300">Use <code class="bg-gray-800 px-1 rounded">&lt;header&gt;</code>, <code class="bg-gray-800 px-1 rounded">&lt;main&gt;</code>, <code class="bg-gray-800 px-1 rounded">&lt;section&gt;</code>, <code class="bg-gray-800 px-1 rounded">&lt;article&gt;</code> for better accessibility and SEO.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-semibold text-white mb-1">Separate Concerns</h4>
                                <p class="text-sm text-gray-300">Put styles in Head Content, HTML in Body Content, and DOM-dependent scripts in Footer Scripts.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-semibold text-white mb-1">Use Theme Colors</h4>
                                <p class="text-sm text-gray-300">Prefer <code class="bg-gray-800 px-1 rounded">btn-primary</code> and CSS variables over hardcoded colors for consistent branding.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-semibold text-white mb-1">Test Responsiveness</h4>
                                <p class="text-sm text-gray-300">Use Tailwind's responsive prefixes (<code class="bg-gray-800 px-1 rounded">md:</code>, <code class="bg-gray-800 px-1 rounded">lg:</code>) to ensure pages look good on all devices.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-semibold text-white mb-1">Leverage Footer Scripts</h4>
                                <p class="text-sm text-gray-300">Move DOM-dependent JavaScript to Footer Scripts to avoid timing issues and eliminate the need for <code class="bg-gray-800 px-1 rounded">DOMContentLoaded</code> wrappers.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Reference --}}
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-rocket text-yellow-400"></i>
                        Quick Reference
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-900 rounded p-4">
                            <h4 class="font-semibold text-blue-300 mb-2">
                                <i class="fas fa-question-circle mr-2"></i>Need custom styling?
                            </h4>
                            <p class="text-sm text-gray-400">Add CSS in <strong>Head Content</strong> or use <strong>Theme Settings → CSS Editor</strong> for global styles.</p>
                        </div>

                        <div class="bg-gray-900 rounded p-4">
                            <h4 class="font-semibold text-blue-300 mb-2">
                                <i class="fas fa-question-circle mr-2"></i>JavaScript not working?
                            </h4>
                            <p class="text-sm text-gray-400">Move scripts to <strong>Footer Scripts</strong> if they need DOM access. The DOM is ready there!</p>
                        </div>

                        <div class="bg-gray-900 rounded p-4">
                            <h4 class="font-semibold text-blue-300 mb-2">
                                <i class="fas fa-question-circle mr-2"></i>Want animation effects?
                            </h4>
                            <p class="text-sm text-gray-400">Canvas, WebGL, and CSS animations work great in <strong>Footer Scripts</strong>.</p>
                        </div>

                        <div class="bg-gray-900 rounded p-4">
                            <h4 class="font-semibold text-blue-300 mb-2">
                                <i class="fas fa-question-circle mr-2"></i>Need consistent colors?
                            </h4>
                            <p class="text-sm text-gray-400">Use <strong>Theme Settings</strong> to set brand colors and apply them with <code class="bg-gray-800 px-1 rounded">.btn-primary</code>, <code class="bg-gray-800 px-1 rounded">.text-brand</code>, etc.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
        </div>
@endsection

@push('scripts')
<!-- CodeMirror CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material-darker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>

<style>
    .CodeMirror {
        height: auto;
        min-height: 100px;
        border: 1px solid rgb(75 85 99);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        background-color: #1f2937;
    }
    
    .CodeMirror-focused {
        border-color: rgb(59 130 246);
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
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
        border-left: 2px solid #60a5fa;
    }
    
    .CodeMirror-selected {
        background-color: rgba(59, 130, 246, 0.2);
    }
</style>

<script>
    function navbarBuilder() {
        return {
            useBusinessColors: {{ $navbarSettings->use_business_colors ? 'true' : 'false' }},
            menuItems: {!! json_encode($navbarSettings->getOrderedMenuItems()) !!},
            menuItemsJson: '',
            
            init() {
                // Initialize JSON
                this.updateMenuItemsJson();
                
                // Watch for menuItems changes to update JSON
                this.$watch('menuItems', () => {
                    this.updateMenuItemsJson();
                }, { deep: true });
            },
            
            updateMenuItemsJson() {
                const itemsWithOrder = this.menuItems.map((item, index) => ({
                    label: item.label || '',
                    url: item.url || '',
                    target: item.target || '_self',
                    style: item.style || '',
                    order: index + 1
                }));
                
                this.menuItemsJson = JSON.stringify(itemsWithOrder);
            },
            
            addMenuItem() {
                this.menuItems.push({
                    label: '',
                    url: '',
                    target: '_self',
                    style: '',
                    order: this.menuItems.length + 1
                });
            },
            
            removeMenuItem(index) {
                if (confirm('Are you sure you want to delete this menu item?')) {
                    this.menuItems.splice(index, 1);
                }
            },
            
            moveMenuItem(index, direction) {
                const newIndex = index + direction;
                if (newIndex < 0 || newIndex >= this.menuItems.length) return;
                
                // Swap items
                const temp = this.menuItems[index];
                this.menuItems[index] = this.menuItems[newIndex];
                this.menuItems[newIndex] = temp;
            }
        }
    }
</script>

<script>
    // Initialize CodeMirror for footer HTML editor
    document.addEventListener('DOMContentLoaded', function() {
        const footerHtmlTextarea = document.getElementById('footer_html');
        if (footerHtmlTextarea) {
            window.footerEditor = CodeMirror.fromTextArea(footerHtmlTextarea, {
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
            
            // Set height
            window.footerEditor.setSize(null, 400);
            
            // If section is open on load, refresh immediately
            setTimeout(() => {
                if (window.footerEditor) {
                    window.footerEditor.refresh();
                }
            }, 200);
            
            // Ensure CodeMirror value is synced on form submit
            const form = footerHtmlTextarea.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    window.footerEditor.save();
                });
            }
        }
    });
</script>

<style>
    [x-cloak] { 
        display: none !important; 
    }
</style>

<script>
    let customCssEditor = null;
    let cssPreviewEditor = null;

    // Initialize CodeMirror for custom CSS editor
    document.addEventListener('DOMContentLoaded', function() {
        const customCssTextarea = document.getElementById('custom_css_field');
        
        if (customCssTextarea) {
            customCssEditor = CodeMirror.fromTextArea(customCssTextarea, {
                mode: 'css',
                theme: 'material-darker',
                lineNumbers: true,
                lineWrapping: true,
                indentUnit: 2,
                tabSize: 2,
                indentWithTabs: false,
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
            
            // Set height
            customCssEditor.setSize(null, 500);
            
            // Refresh after a brief delay to ensure proper rendering
            setTimeout(() => {
                if (customCssEditor) {
                    customCssEditor.refresh();
                }
            }, 200);
            
            // Sync on form submit
            const form = customCssTextarea.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    customCssTextarea.value = customCssEditor.getValue();
                });
            }
            
            console.log('CodeMirror CSS Editor initialized');
        }

        // Initialize CodeMirror for CSS Preview (read-only)
        const cssPreviewTextarea = document.getElementById('css_preview_field');
        const cssPreviewPlaceholder = document.getElementById('css_preview_placeholder');
        
        if (cssPreviewTextarea) {
            cssPreviewEditor = CodeMirror.fromTextArea(cssPreviewTextarea, {
                mode: 'css',
                theme: 'material-darker',
                lineNumbers: true,
                lineWrapping: true,
                readOnly: true,
                indentUnit: 2,
                tabSize: 2
            });
            
            // Set height
            cssPreviewEditor.setSize(null, 300);
            
            // Remove placeholder on click
            if (cssPreviewPlaceholder) {
                cssPreviewPlaceholder.addEventListener('click', function() {
                    this.style.display = 'none';
                    cssPreviewEditor.refresh();
                    cssPreviewEditor.focus();
                });
            }
            
            console.log('CodeMirror CSS Preview initialized');
        }
    });

    // Refresh editors when Theme Settings section is opened
    document.addEventListener('alpine:init', () => {
        Alpine.effect(() => {
            // This will run whenever Alpine components update
            setTimeout(() => {
                if (customCssEditor) customCssEditor.refresh();
                if (cssPreviewEditor) cssPreviewEditor.refresh();
            }, 100);
        });
    });
</script>
@endpush
