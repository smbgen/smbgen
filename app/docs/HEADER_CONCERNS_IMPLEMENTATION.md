# CMS Header Concerns Implementation Plan

## Overview
Transform the CMS Theme Settings into a comprehensive header management system that controls all `<head>` concerns: CSS (with VS Code-style editor), CSS class whitelist for AI, SEO metadata, and Open Graph tags.

## ✅ Completed Steps

### 1. Database Schema ✅
- **Migration created**: `2025_12_31_172458_add_header_concerns_to_cms_company_colors_table.php`
- **New fields added to `cms_company_colors` table**:
  - `custom_css` (text) - Custom CSS written in Monaco editor
  - `base_theme_css` (text) - Base theme CSS template
  - `allowed_css_classes` (json) - Whitelist for AI content generation
  - `seo_title_template` (string) - e.g., "{page_title} | Company Name"
  - `seo_meta_description` (text) - Default meta description
  - `seo_meta_keywords` (text) - Default keywords
  - `og_site_name` (string) - Open Graph site name
  - `og_type` (string) - Default: "website"
  - `og_image_url` (text) - Default OG image
  - `twitter_card_type` (string) - Default: "summary_large_image"
  - `twitter_site_handle` (string) - @yoursite
  - `custom_head_scripts` (text) - Additional scripts/styles

### 2. Model Updates ✅
- **Updated `CmsCompanyColors.php`**:
  - Added all new fields to `$fillable`
  - Added `allowed_css_classes` to casts (array)
  - Created `getDefaultCssClassWhitelist()` method with semantic HTML classes

### 3. Frontend Dependencies ✅
- **Installed Monaco Editor**:
  - `monaco-editor` - Core VS Code editor engine
  - `@monaco-editor/react` - React wrapper

### 4. React Components Created ✅
- **`MonacoEditor.jsx`** - VS Code-style CSS editor component
- **`CssClassWhitelist.jsx`** - Interactive whitelist manager with:
  - Add/remove classes
  - Filter/search classes
  - Load default whitelist
  - Grid display with hover actions

## 🔄 Next Implementation Steps

### Step 1: Add API Route for Default CSS Classes
```php
// routes/web.php
Route::get('/admin/cms/default-css-classes', [\App\Http\Controllers\Admin\CmsPageController::class, 'getDefaultCssClasses'])->name('admin.cms.default-css-classes');
```

### Step 2: Update `CmsPageController` Methods

#### Add new method to get default classes:
```php
public function getDefaultCssClasses()
{
    return response()->json([
        'classes' => \App\Models\CmsCompanyColors::getDefaultCssClassWhitelist()
    ]);
}
```

#### Update `updateCompanyColors()` method:
```php
public function updateCompanyColors(Request $request)
{
    $validated = $request->validate([
        // ... existing validation ...
        'custom_css' => 'nullable|string|max:50000',
        'base_theme_css' => 'nullable|string|max:50000',
        'allowed_css_classes' => 'nullable|json',
        'seo_title_template' => 'nullable|string|max:255',
        'seo_meta_description' => 'nullable|string|max:500',
        'seo_meta_keywords' => 'nullable|string|max:500',
        'og_site_name' => 'nullable|string|max:255',
        'og_type' => 'nullable|string|max:50',
        'og_image_url' => 'nullable|url|max:500',
        'twitter_card_type' => 'nullable|string|max:50',
        'twitter_site_handle' => 'nullable|string|max:50',
        'custom_head_scripts' => 'nullable|string|max:10000',
    ]);
    
    $colors = CmsCompanyColors::getSettings();
    
    // Decode allowed_css_classes if it comes as JSON string
    if (isset($validated['allowed_css_classes']) && is_string($validated['allowed_css_classes'])) {
        $validated['allowed_css_classes'] = json_decode($validated['allowed_css_classes'], true);
    }
    
    $colors->update($validated);
    
    return redirect()->back()->with('success', 'Theme settings updated successfully');
}
```

### Step 3: Update `resources/views/admin/cms/index.blade.php`

Add new tabs to the Theme Settings section (after current color pickers):

```blade
{{-- NEW: Tabbed Interface for Advanced Settings --}}
<div class="mt-8 border-t border-gray-700 pt-6" 
     x-data="{ activeTab: 'css-editor' }">
    
    {{-- Tab Navigation --}}
    <div class="flex space-x-4 border-b border-gray-700 mb-6">
        <button type="button" 
                @click="activeTab = 'css-editor'"
                :class="activeTab === 'css-editor' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400'"
                class="pb-3 px-4 border-b-2 font-medium transition-colors">
            <i class="fas fa-code mr-2"></i>CSS Editor
        </button>
        <button type="button" 
                @click="activeTab = 'css-classes'"
                :class="activeTab === 'css-classes' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400'"
                class="pb-3 px-4 border-b-2 font-medium transition-colors">
            <i class="fas fa-list mr-2"></i>CSS Class Whitelist
        </button>
        <button type="button" 
                @click="activeTab = 'seo'"
                :class="activeTab === 'seo' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400'"
                class="pb-3 px-4 border-b-2 font-medium transition-colors">
            <i class="fas fa-search mr-2"></i>SEO & Meta Tags
        </button>
    </div>

    {{-- CSS Editor Tab --}}
    <div x-show="activeTab === 'css-editor'" x-cloak>
        <div class="space-y-4">
            <div>
                <label class="form-label">Custom CSS</label>
                <p class="text-sm text-gray-400 mb-3">Write custom CSS that will be injected into all CMS pages. This CSS will load after theme CSS.</p>
                <div id="css-editor-mount"></div>
                <textarea 
                    name="custom_css" 
                    id="custom_css_field"
                    class="hidden">{{ old('custom_css', $companyColors->custom_css) }}</textarea>
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
    <div x-show="activeTab === 'css-classes'" x-cloak>
        <div id="css-class-whitelist-mount"></div>
    </div>

    {{-- SEO Tab --}}
    <div x-show="activeTab === 'seo'" x-cloak>
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
                       value="{{ old('seo_title_template', $companyColors->seo_title_template ?? '{page_title} | Your Company') }}"
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

@push('scripts')
<script type="module">
import { createRoot } from 'react-dom/client';
import React from 'react';
import MonacoEditor from '@/components/MonacoEditor';
import CssClassWhitelist from '@/components/CssClassWhitelist';

// Mount Monaco CSS Editor
const editorMount = document.getElementById('css-editor-mount');
if (editorMount) {
    const editorRoot = createRoot(editorMount);
    editorRoot.render(
        <MonacoEditor 
            value={document.getElementById('custom_css_field').value}
            onChange={(value) => {
                document.getElementById('custom_css_field').value = value;
            }}
            language="css"
            height="500px"
            theme="vs-dark"
        />
    );
}

// Mount CSS Class Whitelist
const whitelistMount = document.getElementById('css-class-whitelist-mount');
if (whitelistMount) {
    const initialClasses = {{ json_encode($companyColors->allowed_css_classes ?? []) }};
    const whitelistRoot = createRoot(whitelistMount);
    whitelistRoot.render(
        <CssClassWhitelist 
            initialClasses={initialClasses}
            onChange={(classes) => {
                // Updates hidden input automatically
            }}
        />
    );
}
</script>
@endpush
```

### Step 4: Update CMS Layout to Use Header Concerns

Update `resources/views/layouts/cms.blade.php` to inject all header concerns:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- SEO Meta Tags --}}
    @php
        $companyColors = \App\Models\CmsCompanyColors::getSettings();
        $pageTitle = $title ?? 'Home';
        $seoTitle = str_replace('{page_title}', $pageTitle, $companyColors->seo_title_template ?? '{page_title}');
    @endphp
    
    <title>{{ $seoTitle }}</title>
    
    @if($companyColors->seo_meta_description)
        <meta name="description" content="{{ $companyColors->seo_meta_description }}">
    @endif
    
    @if($companyColors->seo_meta_keywords)
        <meta name="keywords" content="{{ $companyColors->seo_meta_keywords }}">
    @endif
    
    {{-- Open Graph Tags --}}
    @if($companyColors->og_site_name)
        <meta property="og:site_name" content="{{ $companyColors->og_site_name }}">
    @endif
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:type" content="{{ $companyColors->og_type ?? 'website' }}">
    @if($companyColors->seo_meta_description)
        <meta property="og:description" content="{{ $companyColors->seo_meta_description }}">
    @endif
    @if($companyColors->og_image_url)
        <meta property="og:image" content="{{ $companyColors->og_image_url }}">
    @endif
    <meta property="og:url" content="{{ request()->url() }}">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $companyColors->twitter_card_type ?? 'summary_large_image' }}">
    @if($companyColors->twitter_site_handle)
        <meta name="twitter:site" content="@{{ $companyColors->twitter_site_handle }}">
    @endif
    <meta name="twitter:title" content="{{ $seoTitle }}">
    @if($companyColors->seo_meta_description)
        <meta name="twitter:description" content="{{ $companyColors->seo_meta_description }}">
    @endif
    @if($companyColors->og_image_url)
        <meta name="twitter:image" content="{{ $companyColors->og_image_url }}">
    @endif
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Theme CSS (Generated) --}}
    @if($companyColors->auto_inject_css)
        {!! $companyColors->generateCSS() !!}
    @endif
    
    {{-- Custom CSS from Editor --}}
    @if($companyColors->custom_css)
        <style>
            {!! $companyColors->custom_css !!}
        </style>
    @endif
    
    {{-- Custom Head Scripts --}}
    @if($companyColors->custom_head_scripts)
        {!! $companyColors->custom_head_scripts !!}
    @endif
</head>
<body>
    {{-- Page content --}}
</body>
</html>
```

### Step 5: Update AI Prompts to Use CSS Class Whitelist

Update `config/ai.php` prompts to reference whitelisted classes:

```php
'blog_post' => 'You are an SEO content expert creating blog posts...

Rules:
- Output clean, well-structured HTML only
- Use ONLY CSS classes from the provided whitelist
- Do NOT use any framework-specific classes (Bootstrap, Tailwind utilities)
- Do NOT include <html>, <head>, or <body> tags
- No inline JavaScript or CSS
- Use semantic HTML tags (article, section, h1-h6, p, ul, ol)
- Wrap content in <div class="prose"> for automatic styling

Available CSS Classes:
{css_class_whitelist}

Example structure:
<div class="prose">
  <article class="content-block">
    <h1>Article Title</h1>
    <p class="lead">Introduction paragraph...</p>
    <section>
      <h2>Section Heading</h2>
      <p>Content...</p>
    </section>
    <div class="cta-section">
      <a href="#" class="btn-primary">Call to Action</a>
    </div>
  </article>
</div>',
```

Then in the AIContentController, inject the whitelist:

```php
$companyColors = CmsCompanyColors::getSettings();
$cssClassWhitelist = $companyColors->allowed_css_classes ?? CmsCompanyColors::getDefaultCssClassWhitelist();

$systemPrompt = str_replace(
    '{css_class_whitelist}',
    implode(', ', array_map(fn($class) => ".{$class}", $cssClassWhitelist)),
    config('ai.prompts.' . $promptType)
);
```

## Benefits

1. **Theme-Agnostic AI Content** - AI generates semantic HTML that inherits theme colors/styles
2. **Full CSS Control** - VS Code-quality editor for custom styling
3. **SEO Optimization** - Comprehensive meta tag management
4. **Class Safety** - Whitelist prevents AI from using non-existent classes
5. **Easy Theme Switching** - Content adapts automatically when theme changes
6. **Professional Workflow** - Monaco editor provides syntax highlighting, validation, autocomplete

## Testing Checklist

- [ ] CSS Editor saves and loads correctly
- [ ] Monaco Editor has syntax highlighting
- [ ] CSS Class Whitelist add/remove/filter works
- [ ] Load defaults button populates whitelist
- [ ] SEO meta tags appear in page source
- [ ] OG tags validate with Facebook Debugger
- [ ] Twitter Card validates with Twitter Card Validator
- [ ] Custom CSS applies to CMS pages
- [ ] Theme switching preserves custom CSS
- [ ] AI content uses only whitelisted classes
- [ ] Generated content inherits theme colors properly

## File Summary

**Created**:
- `database/migrations/2025_12_31_172458_add_header_concerns_to_cms_company_colors_table.php`
- `resources/js/components/MonacoEditor.jsx`
- `resources/js/components/CssClassWhitelist.jsx`

**Modified**:
- `app/Models/CmsCompanyColors.php` - Added fillable fields, casts, getDefaultCssClassWhitelist()

**To Modify**:
- `routes/web.php` - Add default CSS classes route
- `app/Http/Controllers/Admin/CmsPageController.php` - Add getDefaultCssClasses() and update updateCompanyColors()
- `resources/views/admin/cms/index.blade.php` - Add tabs for CSS Editor, Whitelist, SEO
- `resources/views/layouts/cms.blade.php` - Inject meta tags and custom CSS
- `config/ai.php` - Update prompts to use whitelist
- `app/Http/Controllers/AIContentController.php` - Inject whitelist into prompts

## Next User Actions Required

Would you like me to:
1. Complete the full implementation by updating all remaining files?
2. Focus on a specific section first (CSS Editor, SEO, or AI prompts)?
3. Create a demo/test page to validate the implementation?
