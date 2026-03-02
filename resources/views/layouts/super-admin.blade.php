<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'smbgen') }} - Super Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased" style="--primary-color: #EF4444; --secondary-color: #DC2626;">
    <div class="flex min-h-screen flex-col lg:flex-row bg-white dark:bg-gray-950">
        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 border-r transform transition-transform duration-300 -translate-x-full lg:static lg:translate-x-0 lg:relative flex flex-col" style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center justify-between px-6 border-b" style="border-color: var(--border-color);">
                <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-200">
                        <i class="fas fa-crown text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-900 dark:text-white font-bold text-lg leading-tight">SUPER ADMIN</span>
                        <span class="text-gray-600 dark:text-gray-400 text-xs">Platform Control</span>
                    </div>
                </a>
                <button id="sidebar-close" class="lg:hidden text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-6">
                <!-- Main Section -->
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Overview</div>
                    <a href="{{ route('super-admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt text-lg"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('super-admin.tenants.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.tenants.*') ? 'active' : '' }}">
                        <i class="fas fa-building text-lg"></i>
                        <span>All Tenants</span>
                        <span class="ml-auto bg-red-600/20 text-red-400 text-xs font-semibold px-2 py-0.5 rounded-full">{{ \App\Models\Tenant::count() }}</span>
                    </a>
                </div>

                <!-- Platform Management -->
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Platform</div>
                    <a href="{{ route('super-admin.dashboard') }}#revenue" class="sidebar-link">
                        <i class="fas fa-chart-line text-lg"></i>
                        <span>Revenue Analytics</span>
                    </a>
                    <a href="{{ route('super-admin.dashboard') }}#trials" class="sidebar-link">
                        <i class="fas fa-clock text-lg"></i>
                        <span>Trial Management</span>
                        @php
                            $expiringTrials = \App\Models\Tenant::whereNotNull('trial_ends_at')
                                ->where('trial_ends_at', '>', now())
                                ->where('trial_ends_at', '<=', now()->addDays(7))
                                ->count();
                        @endphp
                        @if($expiringTrials > 0)
                            <span class="ml-auto bg-yellow-600/20 text-yellow-400 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $expiringTrials }}</span>
                        @endif
                    </a>
                    <a href="{{ route('super-admin.dashboard') }}#subscriptions" class="sidebar-link">
                        <i class="fas fa-credit-card text-lg"></i>
                        <span>Subscriptions</span>
                    </a>
                </div>

                <!-- System Tools -->
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">System</div>
                    @if(\Illuminate\Support\Facades\Route::has('super-admin.users.index'))
                        <a href="{{ route('super-admin.users.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-crown text-lg"></i>
                            <span>Super Admins</span>
                            <span class="ml-auto bg-red-600/20 text-red-400 text-xs font-semibold px-2 py-0.5 rounded-full">{{ \App\Models\User::where('is_super_admin', true)->count() }}</span>
                        </a>
                    @endif
                    <a href="{{ config('services.stripe.dashboard_url', 'https://dashboard.stripe.com') }}" target="_blank" class="sidebar-link">
                        <i class="fab fa-stripe text-lg"></i>
                        <span>Stripe Dashboard</span>
                        <i class="fas fa-external-link-alt text-xs ml-auto text-gray-500"></i>
                    </a>
                    <a href="{{ route('super-admin.dashboard') }}#health" class="sidebar-link">
                        <i class="fas fa-heartbeat text-lg"></i>
                        <span>Platform Health</span>
                    </a>
                </div>

                <!-- Quick Stats Widget -->
                <div class="mb-6">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Quick Stats</div>
                    <div class="bg-gray-100 dark:bg-gray-800/50 rounded-lg p-4 space-y-3">
                        @php
                            $activeTrials = \App\Models\Tenant::whereNotNull('trial_ends_at')
                                ->where('trial_ends_at', '>', now())
                                ->count();
                            $payingCustomers = \App\Models\Tenant::whereNotNull('stripe_subscription_id')
                                ->count();
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400 text-sm">Active Trials</span>
                            <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $activeTrials }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400 text-sm">Paying</span>
                            <span class="text-green-600 dark:text-green-400 font-semibold">{{ $payingCustomers }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400 text-sm">Total</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ \App\Models\Tenant::count() }}</span>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- User Profile Footer (Mobile only) -->
            @auth
            <div class="border-t border-gray-700/50 p-4 lg:hidden">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-red-600 dark:text-red-400 truncate">Super Administrator</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/50 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
            @endauth
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col w-full overflow-hidden">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 h-16 bg-white dark:bg-gray-800/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 flex items-center justify-between px-3 sm:px-6">
                <!-- Mobile hamburger (visible only on mobile) -->
                <button id="sidebar-toggle" class="lg:hidden text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded p-2 transition-colors flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <!-- Mobile branding (visible only on mobile) -->
                <div class="lg:hidden flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-crown text-white text-xs"></i>
                    </div>
                    <span class="text-gray-900 dark:text-white font-semibold text-sm">SUPER ADMIN</span>
                </div>
                
                <!-- Desktop breadcrumb or title (visible only on desktop) -->
                <div class="hidden lg:flex items-center gap-2 text-sm">
                    @if(isset($breadcrumbs))
                        @foreach($breadcrumbs as $index => $crumb)
                            @if($index > 0)
                                <i class="fas fa-chevron-right text-gray-400 dark:text-gray-600 text-xs"></i>
                            @endif
                            @if(isset($crumb['url']))
                                <a href="{{ $crumb['url'] }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ $crumb['label'] }}</a>
                            @else
                                <span class="text-gray-900 dark:text-white font-medium">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                    @endif
                </div>
                
                <!-- Desktop actions (visible only on desktop) -->
                <div class="hidden lg:flex ml-auto items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <x-dark-mode-toggle class="text-xs !px-1.5 !py-1.5 bg-gray-100 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600/50 hover:bg-gray-200 dark:hover:bg-gray-600/50 text-gray-900 dark:!text-gray-300" />
                    
                    <!-- Super Admin Badge -->
                    <div class="px-3 py-1.5 bg-red-100 dark:bg-red-600/20 text-red-700 dark:text-red-400 rounded-lg text-xs font-bold border border-red-300 dark:border-red-600/30 flex items-center gap-2">
                        <i class="fas fa-crown"></i>
                        <span>SUPER ADMIN</span>
                    </div>
                    
                    <!-- User Profile (Desktop) -->
                    @auth
                    <div class="flex items-center gap-3 px-4 py-2 bg-gray-100 dark:bg-gray-700/50 rounded-lg border border-gray-300 dark:border-gray-600/50">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[150px]">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 truncate max-w-[150px]">{{ auth()->user()->email }}</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="ml-2">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-600/50 rounded transition-colors">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 w-full bg-white dark:bg-gray-900">
                <div class="w-full">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Sidebar Overlay (mobile only) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden hidden"></div>
    </div>

    <!-- Theme Notification -->
    <x-theme-notification />

    @livewireScripts
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('sidebar-toggle');
            const closeBtn = document.getElementById('sidebar-close');
            
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    openSidebar();
                });
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    closeSidebar();
                });
            }
            
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
            
            if (sidebar) {
                sidebar.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', closeSidebar);
                });
            }
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !overlay.classList.contains('hidden')) {
                    closeSidebar();
                }
            });
        });
    </script>

    <style>
        .sidebar-link {
            @apply flex items-center gap-3 px-3 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/50 rounded-lg transition-all duration-200 group;
        }
        
        .sidebar-link.active {
            @apply bg-red-100 dark:bg-red-600/20 text-red-700 dark:text-red-400 font-medium;
        }
        
        .sidebar-link:hover i {
            @apply transform scale-110 transition-transform duration-200;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        #sidebar nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
        }
        
        #sidebar nav::-webkit-scrollbar {
            width: 6px;
        }
        
        #sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }
        
        #sidebar nav::-webkit-scrollbar-thumb {
            background-color: rgba(75, 85, 99, 0.5);
            border-radius: 3px;
        }
        
        #sidebar nav::-webkit-scrollbar-thumb:hover {
            background-color: rgba(75, 85, 99, 0.7);
        }
    </style>
</body>
</html>
