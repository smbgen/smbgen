<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsCompanyColors;
use App\Models\CmsFooterSetting;
use App\Models\CmsNavbarSetting;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CmsPageController extends Controller
{
    /**
     * Reserved slugs that cannot be used for CMS pages
     * These match existing application routes
     */
    protected array $reservedSlugs = [
        'login', 'register', 'logout', 'password', 'forgot-password', 'reset-password',
        'dashboard', 'admin', 'api', 'sanctum', 'livewire', 'telescope', 'horizon',
        'profile', 'settings', 'verify-email', 'email', 'auth', 'oauth',
        'messages', 'billing', 'payment', 'invoice', 'clients', 'users',
        'book', 'booking', 'schedule', 'availability', 'calendar',
        'track', 'webhook', 'magic-link', 'documents', 'files', 'storage',
        'leads', 'leadform', 'landing2', 'cyber-audit-demo', 'seo-assistant',
        'status', 'health', 'debug', 'test', 'social-accounts', 'cms',
    ];

    public function index()
    {
        $pages = CmsPage::orderBy('slug')->get();
        $navbarSettings = CmsNavbarSetting::getSettings();
        $footerSettings = CmsFooterSetting::getSettings();
        $companyColors = CmsCompanyColors::getSettings();

        // Check for required pages
        $requiredPages = ['home'];
        $missingPages = collect($requiredPages)->filter(function ($slug) use ($pages) {
            return ! $pages->contains('slug', $slug);
        });

        // Check for optional pages with fallbacks
        $hasContactPage = $pages->contains('slug', 'contact');
        $hasBookPage = $pages->contains('slug', 'book');

        return view('admin.cms.index', compact('pages', 'navbarSettings', 'footerSettings', 'companyColors', 'missingPages', 'hasContactPage', 'hasBookPage'));
    }

    public function create()
    {
        // Get public files for media library
        $publicFiles = \App\Models\ClientFile::forCms()->get();

        return view('admin.cms.create', compact('publicFiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:cms_pages,slug',
                Rule::notIn($this->reservedSlugs),
            ],
            'title' => 'required|string|max:255',
            'head_content' => 'nullable|string',
            'body_content' => 'nullable|string',
            'footer_scripts' => 'nullable|string',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:255',
            'background_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'is_published' => 'boolean',
            'show_navbar' => 'boolean',
            'show_footer' => 'boolean',
            'has_form' => 'boolean',
            'form_fields' => 'nullable|json',
            'form_submit_button_text' => 'nullable|string|max:100',
            'form_success_message' => 'nullable|string|max:500',
            'form_redirect_url' => 'nullable|string|max:255',
            'notification_email' => 'nullable|email|max:255',
            'send_admin_notification' => 'boolean',
            'send_client_notification' => 'boolean',
        ], [
            'slug.not_in' => 'The slug ":input" is reserved and cannot be used. Please choose a different slug.',
        ]);

        // Convert form_fields JSON string to array if present
        if (isset($validated['form_fields'])) {
            $validated['form_fields'] = json_decode($validated['form_fields'], true);
        }

        $page = CmsPage::create($validated);

        return redirect()->route('admin.cms.index')
            ->with('success', 'Page created successfully!');
    }

    public function show(CmsPage $cmsPage)
    {
        return view('admin.cms.show', compact('cmsPage'));
    }

    public function edit(CmsPage $cmsPage)
    {
        // Get public files for media library
        $publicFiles = \App\Models\ClientFile::forCms()->get();

        return view('admin.cms.edit', compact('cmsPage', 'publicFiles'));
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $validated = $request->validate([
            'slug' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:cms_pages,slug,'.$cmsPage->id,
                Rule::notIn($this->reservedSlugs),
            ],
            'title' => 'required|string|max:255',
            'head_content' => 'nullable|string',
            'body_content' => 'nullable|string',
            'footer_scripts' => 'nullable|string',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:255',
            'background_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'is_published' => 'boolean',
            'show_navbar' => 'boolean',
            'show_footer' => 'boolean',
            'has_form' => 'boolean',
            'form_fields' => 'nullable|json',
            'form_submit_button_text' => 'nullable|string|max:100',
            'form_success_message' => 'nullable|string|max:500',
            'form_redirect_url' => 'nullable|string|max:255',
            'notification_email' => 'nullable|email|max:255',
            'send_admin_notification' => 'boolean',
            'send_client_notification' => 'boolean',
        ], [
            'slug.not_in' => 'The slug ":input" is reserved and cannot be used. Please choose a different slug.',
        ]);

        // Convert form_fields JSON string to array if present
        if (isset($validated['form_fields'])) {
            $validated['form_fields'] = json_decode($validated['form_fields'], true);
        }

        $cmsPage->update($validated);

        return redirect()->route('admin.cms.index')
            ->with('success', 'Page updated successfully!');
    }

    public function destroy(CmsPage $cmsPage)
    {
        $cmsPage->delete();

        return redirect()->route('admin.cms.index')
            ->with('success', 'Page deleted successfully!');
    }

    public function duplicate(CmsPage $cmsPage)
    {
        // Use Laravel's replicate method to copy all attributes
        $newPage = $cmsPage->replicate();

        // Generate unique slug with timestamp
        $timestamp = now()->timestamp;
        $newPage->slug = $cmsPage->slug.'-copy-'.$timestamp;

        // Append (Copy) to title
        $newPage->title = $cmsPage->title.' (Copy)';

        // Force as draft
        $newPage->is_published = false;

        // Save the duplicated page
        $newPage->save();

        return redirect()->route('admin.cms.edit', $newPage)
            ->with('success', 'Page duplicated successfully! Update the slug and publish when ready.');
    }

    public function updateNavbar(Request $request)
    {
        \Log::info('=== NAVBAR UPDATE STARTED ===');
        \Log::info('Raw request data:', $request->all());

        $validated = $request->validate([
            'logo_text' => 'nullable|string|max:255',
            'logo_image_url' => 'nullable|string|max:500',
            'use_business_colors' => 'boolean',
            'is_sticky' => 'boolean',
            'custom_bg_color' => 'nullable|string|max:20',
            'custom_text_color' => 'nullable|string|max:20',
            'theme' => 'nullable|string|in:default,smbgen',
            'menu_items' => 'nullable|json',
        ]);

        \Log::info('Validated data:', $validated);

        // Convert menu_items JSON string to array if present
        if (isset($validated['menu_items'])) {
            \Log::info('menu_items raw JSON string:', ['json' => $validated['menu_items']]);

            $menuItems = json_decode($validated['menu_items'], true);
            \Log::info('menu_items decoded array:', ['decoded' => $menuItems]);

            // Normalize menu items to ensure all have required fields
            $validated['menu_items'] = array_map(function ($item) {
                return array_merge([
                    'label' => '',
                    'url' => '',
                    'target' => '_self',
                    'style' => '',
                    'order' => 0,
                ], $item);
            }, $menuItems ?? []);

            \Log::info('menu_items after normalization:', ['normalized' => $validated['menu_items']]);
        }

        $navbarSettings = CmsNavbarSetting::getSettings();
        \Log::info('Current navbar settings before update:', [
            'id' => $navbarSettings->id,
            'current_menu_items' => $navbarSettings->menu_items,
            'raw_menu_items' => $navbarSettings->getRawOriginal('menu_items'),
        ]);

        $navbarSettings->update($validated);
        \Log::info('Update method called');

        // Refresh from database to see what was actually saved
        $navbarSettings->refresh();
        \Log::info('Navbar settings after update and refresh:', [
            'id' => $navbarSettings->id,
            'new_menu_items' => $navbarSettings->menu_items,
            'raw_menu_items' => $navbarSettings->getRawOriginal('menu_items'),
            'updated_at' => $navbarSettings->updated_at,
        ]);

        \Log::info('=== NAVBAR UPDATE COMPLETED ===');

        return redirect()->route('admin.cms.index')
            ->with('success', 'Navbar settings updated successfully!')
            ->with('open_navbar', true);
    }

    public function updateFooter(Request $request)
    {
        $validated = $request->validate([
            'footer_html' => 'nullable|string',
            'use_default' => 'boolean',
        ]);

        $footerSettings = CmsFooterSetting::getSettings();
        $footerSettings->update($validated);

        return redirect()->route('admin.cms.index')
            ->with('success', 'Footer settings updated successfully')
            ->with('open_footer', true);
    }

    public function updateCompanyColors(Request $request)
    {
        $validated = $request->validate([
            'theme_preset' => 'required|string|in:default,smbgen,modern,nature,corporate',
            'enabled_effects' => 'nullable|array',
            'enabled_effects.*' => 'string|in:hero_animations,floating_icons,hover_effects,smooth_scrolling,gradient_backgrounds',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'body_background_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'auto_inject_css' => 'boolean',
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

        // Ensure enabled_effects is an array (empty if not provided)
        $validated['enabled_effects'] = $validated['enabled_effects'] ?? [];

        // Decode allowed_css_classes if it comes as JSON string
        if (isset($validated['allowed_css_classes']) && is_string($validated['allowed_css_classes'])) {
            $validated['allowed_css_classes'] = json_decode($validated['allowed_css_classes'], true);
        }

        $companyColors = CmsCompanyColors::getSettings();
        $companyColors->update($validated);

        return redirect()->route('admin.cms.index')
            ->with('success', 'Theme settings updated successfully!');
    }

    public function getDefaultCssClasses()
    {
        return response()->json([
            'classes' => CmsCompanyColors::getDefaultCssClassWhitelist(),
        ]);
    }
}
