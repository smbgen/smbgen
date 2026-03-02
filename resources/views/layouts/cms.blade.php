<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $companyColors = \App\Models\CmsCompanyColors::getSettings();
        $navbarSettings = \App\Models\CmsNavbarSetting::getSettings();
        
        // Get page title or use default
        $pageTitle = $title ?? (isset($page) ? $page->title : config('app.name', 'smbgen'));
        
        // Generate SEO title using template
        $seoTitle = $companyColors->seo_title_template 
            ? str_replace('{page_title}', $pageTitle, $companyColors->seo_title_template)
            : $pageTitle;
    @endphp
    
    <title>{{ $seoTitle }}</title>
    
    {{-- SEO Meta Tags --}}
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
    @if($companyColors->seo_meta_description)
        <meta property="og:description" content="{{ $companyColors->seo_meta_description }}">
    @endif
    @if($companyColors->og_type)
        <meta property="og:type" content="{{ $companyColors->og_type }}">
    @endif
    @if($companyColors->og_image_url)
        <meta property="og:image" content="{{ $companyColors->og_image_url }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    
    {{-- Twitter Card Tags --}}
    @if($companyColors->twitter_card_type)
        <meta name="twitter:card" content="{{ $companyColors->twitter_card_type }}">
    @endif
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
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Theme CSS (Auto-injected from Company Colors) - AFTER Tailwind for proper priority -->
    @php
        // Company colors already loaded at top
    @endphp
    @if($companyColors->auto_inject_css)
        {!! $companyColors->generateCSS() !!}
    @endif
    
    {{-- Custom CSS from Header Concerns --}}
    @if($companyColors->custom_css)
        <style>
            /* Custom CSS from CMS Settings */
            {!! $companyColors->sanitizedCustomCss() !!}
        </style>
    @endif

    <!-- Alpine.js is loaded via Vite in app.js -->

    <!-- Custom Head Content -->
    @stack('head')
    
    {{-- Custom Head Scripts from Header Concerns --}}
    @if($companyColors->custom_head_scripts)
        {!! $companyColors->sanitizedCustomHeadScripts() !!}
    @endif
    
    <!-- Page Specific Head Content -->
    @if(isset($page) && $page->head_content)
        {!! $page->sanitizedHeadContent() !!}
    @endif
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-770N2CMS5K"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-770N2CMS5K');
    </script>
</head>
<body class="m-0 p-0">
    @if(isset($page) && $page->show_navbar)
        <x-public-navbar />
    @endif
    
    @yield('content')

    @php
        $companyName = config('business.company_name', config('app.name', 'smbgen'));
        $companyWebsite = config('business.contact.website');
        $companyHost = $companyWebsite ? parse_url($companyWebsite, PHP_URL_HOST) : null;
    @endphp

    @if(!isset($page) || ($page->show_footer ?? true))
        <x-public-footer />
    @endif
    
    @stack('scripts')
    
    {{-- Footer Scripts (run after DOM is loaded) --}}
    @if(isset($page) && $page->footer_scripts)
        {!! $page->sanitizedFooterScripts() !!}
    @endif
</body>
</html>
