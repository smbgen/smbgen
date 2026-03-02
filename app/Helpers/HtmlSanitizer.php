<?php

namespace App\Helpers;

class HtmlSanitizer
{
    /**
     * Basic HTML sanitizer for CMS content.
     * - Strips script tags
     * - Removes event handlers and javascript: URLs
     * - Keeps a conservative set of tags
     */
    public static function sanitizeContent(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html) ?? '';
        $clean = preg_replace('/on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? '';
        $clean = preg_replace('/javascript:\s*/i', '', $clean) ?? '';

        $allowedTags = '<p><div><section><article><header><footer><main><span><strong><em><b><i><u><ol><ul><li><a><img><h1><h2><h3><h4><h5><h6><blockquote><code><pre><hr><br><figure><figcaption><style><link><meta><svg><path><circle><rect><polygon><line><canvas><table><thead><tbody><tr><th><td><form><input><textarea><select><option><button><label><nav>';

        return strip_tags($clean, $allowedTags);
    }

    /**
     * Sanitize CSS blocks to drop dangerous constructs.
     */
    public static function sanitizeCss(?string $css): string
    {
        if ($css === null || $css === '') {
            return '';
        }

        $clean = preg_replace('/<\/?style[^>]*>/i', '', $css) ?? '';
        $clean = preg_replace('/@import[^;]+;/i', '', $clean) ?? '';
        $clean = preg_replace('/expression\s*\([^)]*\)/i', '', $clean) ?? '';
        $clean = preg_replace('/url\(\s*["\']?\s*javascript:[^)]*\)/i', '', $clean) ?? '';

        return trim($clean);
    }

    /**
     * Allow only external script tags over http/https; drop inline scripts.
     */
    public static function sanitizeHeadScripts(?string $scripts): string
    {
        if ($scripts === null || $scripts === '') {
            return '';
        }

        $output = '';

        preg_match_all('/<script[^>]*src=["\']([^"\']+)["\'][^>]*><\/script>/i', $scripts, $matches);

        foreach ($matches[1] as $src) {
            if (str_starts_with($src, 'https://') || str_starts_with($src, 'http://')) {
                $output .= '<script src="'.$src.'" defer></script>';
            }
        }

        return $output;
    }

    /**
     * Sanitize head content allowing styles, scripts, meta tags, and links.
     * More permissive than sanitizeContent() for head elements.
     * Preserves script and style tag contents intact.
     */
    public static function sanitizeHeadContent(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        // Extract and protect script tags and their content
        $scripts = [];
        $scriptPlaceholder = '___SCRIPT_PLACEHOLDER_';
        $html = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function($matches) use (&$scripts, $scriptPlaceholder) {
            $index = count($scripts);
            $scripts[$index] = $matches[0];
            return $scriptPlaceholder . $index . '___';
        }, $html);

        // Extract and protect style tags and their content
        $styles = [];
        $stylePlaceholder = '___STYLE_PLACEHOLDER_';
        $html = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function($matches) use (&$styles, $stylePlaceholder) {
            $index = count($styles);
            $styles[$index] = $matches[0];
            return $stylePlaceholder . $index . '___';
        }, $html);

        // Now sanitize the remaining HTML (without script/style content)
        $clean = preg_replace('/on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        $clean = preg_replace('/javascript:\s*(?![^<]*<\/script>)/i', '', $clean) ?? '';
        
        // Allow head-specific tags
        $allowedTags = '<link><meta><title><base><noscript>';
        $clean = strip_tags($clean, $allowedTags);

        // Restore script tags with their original content
        foreach ($scripts as $index => $script) {
            $clean = str_replace($scriptPlaceholder . $index . '___', $script, $clean);
        }

        // Restore style tags with their original content
        foreach ($styles as $index => $style) {
            $clean = str_replace($stylePlaceholder . $index . '___', $style, $clean);
        }

        return $clean;
    }
}
