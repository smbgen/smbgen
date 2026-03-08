<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'smbgen') }} - Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased" style="--primary-color: {{ config('business.branding.primary_color', '#3B82F6') }}; --secondary-color: {{ config('business.branding.secondary_color', '#8B5CF6') }};">
    <div class="flex min-h-screen flex-col lg:flex-row bg-white dark:bg-gray-950">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 border-r transform transition-transform duration-300 -translate-x-full lg:static lg:translate-x-0 lg:relative flex flex-col" style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center justify-between px-6 border-b" style="border-color: var(--border-color);">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-200">
                        <i class="fas fa-bridge text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-900 dark:text-white font-bold text-lg leading-tight truncate max-w-40">{{ config('app.company_name', 'smbgen') }}</span>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Admin Panel</span>
                    </div>
                </a>
                <button id="sidebar-close" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-6">
                <!-- Main Section -->
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-500 uppercase tracking-wider mb-3 px-3">Main</div>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home text-lg"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('clients.index') }}" class="sidebar-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <i class="fas fa-users text-lg"></i>
                        <span>Clients</span>
                        <span class="ml-auto bg-primary-600/20 text-primary-400 text-xs font-semibold px-2 py-0.5 rounded-full">{{ \App\Models\Client::count() }}</span>
                    </a>
                    @if(config('business.features.file_management'))
                        <a href="{{ route('admin.clients.files.overview') }}" class="sidebar-link {{ request()->routeIs('admin.clients.files.overview') ? 'active' : '' }}">
                            <i class="fas fa-folder text-lg"></i>
                            <span>File Management</span>
                            <span class="ml-auto bg-cyan-600/20 text-cyan-400 text-xs font-semibold px-2 py-0.5 rounded-full">{{ \App\Models\ClientFile::count() }}</span>
                        </a>
                    @endif
                    <a href="{{ route('admin.leads.index') }}" class="sidebar-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">
                        <i class="fas fa-inbox text-lg"></i>
                        <span>Leads</span>
                    </a>
                    <a href="{{ route('messages.index') }}" class="sidebar-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                        <i class="fas fa-comments text-lg"></i>
                        <span>Messages</span>
                        @if(\App\Models\Message::where('is_read', false)->count() > 0)
                            <span class="ml-auto bg-red-600/20 text-red-400 text-xs font-semibold px-2 py-0.5 rounded-full">{{ \App\Models\Message::where('is_read', false)->count() }}</span>
                        @endif
                    </a>
                </div>

                <!-- Business Section -->
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-500 uppercase tracking-wider mb-3 px-3">Business</div>
                    <a href="{{ route('clients.import.index') }}" class="sidebar-link {{ request()->routeIs('clients.import.*') ? 'active' : '' }}">
                        <i class="fas fa-file-upload text-lg"></i>
                        <span>Import Clients</span>
                    </a>
                    @if(config('business.features.billing'))
                    <a href="{{ route('admin.billing.index') }}" class="sidebar-link {{ request()->routeIs('admin.billing.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar text-lg"></i>
                        <span>Billing & Invoices</span>
                    </a>
                    @endif
                    @if(\Route::has('admin.email.index') && config('business.features.email_composer'))
                        <a href="{{ route('admin.email.index') }}" class="sidebar-link {{ request()->routeIs('admin.email.*') ? 'active' : '' }}">
                            <i class="fas fa-envelope text-lg"></i>
                            <span>Email Composer</span>
                        </a>
                    @endif
                    @if(\Route::has('admin.email-logs.index'))
                        <a href="{{ route('admin.email-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line text-lg"></i>
                            <span>Email Logs</span>
                        </a>
                    @endif
                    @if(config('business.features.inspection_reports') && \Route::has('admin.inspection-reports.index'))
                        <a href="{{ route('admin.inspection-reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.inspection-reports.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check text-lg"></i>
                            <span>Inspection Reports</span>
                        </a>
                    @endif
                    <!-- @if(\Route::has('admin.clients.files.overview'))
                        <a href="{{ route('admin.clients.files.overview') }}" class="sidebar-link {{ request()->routeIs('admin.clients.files.overview') ? 'active' : '' }}">
                            <i class="fas fa-folder-open text-lg"></i>
                            <span>Client Files</span>
                        </a>
                    @endif -->
                </div>

                <!-- Clean Slate Section -->
                @if(\Route::has('cleanslate.billing.plans'))
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-500 uppercase tracking-wider mb-3 px-3">Clean Slate — Admin</div>
                    <a href="{{ route('admin.cleanslate.index') }}" class="sidebar-link {{ request()->routeIs('admin.cleanslate.*') ? 'active' : '' }}">
                        <i class="fas fa-users text-lg"></i>
                        <span>Customers</span>
                    </a>
                    <a href="{{ route('admin.cleanslate.brokers') }}" class="sidebar-link {{ request()->routeIs('admin.cleanslate.brokers*') ? 'active' : '' }}">
                        <i class="fas fa-database text-lg"></i>
                        <span>Data Brokers</span>
                    </a>
                    <a href="{{ route('admin.cleanslate.debug') }}" class="sidebar-link {{ request()->routeIs('admin.cleanslate.debug') ? 'active' : '' }}">
                        <i class="fas fa-bug text-lg"></i>
                        <span>Debug</span>
                    </a>
                </div>
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-500 uppercase tracking-wider mb-3 px-3">Clean Slate — My Account</div>
                    <a href="{{ route('cleanslate.entry') }}" class="sidebar-link {{ request()->routeIs('cleanslate.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-halved text-lg"></i>
                        <span>My Dashboard</span>
                    </a>
                    <a href="{{ route('cleanslate.billing.plans') }}" class="sidebar-link {{ request()->routeIs('cleanslate.billing.*') ? 'active' : '' }}">
                        <i class="fas fa-credit-card text-lg"></i>
                        <span>Plans & Billing</span>
                    </a>
                </div>
                @endif

                <!-- Content Management Section -->
                @if(config('business.features.cms') || config('business.features.blog') || config('business.features.booking'))
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Content Management</div>
                    
                    @if(config('business.features.booking'))
                        <a href="{{ route('admin.bookings.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.bookings.*') && !request()->routeIs('admin.booking-fields.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check text-lg"></i>
                            <span>Bookings</span>
                        </a>
                        @if(\Route::has('admin.calendar.index'))
                            <a href="{{ route('admin.calendar.index') }}" class="sidebar-link {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt text-lg ml-4 text-sm"></i>
                                <span class="text-sm">Calendar Integration</span>
                            </a>
                        @endif
                        @if(\Route::has('admin.availability.index'))
                            <a href="{{ route('admin.availability.index') }}" class="sidebar-link {{ request()->routeIs('admin.availability.*') ? 'active' : '' }}">
                                <i class="fas fa-clock text-lg ml-4 text-sm"></i>
                                <span class="text-sm">Availability</span>
                            </a>
                        @endif
                        @if(\Route::has('admin.booking-fields.edit'))
                            <a href="{{ route('admin.booking-fields.edit') }}" class="sidebar-link {{ request()->routeIs('admin.booking-fields.*') ? 'active' : '' }}">
                                <i class="fas fa-list-check text-lg ml-4 text-sm"></i>
                                <span class="text-sm">Booking Fields</span>
                            </a>
                        @endif
                    @endif
                    
                    @if(config('business.features.blog'))
                        <a href="{{ route('admin.blog.posts.index') }}" class="sidebar-link {{ request()->routeIs('admin.blog.posts.*') ? 'active' : '' }}">
                            <i class="fas fa-blog text-lg"></i>
                            <span>Blog Posts</span>
                            @php
                                try {
                                    $postCount = \App\Models\BlogPost::count();
                                    if ($postCount > 0) {
                                        echo '<span class="ml-auto bg-purple-600/20 text-purple-400 text-xs font-semibold px-2 py-0.5 rounded-full">' . $postCount . '</span>';
                                    }
                                } catch (\Exception $e) {
                                    // Silently fail if table doesn't exist yet
                                }
                            @endphp
                        </a>
                        <a href="{{ route('admin.blog.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.blog.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-folder text-lg ml-4 text-sm"></i>
                            <span class="text-sm">Categories</span>
                        </a>
                        <a href="{{ route('admin.blog.tags.index') }}" class="sidebar-link {{ request()->routeIs('admin.blog.tags.*') ? 'active' : '' }}">
                            <i class="fas fa-tags text-lg ml-4 text-sm"></i>
                            <span class="text-sm">Tags</span>
                        </a>
                    @endif
                    
                    @if(config('business.features.cms'))
                        <a href="{{ route('admin.cms.index') }}" class="sidebar-link {{ request()->routeIs('admin.cms.index') || request()->routeIs('admin.cms.show') || request()->routeIs('admin.cms.create') || request()->routeIs('admin.cms.edit') ? 'active' : '' }}">
                            <i class="fas fa-file-alt text-lg"></i>
                            <span>CMS Editor</span>
                        </a>
                        <a href="{{ route('admin.cms.images.index') }}" class="sidebar-link {{ request()->routeIs('admin.cms.images.*') ? 'active' : '' }}">
                            <i class="fas fa-images text-lg"></i>
                            <span>Media Library</span>
                            @php
                                try {
                                    $imageCount = \App\Models\CmsImage::count();
                                    if ($imageCount > 0) {
                                        echo '<span class="ml-auto bg-green-600/20 text-green-400 text-xs font-semibold px-2 py-0.5 rounded-full">' . $imageCount . '</span>';
                                    }
                                } catch (\Exception $e) {
                                    // Silently fail if table doesn't exist yet
                                }
                            @endphp
                        </a>
                    @endif
                    
                    @if(config('ai.enabled'))
                        <a href="{{ route('admin.ai.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.ai.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-robot text-lg"></i>
                            <span>AI Assistant</span>
                        </a>
                    @endif
                </div>
                @endif

                <!-- Settings Section -->
                @if(auth()->user()->isAdministrator())
                <div class="mb-6 space-y-1">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Settings</div>
                    <a href="{{ route('admin.business_settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.business_settings.*') ? 'active' : '' }}">
                        <i class="fas fa-building text-lg"></i>
                        <span>Business Settings</span>
                    </a>
                    <a href="{{ route('admin.environment_settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.environment_settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog text-lg"></i>
                        <span>System Settings</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog text-lg"></i>
                        <span>User Management</span>
                    </a>
                    <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <i class="fas fa-history text-lg"></i>
                        <span>Activity Logs</span>
                    </a>
                </div>
                @endif

                <!-- Development Tools (Debug Mode Only) -->
                @if(config('app.debug') && auth()->user()->isAdministrator())
                <div class="mb-6 space-y-1">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-500 uppercase tracking-wider mb-3 px-3">Development</div>
                    <a href="{{ route('debug.design') }}" class="sidebar-link {{ request()->routeIs('debug.design') ? 'active' : '' }}">
                        <i class="fas fa-palette text-lg"></i>
                        <span>Design Playground</span>
                    </a>
                    <a href="{{ route('debug.info') }}" class="sidebar-link {{ request()->routeIs('debug.info') ? 'active' : '' }}">
                        <i class="fas fa-info-circle text-lg"></i>
                        <span>Debug Info</span>
                    </a>
                </div>
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
            <header class="sticky top-0 z-30 h-16 bg-white dark:bg-gray-800/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 flex items-center justify-between px-3 sm:px-6">
                <!-- Mobile hamburger (visible only on mobile) -->
                <button id="sidebar-toggle" class="lg:hidden text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded p-2 transition-colors flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <!-- Mobile branding (visible only on mobile) -->
                <div class="lg:hidden flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bridge text-white text-xs"></i>
                    </div>
                    <span class="text-gray-900 dark:text-white font-semibold text-sm">CB</span>
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

    <style>
        /* Sidebar Link Styles */
        .sidebar-link {
            @apply flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group;
        }
        
        .sidebar-link.active {
            @apply bg-primary-600/20 text-primary-400 font-medium;
        }
        
        .sidebar-link:hover i {
            @apply transform scale-110 transition-transform duration-200;
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom scrollbar for sidebar */
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
