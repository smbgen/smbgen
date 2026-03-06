<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'smbgen') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased" style="--primary-color: {{ config('business.branding.primary_color', '#3B82F6') }}; --secondary-color: {{ config('business.branding.secondary_color', '#8B5CF6') }};">
    <div class="min-h-screen bg-white dark:bg-gray-950">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ config('app.company_name', 'smbgen') }}</a>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            @auth
                                <a href="{{ route('dashboard') }}" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
                            @endauth
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Dark Mode Toggle (Right-aligned) -->
                        <x-dark-mode-toggle class="!ml-auto text-sm px-2 py-1.5" />
                        
                        @auth
                            <div class="ml-3 relative">
                                <div class="flex items-center space-x-4">
                                    <span class="text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</span>
                                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn-danger text-sm">Logout</button></form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary text-sm">Login</a>
                        @endauth
                    </div>

                    <div class="-mr-2 flex items-center sm:hidden">
                        <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
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
                        <a href="{{ route('dashboard') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">Dashboard</a>
                        <a href="{{ route('messages.index') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">Messages</a>
                    @else
                        <a href="{{ route('login') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">Login</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
                @if (session('error'))<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>@endif
                
                @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
                    <!-- Verification Required Overlay -->
                    <div class="fixed inset-0 bg-gray-900/95 backdrop-blur-md z-50 flex items-center justify-center">
                        <div class="max-w-md mx-4 bg-gray-800 border-2 border-yellow-500 rounded-xl shadow-2xl p-8 text-center">
                            <div class="w-20 h-20 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-envelope-open-text text-yellow-400 text-4xl"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-white mb-4">Verify Your Account</h2>
                            <p class="text-gray-300 mb-6 leading-relaxed">
                                You must verify your email address to access the client portal. Check your inbox for a verification email.
                            </p>
                            <div class="space-y-3">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 bg-yellow-600 hover:bg-yellow-500 text-white font-semibold rounded-lg transition-colors shadow-lg">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Resend Verification Email
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-gray-300 font-medium rounded-lg transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                            <p class="text-gray-400 text-sm mt-6">
                                Didn't receive an email? Check your spam folder or contact support.
                            </p>
                        </div>
                    </div>
                    <!-- Blurred Content Behind -->
                    <div class="filter blur-lg pointer-events-none">
                        @yield('content')
                    </div>
                @else
                    @yield('content')
                @endif
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
