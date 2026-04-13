<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme-mode="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('business.company_name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts & Styles -->
    @vite('resources/js/app.js')
    @livewireStyles

    {{-- Company Colors Auto-Injection --}}
    @php
        $companyColors = \App\Models\CmsCompanyColors::getSettings();
    @endphp
    @if($companyColors->auto_inject_css)
        <style>
            :root {
                --color-primary: {{ $companyColors->primary_color }};
                --color-secondary: {{ $companyColors->secondary_color }};
                --color-background: {{ $companyColors->background_color }};
                --color-text: {{ $companyColors->text_color }};
                --color-accent: {{ $companyColors->accent_color }};
            }
            
            /* Body Styling */
            body {
                background-color: {{ $companyColors->body_background_color ?? '#ffffff' }};
                color: {{ $companyColors->text_color }};
            }
            
            /* Override ALL text color classes to use theme colors */
            .text-white,
            .text-gray-100,
            .text-gray-200,
            .text-gray-300,
            .text-gray-400,
            .text-gray-500,
            .text-gray-600,
            .text-gray-700,
            .text-gray-800,
            .text-gray-900 {
                color: {{ $companyColors->text_color }} !important;
            }
            
            /* Dark mode text overrides */
            .dark\:text-white,
            .dark\:text-gray-100,
            .dark\:text-gray-200,
            .dark\:text-gray-300,
            .dark\:text-gray-400 {
                color: {{ $companyColors->text_color }} !important;
            }
            
            /* Default text colors for readability */
            h1, h2, h3, h4, h5, h6, p, span, div, label, a {
                color: {{ $companyColors->text_color }};
            }
            
            /* Form elements - ensure readability */
            input:not([type="checkbox"]):not([type="radio"]), 
            textarea, 
            select {
                background-color: #ffffff !important;
                color: #1f2937 !important;
                border-color: #d1d5db !important;
            }
            
            input::placeholder,
            textarea::placeholder {
                color: #9ca3af !important;
            }
            
            /* Booking wizard and card elements */
            .bg-gray-800,
            .bg-gray-700,
            .bg-gray-900 {
                background-color: rgba(255, 255, 255, 0.05) !important;
                border: 1px solid rgba(0, 0, 0, 0.1) !important;
            }
            
            /* Override background opacity classes */
            .bg-gray-700\/50,
            .bg-gray-800\/50,
            .bg-gray-900\/50 {
                background-color: rgba(255, 255, 255, 0.03) !important;
                border: 1px solid rgba(0, 0, 0, 0.08) !important;
            }
            
            /* Keep fixed bottom bar visible */
            #stickySubmit {
                background-color: {{ $companyColors->background_color }} !important;
            }
            
            #stickySubmit * {
                color: {{ $companyColors->getNavbarTextColor() }} !important;
            }
            
            /* Error and success messages keep their colors */
            .text-red-500,
            .text-red-600,
            .text-red-700,
            .text-green-500,
            .text-green-600,
            .text-green-700,
            .text-blue-500,
            .text-blue-600,
            .text-blue-700,
            .text-yellow-300,
            .text-yellow-400,
            .text-yellow-500 {
                color: inherit !important;
            }
            
            /* Navbar Button Styles - Must come before utility classes */
            .btn-accent {
                display: inline-block;
                background-color: {{ $companyColors->accent_color }} !important;
                color: #ffffff !important;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-accent:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                opacity: 0.9;
                transform: translateY(-1px);
            }
            
            .btn-primary {
                display: inline-block;
                background-color: {{ $companyColors->primary_color }} !important;
                color: #ffffff !important;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-primary:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                opacity: 0.9;
                transform: translateY(-1px);
            }
            
            .btn-secondary {
                display: inline-block;
                background-color: {{ $companyColors->secondary_color }} !important;
                color: #ffffff !important;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-secondary:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                opacity: 0.9;
                transform: translateY(-1px);
            }
            
            .btn-success {
                display: inline-block;
                background-color: #10B981 !important;
                color: #ffffff !important;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-success:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                opacity: 0.9;
                transform: translateY(-1px);
            }
            
            /* Apply colors to common elements */
            .bg-primary { background-color: var(--color-primary) !important; }
            .text-primary { color: var(--color-primary) !important; }
            .border-primary { border-color: var(--color-primary) !important; }
            .bg-secondary { background-color: var(--color-secondary) !important; }
            .text-secondary { color: var(--color-secondary) !important; }
            .bg-accent { background-color: var(--color-accent) !important; }
            .text-accent { color: var(--color-accent) !important; }
        </style>
    @endif

    {{-- Additional Head Content --}}
    @stack('head')
</head>
<body class="font-sans antialiased">
    {{-- Public Navigation Bar --}}
    <x-public-navbar />

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Public Footer --}}
    <div class="pb-32">
        <x-public-footer />
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
