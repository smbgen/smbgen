@php
    $navbarSettings = \App\Models\CmsNavbarSetting::getSettings();
    $bgColor = $navbarSettings->getBackgroundColor();
    $textColor = $navbarSettings->getTextColor();
    $menuItems = $navbarSettings->getOrderedMenuItems();
    $logoText = $navbarSettings->logo_text ?? config('business.name');
    $logoImage = $navbarSettings->logo_image_url;
    $isSticky = $navbarSettings->is_sticky ?? true;
@endphp

<nav x-data="{ mobileMenuOpen: false }" style="background-color: {{ $bgColor }}; color: {{ $textColor }};" class="shadow-lg {{ $isSticky ? 'sticky top-0 z-50' : '' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    @if($logoImage)
                        <img src="{{ $logoImage }}" alt="{{ $logoText }}" class="h-8 w-auto">
                    @endif
                    @if($logoText)
                        <span class="font-bold text-xl" style="color: {{ $textColor }};">{{ $logoText }}</span>
                    @endif
                </a>
            </div>

            {{-- Desktop Menu --}}
            <div class="hidden md:flex md:items-center md:space-x-6">
                @foreach($menuItems as $item)
                    @if(!empty($item['style']))
                        <a 
                            href="{{ $item['url'] }}" 
                            target="{{ $item['target'] ?? '_self' }}"
                            class="{{ $item['style'] }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        <a 
                            href="{{ $item['url'] }}" 
                            target="{{ $item['target'] ?? '_self' }}"
                            class="hover:opacity-80 transition-opacity duration-200 font-medium"
                            style="color: {{ $textColor }};"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
                
                {{-- Auth Links --}}
                @auth
                    <a 
                        href="{{ auth()->user()->role === 'company_administrator' ? route('admin.dashboard') : route('dashboard') }}" 
                        class="hover:opacity-80 transition-opacity duration-200 font-medium"
                        style="color: {{ $textColor }};"
                    >
                        Dashboard
                    </a>
                @else
                    <a 
                        href="{{ route('login') }}" 
                        class="hover:opacity-80 transition-opacity duration-200 font-medium"
                        style="color: {{ $textColor }};"
                    >
                        Login
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden">
                <button 
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    type="button" 
                    class="inline-flex items-center justify-center p-2 rounded-md hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-opacity"
                    style="color: {{ $textColor }};"
                    aria-expanded="false"
                >
                    <span class="sr-only">Open main menu</span>
                    {{-- Icon when menu is closed --}}
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    {{-- Icon when menu is open --}}
                    <svg x-show="mobileMenuOpen" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div 
        x-show="mobileMenuOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="md:hidden"
        x-cloak
    >
        <div class="px-2 pt-2 pb-3 space-y-2 sm:px-3" style="background-color: {{ $bgColor }};">
            @foreach($menuItems as $item)
                @if(!empty($item['style']))
                    <a 
                        href="{{ $item['url'] }}" 
                        target="{{ $item['target'] ?? '_self' }}"
                        class="{{ $item['style'] }} block text-center mx-2"
                    >
                        {{ $item['label'] }}
                    </a>
                @else
                    <a 
                        href="{{ $item['url'] }}" 
                        target="{{ $item['target'] ?? '_self' }}"
                        class="block px-3 py-2 rounded-md text-base font-medium hover:opacity-80 transition-opacity"
                        style="color: {{ $textColor }};"
                    >
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
            
            {{-- Auth Links --}}
            @auth
                <a 
                    href="{{ auth()->user()->role === 'company_administrator' ? route('admin.dashboard') : route('dashboard') }}" 
                    class="block px-3 py-2 rounded-md text-base font-medium hover:opacity-80 transition-opacity"
                    style="color: {{ $textColor }};"
                >
                    Dashboard
                </a>
            @else
                <a 
                    href="{{ route('login') }}" 
                    class="block px-3 py-2 rounded-md text-base font-medium hover:opacity-80 transition-opacity"
                    style="color: {{ $textColor }};"
                >
                    Login
                </a>
            @endauth
        </div>
    </div>
</nav>

<style>
    [x-cloak] { 
        display: none !important; 
    }
</style>
