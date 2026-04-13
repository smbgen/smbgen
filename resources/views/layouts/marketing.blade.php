<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme-mode="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'smbgen'))</title>
    <meta name="description" content="@yield('description', 'Build fast. Deliver beautifully. Grow aggressively.')">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('title', config('app.name', 'smbgen'))">
    <meta property="og:description" content="@yield('description', 'Build fast. Deliver beautifully. Grow aggressively.')">
    <meta property="og:type" content="website">

    {{-- Inter — clean, Helvetica-adjacent, built for screens --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Vite build — no CMS color injection here --}}
    @vite('resources/js/app.js')

    <style>
        /* Marketing layout uses Inter throughout — no CMS overrides */
        .marketing-root,
        .marketing-root * {
            font-family: 'Inter', 'Helvetica Neue', Arial, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    @stack('head')
</head>
<body class="marketing-root bg-white">

    @include('marketing.partials.nav')

    <main>
        @yield('content')
    </main>

    @include('marketing.partials.footer')

    @livewireScripts
    @stack('scripts')

</body>
</html>
