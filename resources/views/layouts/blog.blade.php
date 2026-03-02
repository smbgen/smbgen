<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-mode-only">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Blog') - {{ config('app.name', 'smbgen') }}</title>
    
    <!-- SEO Meta Tags -->
    @stack('meta')
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js is loaded via Vite in app.js -->

    <!-- Company Colors CSS (Auto-injected) -->
    @php
        $companyColors = \App\Models\CmsCompanyColors::getSettings();
    @endphp
    @if($companyColors->auto_inject_css)
        {!! $companyColors->generateCSS() !!}
    @endif
    
    <!-- View-specific Styles -->
    @stack('styles')
    
    <!-- Disable dark mode for blog -->
    <script>
        // Force light mode on blog pages - run immediately
        document.documentElement.classList.remove('dark');
        
        // Override dark mode manager after it loads
        if (typeof window !== 'undefined') {
            window.addEventListener('DOMContentLoaded', function() {
                if (window.DarkModeManager) {
                    document.documentElement.classList.remove('dark');
                }
            });
            
            // Also check after everything loads
            window.addEventListener('load', function() {
                document.documentElement.classList.remove('dark');
            });
        }
    </script>
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-770N2CMS5K"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-770N2CMS5K');
    </script>
</head>
<body class="m-0 p-0 bg-gray-50">
    <!-- CMS Public Navbar (Top) -->
    <x-public-navbar />
    
    <!-- Blog-Specific Navigation Bar (Below CMS Navbar) -->
    <nav class="bg-white border-b border-gray-200 shadow-sm" x-data="{ 
        mobileMenuOpen: false,
        categoriesOpen: false
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <!-- Left Side: Blog Links -->
                <div class="flex items-center space-x-6">
                    <a href="{{ route('blog.index') }}" 
                       class="inline-flex items-center h-14 text-sm font-medium {{ request()->routeIs('blog.index') ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600' }} transition">
                        <i class="fas fa-newspaper mr-2"></i>
                        All Posts
                    </a>
                    
                    <!-- Categories Dropdown -->
                    <div class="relative hidden md:flex md:items-center h-14" @click.away="categoriesOpen = false">
                        <button @click="categoriesOpen = !categoriesOpen" 
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 transition">
                            <i class="fas fa-folder mr-2"></i>
                            Categories
                            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="categoriesOpen"
                             x-transition
                             class="absolute left-0 top-full mt-0 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                @php
                                    $categories = \App\Models\BlogCategory::withCount('posts')->orderBy('name')->get();
                                @endphp
                                @forelse($categories as $category)
                                    <a href="{{ route('blog.category', $category->slug) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ $category->name }}
                                        <span class="text-gray-500">({{ $category->posts_count }})</span>
                                    </a>
                                @empty
                                    <div class="px-4 py-2 text-sm text-gray-500">
                                        No categories yet
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tags Link -->
                    <a href="{{ route('blog.index') }}#tags" 
                       class="hidden md:inline-flex items-center h-14 text-sm font-medium text-gray-700 hover:text-blue-600 transition">
                        <i class="fas fa-tags mr-2"></i>
                        Tags
                    </a>
                    
                    @auth
                        @if(auth()->user()->isAdministrator() || auth()->user()->role === 'company_administrator')
                            <a href="{{ route('admin.blog.posts.index') }}" 
                               class="hidden md:inline-flex items-center h-14 text-sm font-medium text-purple-600 hover:text-purple-700 transition">
                                <i class="fas fa-edit mr-2"></i>
                                Manage Blog
                            </a>
                        @endif
                    @endauth
                </div>
                
                <!-- Right Side: Search -->
                <div class="flex items-center space-x-4">
                    <form action="{{ route('blog.search') }}" method="GET" class="hidden md:block">
                        <div class="relative">
                            <input type="text" 
                                   name="q" 
                                   placeholder="Search posts..." 
                                   value="{{ request('q') }}"
                                   class="w-64 px-4 py-2 pr-10 text-sm border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Mobile menu button -->
                    <button type="button" 
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" 
             x-transition
             class="md:hidden border-t border-gray-200"
             style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('blog.index') }}" 
                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('blog.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-newspaper mr-2"></i>
                    All Posts
                </a>
                
                <!-- Mobile Categories -->
                <div class="px-3 py-2">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Categories
                    </div>
                    @php
                        $categories = \App\Models\BlogCategory::withCount('posts')->orderBy('name')->get();
                    @endphp
                    @forelse($categories as $category)
                        <a href="{{ route('blog.category', $category->slug) }}" 
                           class="block px-3 py-1 text-sm text-gray-700 hover:text-blue-600">
                            {{ $category->name }} ({{ $category->posts_count }})
                        </a>
                    @empty
                        <div class="px-3 py-1 text-sm text-gray-500">
                            No categories yet
                        </div>
                    @endforelse
                </div>
                
                <!-- Mobile Search -->
                <form action="{{ route('blog.search') }}" method="GET" class="px-3 py-2">
                    <input type="text" 
                           name="q" 
                           placeholder="Search posts..." 
                           value="{{ request('q') }}"
                           class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </form>
                
                @auth
                    @if(auth()->user()->isAdministrator() || auth()->user()->role === 'company_administrator')
                        <a href="{{ route('admin.blog.posts.index') }}" 
                           class="block px-3 py-2 rounded-md text-base font-medium text-purple-600 hover:text-purple-700">
                            <i class="fas fa-edit mr-2"></i>
                            Manage Blog
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    
    <!-- Page Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-public-footer />
    
    <!-- View-specific Scripts -->
    @stack('scripts')
</body>
</html>
