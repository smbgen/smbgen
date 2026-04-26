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
    <title>{{ config('app.name', 'smbgen') }} - Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite('resources/js/app.js')
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased text-base" style="--primary-color: {{ config('business.branding.primary_color', '#3B82F6') }}; --secondary-color: {{ config('business.branding.secondary_color', '#8B5CF6') }};">
    <x-demo-banner />
    <div class="flex min-h-screen flex-col lg:flex-row bg-white dark:bg-gray-950">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 border-r transform transition-transform duration-300 -translate-x-full lg:static lg:translate-x-0 lg:relative flex flex-col" style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <!-- Sidebar Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b" style="border-color: var(--border-color);">
                <x-navigation.brand :href="route('admin.dashboard')" subtitle="Admin Panel" />
                <button id="sidebar-close" aria-label="Close sidebar" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="sidebar-scroll flex-1 overflow-y-auto px-4 py-6">
                @php
                    $unreadMessagesCount = \App\Models\Message::where('is_read', false)->count();
                    $blogPostCount = 0;
                    $cmsImageCount = 0;

                    if (config('business.features.blog')) {
                        try {
                            $blogPostCount = \App\Models\BlogPost::count();
                        } catch (\Throwable $e) {
                            $blogPostCount = 0;
                        }
                    }

                    if (config('business.features.cms')) {
                        try {
                            $cmsImageCount = \App\Models\CmsImage::count();
                        } catch (\Throwable $e) {
                            $cmsImageCount = 0;
                        }
                    }
                @endphp

                <!-- Quick Actions (Always Visible) -->
                <div class="mb-6 rounded-xl border border-gray-200 dark:border-gray-700 p-3 surface-zone-primary">
                    <x-navigation.section-title label="Quick Actions" />
                    <div class="space-y-1">
                        @if(\Route::has('admin.search'))
                            <x-navigation.sidebar-link :href="route('admin.search')" :active="request()->routeIs('admin.search*')" icon="fas fa-magnifying-glass" label="Global Search" />
                        @endif
                        @if(\Route::has('clients.create'))
                            <x-navigation.sidebar-link :href="route('clients.create')" :active="request()->routeIs('clients.create')" icon="fas fa-user-plus" label="New Client" />
                        @endif
                    </div>
                </div>

                <!-- Main Section (Collapsible, Default Open) -->
                <x-navigation.collapsible-section id="nav_main" label="Main" :defaultOpen="true">
                    <x-navigation.sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="fas fa-home" label="Dashboard" />
                </x-navigation.collapsible-section>

                <!-- Business Section (Collapsible) -->
                <x-navigation.collapsible-section id="nav_business" label="Business">
                    <x-navigation.sidebar-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" icon="fas fa-users" label="Clients" :badge="\App\Models\Client::count()" />
                    @if(config('business.features.file_management'))
                        <x-navigation.sidebar-link :href="route('admin.clients.files.overview')" :active="request()->routeIs('admin.clients.files.overview')" icon="fas fa-folder" label="File Management" :badge="\App\Models\ClientFile::count()" badge-class="bg-cyan-600/20 text-cyan-400" />
                    @endif
                    <x-navigation.sidebar-link :href="route('admin.leads.index')" :active="request()->routeIs('admin.leads.*')" icon="fas fa-inbox" label="Leads" />
                    <x-navigation.sidebar-link :href="route('messages.index')" :active="request()->routeIs('messages.*')" icon="fas fa-comments" label="Messages" :badge="$unreadMessagesCount > 0 ? $unreadMessagesCount : null" badge-class="bg-red-600/20 text-red-400" />
                    <x-navigation.sidebar-link :href="route('admin.packages.index')" :active="request()->routeIs('admin.packages.*')" icon="fas fa-box-open" label="Client Presentations" />
                    @if(config('business.features.billing'))
                        <x-navigation.sidebar-link :href="route('admin.billing.index')" :active="request()->routeIs('admin.billing.*')" icon="fas fa-file-invoice-dollar" label="Payments" />
                    @endif
                    @if(\Route::has('admin.email.index') && config('business.features.email_composer'))
                        <x-navigation.sidebar-link :href="route('admin.email.index')" :active="request()->routeIs('admin.email.*')" icon="fas fa-envelope" label="Email Composer" />
                    @endif
                    @if(\Route::has('admin.email-logs.index'))
                        <x-navigation.sidebar-link :href="route('admin.email-logs.index')" :active="request()->routeIs('admin.email-logs.*')" icon="fas fa-chart-line" label="Email Logs" />
                    @endif
                    @if(config('business.features.inspection_reports') && \Route::has('admin.inspection-reports.index'))
                        <x-navigation.sidebar-link :href="route('admin.inspection-reports.index')" :active="request()->routeIs('admin.inspection-reports.*')" icon="fas fa-clipboard-check" label="Inspection Reports" />
                    @endif
                </x-navigation.collapsible-section>

                <!-- Content Management Section (Collapsible) -->
                @if(config('business.features.cms') || config('business.features.blog') || config('business.features.booking'))
                    <x-navigation.collapsible-section id="nav_content" label="Content Management">
                        
                        @if(config('business.features.booking'))
                            <x-navigation.collapsible-item label="Bookings" icon="fas fa-calendar-check">
                                <x-navigation.sidebar-link :href="route('admin.bookings.dashboard')" :active="request()->routeIs('admin.bookings.*') && !request()->routeIs('admin.booking-fields.*')" icon="fas fa-calendar-check" label="Dashboard" />
                                @if(\Route::has('admin.calendar.index'))
                                    <x-navigation.sidebar-link :href="route('admin.calendar.index')" :active="request()->routeIs('admin.calendar.*')" icon="fas fa-calendar-alt" label="Calendar Integration" />
                                @endif
                                @if(\Route::has('admin.availability.index'))
                                    <x-navigation.sidebar-link :href="route('admin.availability.index')" :active="request()->routeIs('admin.availability.*')" icon="fas fa-clock" label="Availability" />
                                @endif
                                @if(\Route::has('admin.booking-fields.edit'))
                                    <x-navigation.sidebar-link :href="route('admin.booking-fields.edit')" :active="request()->routeIs('admin.booking-fields.*')" icon="fas fa-list-check" label="Fields" />
                                @endif
                            </x-navigation.collapsible-item>
                        @endif
                        
                        @if(config('business.features.blog'))
                            <x-navigation.collapsible-item label="Blog" icon="fas fa-blog">
                                <x-navigation.sidebar-link :href="route('admin.blog.posts.index')" :active="request()->routeIs('admin.blog.posts.*')" icon="fas fa-blog" label="Posts" :badge="$blogPostCount > 0 ? $blogPostCount : null" badge-class="bg-purple-600/20 text-purple-400" />
                                <x-navigation.sidebar-link :href="route('admin.blog.categories.index')" :active="request()->routeIs('admin.blog.categories.*')" icon="fas fa-folder" label="Categories" />
                                <x-navigation.sidebar-link :href="route('admin.blog.tags.index')" :active="request()->routeIs('admin.blog.tags.*')" icon="fas fa-tags" label="Tags" />
                            </x-navigation.collapsible-item>
                        @endif
                        
                        @if(config('business.features.cms'))
                            <x-navigation.collapsible-item label="CMS" icon="fas fa-file-alt">
                                <x-navigation.sidebar-link :href="route('admin.cms.index')" :active="request()->routeIs('admin.cms.index') || request()->routeIs('admin.cms.show') || request()->routeIs('admin.cms.create') || request()->routeIs('admin.cms.edit')" icon="fas fa-file-alt" label="Editor" />
                                <x-navigation.sidebar-link :href="route('admin.cms.images.index')" :active="request()->routeIs('admin.cms.images.*')" icon="fas fa-images" label="Media Library" :badge="$cmsImageCount > 0 ? $cmsImageCount : null" badge-class="bg-green-600/20 text-green-400" />
                            </x-navigation.collapsible-item>
                        @endif
                        
                        @if(config('ai.enabled'))
                            <x-navigation.sidebar-link :href="route('admin.ai.settings.index')" :active="request()->routeIs('admin.ai.settings.*')" icon="fas fa-robot" label="AI Assistant" />
                        @endif
                    </x-navigation.collapsible-section>
                @endif

                <!-- Settings Section (Collapsible) -->
                @if(auth()->user()->isAdministrator())
                    <x-navigation.collapsible-section id="nav_settings" label="Settings">
                        @if(auth()->user()->isSuperAdmin() && \Route::has('super-admin.dashboard'))
                            <x-navigation.sidebar-link :href="route('super-admin.dashboard')" :active="request()->routeIs('super-admin.*')" icon="fas fa-server" label="Super Admin" />
                        @endif
                        <x-navigation.sidebar-link :href="route('admin.onboarding')" :active="request()->routeIs('admin.onboarding')" icon="fas fa-plug" label="Integrations" />
                        <x-navigation.sidebar-link :href="route('admin.business_settings.index')" :active="request()->routeIs('admin.business_settings.*')" icon="fas fa-building" label="Business Settings" />
                        <x-navigation.sidebar-link :href="route('admin.environment_settings.index')" :active="request()->routeIs('admin.environment_settings.*')" icon="fas fa-cog" label="System Settings" />
                        <x-navigation.sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="fas fa-users-cog" label="User Management" />
                        <x-navigation.sidebar-link :href="route('admin.activity-logs.index')" :active="request()->routeIs('admin.activity-logs.*')" icon="fas fa-history" label="Activity Logs" />
                    </x-navigation.collapsible-section>
                @endif

                <!-- Development Tools (Collapsible, Debug Mode Only) -->
                @if(config('app.debug') && auth()->user()->isAdministrator())
                    <x-navigation.collapsible-section id="nav_debug" label="Development">
                        <x-navigation.sidebar-link :href="route('debug.design')" :active="request()->routeIs('debug.design')" icon="fas fa-palette" label="Design Playground" />
                        <x-navigation.sidebar-link :href="route('debug.info')" :active="request()->routeIs('debug.info')" icon="fas fa-info-circle" label="Debug Info" />
                    </x-navigation.collapsible-section>
                @endif
            </nav>

            <!-- User Profile Footer (Mobile only) -->
            @auth
            <div class="border-t border-gray-200 dark:border-gray-700/50 p-4 lg:hidden">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ auth()->user()->email }}</div>
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
            <header class="sticky top-0 z-30 h-20 bg-white dark:bg-gray-800/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 flex items-center justify-between px-3 sm:px-6">
                <!-- Mobile hamburger (visible only on mobile) -->
                <button id="sidebar-toggle" aria-label="Open sidebar" class="lg:hidden text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded p-2 transition-colors flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <!-- Mobile branding (visible only on mobile) -->
                <div class="lg:hidden flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bridge text-white text-xs"></i>
                    </div>
                    <span class="text-gray-900 dark:text-white font-semibold text-sm">{{ collect(explode(' ', config('app.company_name', config('app.name'))))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(3)->implode('') }}</span>
                </div>

                <!-- Desktop quick links (visible only on desktop) -->
                <div class="hidden lg:flex items-center gap-2">
                    <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 dark:border-gray-600/50 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.leads.*') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                        <i class="fas fa-inbox"></i>
                        <span>Leads</span>
                    </a>
                    <a href="{{ route('admin.bookings.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 dark:border-gray-600/50 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.bookings.*') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                    @if(config('business.features.billing'))
                        <a href="{{ route('admin.billing.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 dark:border-gray-600/50 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.billing.*') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    @endif
                </div>
                
                <!-- Desktop actions (visible only on desktop) -->
                <div class="hidden lg:flex ml-auto items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <x-dark-mode-toggle class="text-xs !px-1.5 !py-1.5 bg-gray-100 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600/50 hover:bg-gray-200 dark:hover:bg-gray-600/50 text-gray-900 dark:!text-gray-300" />
                    
                    <!-- User Profile (Desktop) -->
                    @auth
                    <div class="flex items-center gap-3 px-4 py-2 bg-gray-100 dark:bg-gray-700/50 rounded-lg border border-gray-300 dark:border-gray-600/50">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center text-white font-semibold text-sm">
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
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 w-full">
                <div class="w-full">
                    @if (session('super_admin_impersonating'))
                        @php
                            $impersonation = session('super_admin_impersonating');
                            $impersonationTenantName = is_array($impersonation) ? ($impersonation['tenant_name'] ?? 'this tenant') : 'this tenant';
                        @endphp
                        <div class="mb-4 rounded-xl border border-amber-300/70 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-user-secret"></i>
                                    <span>You are impersonating {{ $impersonationTenantName }} as a tenant administrator.</span>
                                </div>
                                <form method="POST" action="{{ route('admin.stop-impersonating') }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700 transition-colors">
                                        <i class="fas fa-arrow-rotate-left"></i>
                                        Stop Impersonating
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

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
                document.body.style.overflow = 'hidden'; // Prevent body scroll when menu is open
            }
            
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = ''; // Restore body scroll
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
            
            // Close sidebar when clicking overlay
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
            
            // Close sidebar when clicking on a link
            if (sidebar) {
                sidebar.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', closeSidebar);
                });
            }
            
            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !overlay.classList.contains('hidden')) {
                    closeSidebar();
                }
            });
        });
    </script>

</body>
</html>
