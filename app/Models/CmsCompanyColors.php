<?php

namespace App\Models;

use App\Helpers\HtmlSanitizer;
use Illuminate\Database\Eloquent\Model;

class CmsCompanyColors extends Model
{
    protected $fillable = [
        'primary_color',
        'secondary_color',
        'background_color',
        'body_background_color',
        'text_color',
        'accent_color',
        'auto_inject_css',
        'theme_preset',
        'enabled_effects',
        'custom_css',
        'base_theme_css',
        'allowed_css_classes',
        'seo_title_template',
        'seo_meta_description',
        'seo_meta_keywords',
        'og_site_name',
        'og_type',
        'og_image_url',
        'twitter_card_type',
        'twitter_site_handle',
        'custom_head_scripts',
    ];

    protected $attributes = [
        'enabled_effects' => '[]',
    ];

    protected function casts(): array
    {
        return [
            'auto_inject_css' => 'boolean',
            'enabled_effects' => 'array',
            'allowed_css_classes' => 'array',
        ];
    }

    public function sanitizedCustomCss(): string
    {
      return HtmlSanitizer::sanitizeCss($this->custom_css);
    }

    public function sanitizedCustomHeadScripts(): string
    {
      return HtmlSanitizer::sanitizeHeadScripts($this->custom_head_scripts);
    }

    /**
     * Available theme presets
     */
    public static function getThemePresets(): array
    {
        return [
            'default' => [
                'name' => 'Default (Minimal)',
                'description' => 'Clean, professional design with standard colors',
                'primary' => '#3B82F6',
                'secondary' => '#10B981',
                'background' => '#1f2937',
                'body_background' => '#ffffff',
                'text' => '#1f2937',
                'accent' => '#F59E0B',
            ],
            'smbgen' => [
                'name' => 'smbgen (Contractor)',
                'description' => 'Construction theme with orange accents and animated effects',
                'primary' => '#FF6B35',
                'secondary' => '#4A5568',
                'background' => '#2D3748',
                'body_background' => '#f5f5f4',
                'text' => '#1c1917',
                'accent' => '#FBD38D',
            ],
            'modern' => [
                'name' => 'Modern (Tech)',
                'description' => 'Sleek gradients and smooth transitions',
                'primary' => '#8B5CF6',
                'secondary' => '#EC4899',
                'background' => '#0F172A',
                'body_background' => '#0F172A',
                'text' => '#F8FAFC',
                'accent' => '#06B6D4',
            ],
            'nature' => [
                'name' => 'Nature (Organic)',
                'description' => 'Earth tones with natural animations',
                'primary' => '#059669',
                'secondary' => '#84CC16',
                'background' => '#14532D',
                'body_background' => '#f0fdf4',
                'text' => '#14532d',
                'accent' => '#FCD34D',
            ],
            'corporate' => [
                'name' => 'Corporate (Professional)',
                'description' => 'Classic navy and charcoal with minimal effects',
                'primary' => '#1E40AF',
                'secondary' => '#475569',
                'background' => '#1E293B',
                'body_background' => '#f8fafc',
                'text' => '#0f172a',
                'accent' => '#3B82F6',
            ],
        ];
    }

    /**
     * Available effects
     */
    public static function getAvailableEffects(): array
    {
        return [
            'hero_animations' => 'Hero Animations (3D tilt, slide-in text)',
            'floating_icons' => 'Floating Background Icons',
            'hover_effects' => 'Button & Card Hover Effects',
            'smooth_scrolling' => 'Smooth Page Scrolling',
            'gradient_backgrounds' => 'Gradient Backgrounds',
        ];
    }

    /**
     * Get default CSS class whitelist for AI content generation
     */
    public static function getDefaultCssClassWhitelist(): array
    {
        return [
            // Layout & Structure
            'container', 'section', 'hero', 'content-block', 'feature-grid', 'feature-card',

            // Feature Carousel Components
            'feature-badge', 'feature-icon', 'feature-label', 'feature-description',

            // Buttons
            'btn-primary', 'btn-secondary', 'btn-accent', 'btn-ghost', 'btn-outline',

            // Text & Typography
            'heading', 'subheading', 'lead', 'text-brand', 'text-accent', 'prose',

            // Cards & Components
            'card', 'badge', 'alert', 'alert-success', 'alert-info', 'alert-warning', 'alert-error',

            // Backgrounds & Colors
            'bg-brand', 'bg-accent', 'gradient-hero', 'gradient-accent',
            'bg-white', 'bg-blue-50', 'bg-blue-900/20', 'bg-gray-800',

            // CTA & Forms
            'cta-section', 'cta-button', 'form-group', 'form-input', 'form-label',

            // Grid & Flex
            'grid', 'flex', 'gap-2', 'gap-4', 'gap-6', 'gap-8',
            'flex-shrink-0', 'overflow-x-auto', 'snap-x', 'snap-mandatory', 'snap-center',

            // Spacing (Tailwind utilities)
            'p-4', 'p-6', 'p-8', 'py-4', 'py-6', 'py-8', 'py-12', 'px-4', 'px-6',
            'mt-4', 'mt-6', 'mt-8', 'mb-4', 'mb-6', 'mb-8', 'mb-1', 'mb-3',
            'my-12', 'pb-4',

            // Sizing
            'min-w-[180px]', 'text-4xl', 'text-sm', 'text-xs',

            // Borders
            'border-2', 'border-blue-600', 'border-gray-300',

            // Dark Mode
            'dark:bg-blue-900/20', 'dark:bg-gray-800', 'dark:border-gray-600',
            'dark:text-white', 'dark:text-gray-400',

            // Text Colors
            'text-gray-900', 'text-gray-600', 'text-center',

            // Interactions
            'cursor-pointer', 'transition-all', 'duration-300',
            'hover:border-blue-500',

            // Responsive
            'md:grid-cols-2', 'md:grid-cols-3', 'lg:grid-cols-3', 'lg:grid-cols-4',

            // Miscellaneous
            'rounded', 'rounded-lg', 'shadow', 'shadow-lg', 'hover:shadow-xl',
            'scrollbar-hide', 'font-bold',
        ];
    }

    /**
     * Get the singleton company colors settings
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'primary_color' => config('business.branding.primary_color') ?: '#3B82F6',
            'secondary_color' => config('business.branding.secondary_color') ?: '#10B981',
            'background_color' => config('business.branding.background_color') ?: '#1f2937',
            'body_background_color' => '#ffffff',
            'text_color' => '#1f2937',
            'accent_color' => '#F59E0B',
            'auto_inject_css' => true,
        ]);
    }

    /**
     * Generate CSS for theme
     */
    public function generateCSS(): string
    {
        $effects = $this->enabled_effects ?? [];
        $theme = $this->theme_preset ?? 'default';

        $css = <<<CSS
<style>
:root {
  --brand-primary: {$this->primary_color};
  --brand-secondary: {$this->secondary_color};
  --brand-navbar: {$this->background_color};
  --brand-text: {$this->text_color};
  --brand-accent: {$this->accent_color};
}

/* Layout & Structure - Higher specificity */
body .container {
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 1rem;
  padding-right: 1rem;
}

body .section {
  padding-top: 4rem;
  padding-bottom: 4rem;
}

body .hero {
  padding-top: 6rem;
  padding-bottom: 6rem;
  text-align: center;
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

body .content-block {
  margin-bottom: 2rem;
}

/* Typography - Higher specificity */
body .heading {
  font-size: 2.25rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 1rem;
  color: inherit;
}

body .subheading {
  font-size: 1.5rem;
  font-weight: 600;
  line-height: 1.3;
  margin-bottom: 0.75rem;
  color: inherit;
}

body .lead {
  font-size: 1.25rem;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  color: inherit;
}

/* Buttons */
.btn-primary {
  background-color: var(--brand-primary);
  border: 1px solid var(--brand-primary);
  border-radius: 0.5rem;
  color: var(--brand-text);
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-block;
  border: none;
  cursor: pointer;
}

.btn-secondary {
  background-color: var(--brand-secondary);
  color: var(--brand-text);
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-block;
  border: none;
  cursor: pointer;
}

.btn-accent {
  background-color: var(--brand-accent);
  color: var(--brand-text);
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-block;
  border: none;
  cursor: pointer;
}

.cta-button {
  font-size: 1.125rem;
  padding: 1rem 2rem;
}

/* Cards & Components */
.card {
  background-color: white;
  border-radius: 0.5rem;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

body .feature-card {
  background-color: white !important;
  border-radius: 0.5rem;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
  color: var(--brand-text) !important;
}

body .feature-card svg {
  color: var(--brand-primary) !important;
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

body .feature-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

body .feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  width: 100%;
}

/* Feature Carousel Badge */
body .feature-badge {
  flex-shrink: 0;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid #d1d5db;
  background-color: white;
  border-radius: 0.5rem;
  padding: 1.5rem;
  min-width: 180px;
  text-align: center;
}

body .feature-badge.active,
body .feature-badge:hover {
  border-color: var(--brand-primary);
  background-color: rgba(59, 130, 246, 0.05);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

body .feature-icon {
  font-size: 2.5rem;
  line-height: 1;
  margin-bottom: 0.75rem;
}

body .feature-label {
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  margin-bottom: 0.25rem;
  color: var(--brand-text);
}

body .feature-description {
  font-size: 0.75rem;
  color: #6b7280;
  line-height: 1.3;
}

/* Hide scrollbar for feature carousel */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Colors */
body .text-brand { color: var(--brand-primary); }
body .bg-brand { background-color: var(--brand-primary); color: white; }
body .text-accent { color: var(--brand-accent); }
body .bg-accent { background-color: var(--brand-accent); color: white; }

body .text-white { color: white !important; }
body .text-gray-900 { color: var(--brand-text) !important; }

/* Ensure regular sections have dark text */
body .section {
  color: var(--brand-text) !important;
}

body .section .heading,
body .section .lead,
body .section p,
body .section li {
  color: var(--brand-text) !important;
}

/* Content blocks should have dark text */
body .content-block {
  color: var(--brand-text) !important;
}

body .content-block .heading,
body .content-block h2,
body .content-block h3 {
  color: var(--brand-text) !important;
}

/* Backgrounds */
body .gradient-hero {
  background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%) !important;
  color: var(--brand-text) !important;
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

body .gradient-hero .text-brand {
  color: var(--brand-text) !important;
}

body .gradient-hero .heading,
body .gradient-hero .lead {
  color: var(--brand-text) !important;
}

.gradient-accent {
  background: linear-gradient(45deg, var(--brand-accent) 0%, var(--brand-primary) 100%);
}

/* CTA Section */
body .cta-section {
  padding: 4rem 0;
  text-align: center;
  color: var(--brand-text) !important;
  background-color: var(--brand-primary) !important;
}

body .cta-section .heading,
body .cta-section .lead {
  color: var(--brand-text) !important;
}

body .cta-section .form-group {
  margin-bottom: 1rem;
  text-align: left;
}

body .cta-section .form-label {
  color: var(--brand-text) !important;
  display: block;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

body .cta-section .form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
  color: var(--brand-text) !important;
  background-color: white !important;
}

/* Forms */
.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: inherit;
}

.cta-section .form-label {
  color: var(--brand-text) !important;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
  color: var(--brand-text);
  background-color: var(--brand-background);
}

/* Responsive Grid */
@media (min-width: 768px) {
  .md\:grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
  .md\:grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
  .md\:grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
}

/* Prose Styling */
body .prose {
  line-height: 1.75;
  color: var(--brand-text) !important;
}

body .prose ul {
  list-style-type: disc;
  margin-left: 1.5rem;
  color: var(--brand-text) !important;
}

body .prose li {
  margin-bottom: 0.5rem;
  color: var(--brand-text) !important;
}

/* Body Styling */
body {
  background-color: {$this->body_background_color};
  color: var(--brand-text) !important;
  line-height: 1.6;
}

/* SIMPLE TEXT COLOR FIXES - Target content areas only, never nav */
.section, .content-block, .feature-grid, .prose {
  color: var(--brand-text) !important;
}

.section *, .content-block *, .feature-grid *, .prose * {
  color: var(--brand-text) !important;
}

/* Force dark text in main content areas */
main, main *, article, article *, .cms-content, .cms-content * {
  color: var(--brand-text) !important;
}

/* Specific heading fixes */
h1, h2, h3, h4, h5, h6 {
  color: var(--brand-text) !important;
}

.section h1, .content-block h1, main h1, article h1, .cms-content h1 {
  color: var(--brand-text) !important;
}

/* Brand color headings */
.heading, .text-brand {
  color: var(--brand-primary) !important;
}

/* Hero sections - let CSS handle naturally, only fix feature cards inside */
.hero .feature-card, .hero .feature-card * {
  color: var(--brand-text) !important;
}

/* Override white text classes in content (not nav) */
.section .text-white, 
.content-block .text-white,
.prose .text-white,
main .text-white,
article .text-white {
  color: var(--brand-text) !important;
}

CSS;

        // Add hover effects if enabled
        if (in_array('hover_effects', $effects)) {
            $css .= <<<'CSS'

/* Enhanced Hover Effects */
.btn-primary:hover, .btn-secondary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
}

.card:hover, .admin-card:hover {
  transform: scale(1.02);
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

CSS;
        }

        // Add hero animations if enabled
        if (in_array('hero_animations', $effects)) {
            $css .= <<<'CSS'

/* Hero Animations */
@keyframes tiltMotion {
  0%, 100% { transform: rotateY(-2deg) rotateX(1deg); }
  50% { transform: rotateY(2deg) rotateX(-1deg); }
}

@keyframes heroTextSlideIn {
  0% { opacity: 0; transform: translateY(30px); }
  100% { opacity: 1; transform: translateY(0); }
}

.hero-headline {
  animation: tiltMotion 4s ease-in-out infinite;
  perspective: 1000px;
  transform-style: preserve-3d;
}

.hero-text {
  animation: heroTextSlideIn 0.8s ease-out 0.2s both;
}

.hero-visual {
  transition: background-image 1s ease-in-out;
}

.hero-visual:hover {
  transform: scale(1.05);
}

CSS;
        }

        // Add floating icons if enabled
        if (in_array('floating_icons', $effects)) {
            $css .= <<<'CSS'

/* Floating Background Icons */
@keyframes float-slow {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

.floating-icon {
  animation: float-slow 8s ease-in-out infinite;
  position: absolute;
  opacity: 0.12;
  z-index: 1;
}

CSS;
        }

        // Add smooth scrolling if enabled
        if (in_array('smooth_scrolling', $effects)) {
            $css .= <<<'CSS'

/* Smooth Scrolling */
html {
  scroll-behavior: smooth;
}

CSS;
        }

        // Add gradient backgrounds if enabled
        if (in_array('gradient_backgrounds', $effects)) {
            $css .= <<<'CSS'

/* Gradient Backgrounds - handled by main CSS above */

CSS;
        }

        // Add theme-specific styles
        if ($theme === 'smbgen') {
            $css .= <<<'CSS'

/* smbgen Theme Specific */
:root {
  --contractor-orange: #FF6B35;
  --steel-grey: #4A5568;
  --charcoal: #2D3748;
  --concrete: #E2E8F0;
  --safety-yellow: #FBD38D;
}

CSS;
        }

        if ($theme === 'modern') {
            $css .= <<<'CSS'

/* Modern Theme Specific */
body {
  background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
  color: #F8FAFC;
}

.card {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

CSS;
        }

        $css .= "\n</style>";

        return $css;
    }

    /**
     * Get navbar background color
     */
    public function getNavbarColor(): string
    {
        return $this->background_color;
    }

    /**
     * Get contrasting text color for navbar
     */
    public function getNavbarTextColor(): string
    {
        // Calculate luminance
        $hex = ltrim($this->background_color, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.5 ? '#1f2937' : '#ffffff';
    }
}
