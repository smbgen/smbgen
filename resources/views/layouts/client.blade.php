<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <!-- Prevent flash of unstyled content for dark mode users -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme-preference');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'smbgen') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite('resources/js/app.js')
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased text-base" style="--primary-color: {{ config('business.branding.primary_color', '#3B82F6') }}; --secondary-color: {{ config('business.branding.secondary_color', '#8B5CF6') }};">
    <div class="min-h-screen bg-white dark:bg-gray-950">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="page-shell">
                <div class="flex justify-between h-20">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <x-navigation.brand :href="route('dashboard')" />
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            @auth
                                <x-navigation.top-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" label="Dashboard" />
                            @endauth
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Dark Mode Toggle (Right-aligned) -->
                        <x-dark-mode-toggle class="!ml-auto text-sm px-2 py-1.5" />
                        
                        @auth
                            <div class="ml-3 relative">
                                <div class="flex items-center gap-4">
                                    <span class="text-gray-900 dark:text-gray-100 text-base font-medium">{{ auth()->user()->name }}</span>
                                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn-danger">Logout</button></form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">Login</a>
                        @endauth
                    </div>

                    <div class="-mr-2 flex items-center sm:hidden">
                        <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="mobile-menu" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    @auth
                        <a href="{{ route('dashboard') }}" class="mobile-nav-link">Dashboard</a>
                        <a href="{{ route('messages.index') }}" class="mobile-nav-link">Messages</a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="mobile-nav-link mobile-nav-link-danger w-full text-left">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="mobile-nav-link">Login</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="py-12">
            <div class="page-shell">
                <x-flash-messages />
                
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Theme Notification -->
    <x-theme-notification />

    @livewireScripts
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');
            if (btn && menu) { btn.addEventListener('click', function() { menu.classList.toggle('hidden'); }); }
        });
    </script>
</body>
</html>
