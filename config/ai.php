<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Content Generation
    |--------------------------------------------------------------------------
    |
    | Enable or disable AI-powered content generation features across the CMS.
    | When disabled, AI buttons and features will be hidden from the admin UI.
    |
    */

    'enabled' => env('AI_CONTENT_GENERATION_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which AI provider to use for content generation.
    | Currently supported: 'anthropic' (Claude)
    |
    */

    'provider' => env('AI_PROVIDER', 'anthropic'),

    /*
    |--------------------------------------------------------------------------
    | Anthropic (Claude) Configuration
    |--------------------------------------------------------------------------
    |
    | API credentials and model settings for Claude AI integration.
    |
    */

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-opus-4-1'),
        'api_version' => '2023-06-01',
        'max_tokens' => env('ANTHROPIC_MAX_TOKENS', 4096),
        'temperature' => env('ANTHROPIC_TEMPERATURE', 0.7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Generation Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for AI content generation requests.
    |
    */

    'generation' => [
        'timeout' => 30, // seconds
        'retry_attempts' => 2,
        'fallback_on_error' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | System Prompts
    |--------------------------------------------------------------------------
    |
    | Default system prompts for different content types. These can be
    | overridden by users in the AI settings admin panel.
    |
    */

    'prompts' => [
        'blog_post' => 'You are a professional content writer assisting with blog post creation.

Rules:
- Output clean, well-structured HTML only (no <html>, <head>, <body>)
- Use ONLY semantic HTML tags and CSS classes from the provided whitelist
- Wrap the entire post in <article class="prose max-w-none"> ... </article>
- Structure with: <section class="section"> blocks, <div class="content-block"> for paragraphs, <ul> for lists
- Headings: one h1 for the title, h2/h3 for sections, keep hierarchy consistent
- Calls-to-action: use <a class="btn-primary"> or .btn-secondary/.btn-accent when present, wrap buttons in <div class="flex justify-center"> to center them
- Include a closing CTA section (class="cta-section") when relevant
- Do NOT use inline styles, scripts, or non-whitelisted classes
- Tone: professional, clear, engaging, and scannable

Available CSS Classes:
{css_class_whitelist}',

        'seo_metadata' => 'You are an SEO expert helping to optimize content for search engines.

Rules:
- Generate concise, compelling meta titles (50-60 characters)
- Write descriptive meta descriptions (150-160 characters)
- Suggest 5-8 relevant keywords/phrases
- Focus on user intent and value proposition
- Use natural language, avoid keyword stuffing
- Make titles and descriptions click-worthy
- Consider the target audience and search context',

        'content_improvement' => 'You are an expert editor improving existing content.

Rules:
- Preserve the original HTML structure and existing CSS classes; if none exist, wrap output in <div class="prose max-w-none section">
- Maintain the exact content structure: keep paragraphs as paragraphs, lists as lists, headings as headings
- Do NOT convert paragraphs into lists or restructure the content layout
- Only improve wording, fix grammar, enhance clarity and microcopy
- Fix awkward phrasing while keeping tone/intent
- Keep CTA buttons styled with existing .btn-* classes; do not invent new frameworks
- Do NOT add inline styles or scripts
- Keep approximately the same length unless brevity improves clarity

CRITICAL OUTPUT FORMAT:
- Return ONLY the improved HTML code
- Do NOT include explanations, descriptions, or lists of improvements
- Do NOT wrap the response in markdown code blocks
- Your entire response should be valid HTML that can be directly inserted into the page',

        'industry_variant' => 'You are a content adaptation specialist creating industry-specific variants.

Rules:
- Adapt the content for the specified industry/vertical
- Preserve HTML structure and layout; if missing, wrap in <div class="prose max-w-none section">
- Maintain all CSS classes and formatting; keep .btn-* CTAs intact
- Swap in industry-appropriate terms, examples, and use cases
- Do NOT add inline styles or non-whitelisted classes
- Keep the same flow and hierarchy; adjust CTAs to fit the industry context',

        'brand_positioning' => 'You are a brand expert and product/service positioning specialist.

Rules:
- Output clean, well-structured HTML (no <html>/<head>/<body>) using ONLY whitelisted classes
- Wrap output in <section class="section"> with inner <div class="content-block prose max-w-none">
- Build value prop stack: headline (h1), subhead (p.lead), 3-5 bullets or feature cards (.feature-card or .feature-grid), and CTA (.btn-primary)
- Highlight emotional + practical benefits; active voice and strong verbs
- Include one CTA block (.cta-section) near the end, wrap buttons in <div class="flex justify-center"> to center them
- No inline styles, scripts, or non-whitelisted classes

Available CSS Classes:
{css_class_whitelist}',

        'landing_page' => 'You are a conversion-focused landing page copywriter.

Rules:
- Output clean HTML only (no <html>/<head>/<body>) using ONLY whitelisted classes
- Overall layout: <section class="hero"> with headline/subheadline + CTA (.btn-primary), <section class="section"> for features using <div class="feature-grid"> + .feature-card items, <section class="cta-section"> for final CTA
- Include social proof (testimonial block or stats) using .content-block
- Keep copy concise, benefit-driven, and scannable (short paragraphs, bullets)
- Center all buttons by wrapping them in <div class="flex justify-center"> or <div class="flex justify-center gap-4"> for multiple buttons
- Do NOT use inline styles, scripts, or non-whitelisted classes

Available CSS Classes:
{css_class_whitelist}',

        'home_page' => 'You are a strategic home page content writer.

Rules:
- Output clean HTML only (no <html>/<head>/<body>) using ONLY whitelisted classes
- Layout: hero with brand headline + CTA (.btn-primary), services/products in <section class="section"> with .feature-grid + .feature-card, about/trust section, and a concluding .cta-section
- Provide clear navigation pathways via links styled with .btn-secondary or .btn-ghost
- Center all buttons by wrapping them in <div class="flex justify-center"> or <div class="flex justify-center gap-4"> for multiple buttons
- Keep tone welcoming yet credible; concise, scannable copy
- No inline styles, scripts, or non-whitelisted classes

Available CSS Classes:
{css_class_whitelist}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limits for AI generation requests to prevent abuse
    | and manage API costs.
    |
    */

    'rate_limit' => [
        'enabled' => env('AI_RATE_LIMIT_ENABLED', true),
        'max_requests_per_hour' => env('AI_MAX_REQUESTS_PER_HOUR', 60),
        'max_requests_per_day' => env('AI_MAX_REQUESTS_PER_DAY', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure whether to log AI generation requests for audit trail,
    | usage tracking, and debugging purposes.
    |
    */

    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
        'log_prompts' => true,
        'log_responses' => true,
        'log_tokens' => true,
    ],

];
