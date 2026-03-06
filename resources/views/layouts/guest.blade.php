<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'smbgen') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts: let laravel-vite-plugin auto-detect dev server or built manifest -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-770N2CMS5K"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-770N2CMS5K');
    </script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-950 flex items-center justify-center">
        @if (isset($slot) && ! (method_exists($slot, 'isEmpty') && $slot->isEmpty()))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </div>
    
    <!-- Theme Notification -->
    <x-theme-notification />
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
