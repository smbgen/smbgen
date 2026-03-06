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
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- View-specific Styles -->
    @stack('styles')
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-770N2CMS5K"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-770N2CMS5K');
    </script>
</head>
<body class="font-sans antialiased" style="--bg-color: {{ config('business.branding.background_color', '#ffffff') }}; --primary-color: {{ config('business.branding.primary_color', '#3B82F6') }}; --secondary-color: {{ config('business.branding.secondary_color', '#8B5CF6') }};">
    <div class="min-h-screen bg-white dark:bg-gray-950">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-100">
                                {{ config('app.company_name', 'smbgen') }}
                            </a>
                        </div>

                        <!-- Navigation Links (minimal for MVP) -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            @auth
                                <a href="{{ route('dashboard') }}" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-300 hover:text-white">
                                    Dashboard
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            <!-- Connected Services Dropdown -->
                            <div class="ml-3 relative" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" class="flex items-center text-gray-300 hover:text-white transition duration-150 ease-in-out">
                                    <i class="fas fa-plug mr-2"></i>
                                    <span class="text-sm">Services</span>
                                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                                     style="display: none;">
                                    <div class="p-4">
                                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                            Connected Services
                                        </div>
                                        
                                        <!-- Account Info -->
                                        <div class="mb-4">
                                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                                <span class="font-medium">Account:</span> {{ auth()->user()->email }}
                                            </div>
                                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                                <span class="font-medium">ID:</span> {{ auth()->user()->id }}
                                            </div>
                                        </div>

                                        <!-- Google Services Status -->
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center">
                                                    <i class="fab fa-google text-red-500 mr-2"></i>
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Google Services</span>
                                                </div>
                                                @if(auth()->user()->googleCredential)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Connected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                                        Not Connected
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if(auth()->user()->googleCredential)
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                                    Calendar and Drive access enabled for inspection reports and scheduling.
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                                    Connect your Google account to enable Calendar sync and Drive storage for inspection reports.
                                                </p>
                                            @endif

                                            <a href="{{ route('admin.calendar.connect') }}" 
                                               class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                                <i class="fas fa-link mr-2"></i>
                                                @if(auth()->user()->googleCredential)
                                                    Reconnect Services
                                                @else
                                                    Connect Services
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-3 relative">
                                <div class="flex items-center space-x-4">
                                    <span class="text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</span>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn-danger text-sm">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary text-sm">
                                Login
                            </a>
                        @endauth
                        
                        <!-- Dark Mode Toggle (Right-aligned) -->
                        <x-dark-mode-toggle class="!ml-auto text-sm px-2 py-1.5 dark:hover:bg-gray-700" />
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    @auth
                        @if(auth()->user() && auth()->user()->isAdministrator())
                            <!-- Admin Mobile Navigation -->
                            <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-tachometer-alt mr-3"></i>Admin Dashboard
                            </a>
                            <a href="{{ route('clients.index') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-users mr-3"></i>Manage Clients
                            </a>
                            <a href="{{ route('admin.google-oauth') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-share-alt mr-3"></i>Social Accounts
                            </a>
                            <a href="{{ route('admin.test') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-cog mr-3"></i>System Test
                            </a>
                            @if(auth()->check() && auth()->user()->isAdministrator())
                                <a href="{{ route('admin.game') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                    <i class="fas fa-gamepad mr-3"></i>Admin Game
                                </a>
                            @endif
                        @else
                            <!-- Client Mobile Navigation -->
                            <a href="{{ route('dashboard') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                            </a>
                            <a href="{{ route('messages.index') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-envelope mr-3"></i>Messages
                            </a>
                            <a href="{{ route('cyber-audit.index') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-shield-alt mr-3"></i>Cyber Audit
                            </a>
                            <a href="{{ route('client.seo-assistant') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-search mr-3"></i>SEO Assistant
                            </a>
                            @if(
                                (Route::has('cyber-audit-demo') )
                                && (bool) data_get(config('business'), 'features.cyber_audit_demo', false)
                            )
                                <a href="{{ route('cyber-audit-demo') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                                    <i class="fas fa-play mr-3"></i>Cyber Audit Demo
                                </a>
                            @endif
                        @endif
                        
                        <!-- User Profile & Logout -->
                        <div class="pt-4 pb-3 border-t border-gray-700">
                            <div class="flex items-center px-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                                        <span class="text-white font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-base font-medium text-white">{{ auth()->user()->name }}</div>
                                    <div class="text-sm font-medium text-gray-400">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            <div class="mt-3 space-y-1">
                                <a href="{{ route('profile.edit') }}" class="mobile-nav-link block px-4 py-2 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 transition duration-150 ease-in-out">
                                    <i class="fas fa-user-edit mr-3"></i>Profile Settings
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 transition duration-150 ease-in-out">
                                        <i class="fas fa-sign-out-alt mr-3"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="mobile-nav-link block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                            <i class="fas fa-sign-in-alt mr-3"></i>Login
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Theme Notification -->
    <x-theme-notification />

    @livewireScripts
    
    <!-- View-specific Scripts -->
    @stack('scripts')
    
    <!-- Mobile Navigation JavaScript -->
    <script>
        // Mobile navigation functionality
        function initMobileNav() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (!mobileMenuButton || !mobileMenu) {
                console.warn('Mobile navigation elements not found');
                return;
            }
            
            // Remove any existing event listeners
            const newButton = mobileMenuButton.cloneNode(true);
            mobileMenuButton.parentNode.replaceChild(newButton, mobileMenuButton);
            
            // Add click event listener
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Mobile menu button clicked');
                
                // Toggle mobile menu visibility
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.remove('hidden');
                    // Change hamburger to X
                    newButton.innerHTML = `
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    `;
                } else {
                    mobileMenu.classList.add('hidden');
                    // Change X back to hamburger
                    newButton.innerHTML = `
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    `;
                }
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!newButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    // Reset hamburger icon
                    newButton.innerHTML = `
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    `;
                }
            });
            
            // Close mobile menu when window is resized to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 640) { // sm breakpoint
                    mobileMenu.classList.add('hidden');
                    // Reset hamburger icon
                    newButton.innerHTML = `
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    `;
                }
            });
            
            console.log('Mobile navigation initialized');
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMobileNav);
        } else {
            initMobileNav();
        }
        
        // Also initialize after a short delay to catch any late-loading elements
        setTimeout(initMobileNav, 100);
    </script>
</body>
</html>
