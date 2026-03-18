<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'smbgen — Build fast. Deliver beautifully. Grow aggressively.')</title>
    <meta name="description" content="@yield('description', 'smbgen is the next-generation AI-powered platform for small & mid-market businesses. Rapid application development, web design, social media automation, email marketing, lead generation and management — all in one place.')">

    {{-- Open Graph --}}
    <meta property="og:title"       content="@yield('title', 'smbgen — AI-Powered Growth Platform')">
    <meta property="og:description" content="@yield('description', 'Build fast. Deliver beautifully. Grow aggressively.')">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url()->current() }}">

    {{-- Inter — the voice of smbgen --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Vite assets (no CMS color injection on the public frontend) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Frontend Layout Base ─────────────────────────────────── */
        html { scroll-behavior: smooth; }
        .fe-root,
        .fe-root * {
            font-family: 'Inter', 'Helvetica Neue', Arial, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    @stack('head')
</head>
<body class="fe-root bg-white">

    {{-- ── Navigation ──────────────────────────────────────────────── --}}
    @include('frontend.partials.nav')

    {{-- ── Page Content ─────────────────────────────────────────────── --}}
    <main>
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────── --}}
    @include('frontend.partials.footer')

    @livewireScripts
    @stack('scripts')

</body>
</html>
