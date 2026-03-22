{{-- Marketing Nav — standalone, no CMS dependency --}}
<nav
    x-data="{ open: false }"
    class="bg-white border-b border-gray-100 sticky top-0 z-50"
>
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2.5 group">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="font-extrabold text-gray-900 text-lg tracking-tight">smbgen</span>
        </a>

        {{-- Desktop links --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-500">
            <a href="#platform"  class="hover:text-gray-900 transition-colors">Platform</a>
            <a href="#services"  class="hover:text-gray-900 transition-colors">Services</a>
            <a href="#growth"    class="hover:text-gray-900 transition-colors">Growth</a>
            @if(Route::has('blog.index'))
                <a href="{{ route('blog.index') }}" class="hover:text-gray-900 transition-colors">Blog</a>
            @endif
            <a href="{{ route('contact.submit') !== null ? '/contact' : '#' }}" class="hover:text-gray-900 transition-colors">Contact</a>
        </div>

        {{-- CTA --}}
        <div class="hidden md:flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                    Dashboard &rarr;
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    Sign in
                </a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white text-sm font-bold px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors">
                    Get started &rarr;
                </a>
            @endauth
        </div>

        {{-- Mobile hamburger --}}
        <button
            @click="open = !open"
            class="md:hidden text-gray-500 hover:text-gray-900 transition-colors p-1"
            aria-label="Toggle menu"
        >
            <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition class="md:hidden border-t border-gray-100 bg-white">
        <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col gap-4">
            <a href="#platform" @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Platform</a>
            <a href="#services"  @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Services</a>
            <a href="#growth"    @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Growth</a>
            @if(Route::has('blog.index'))
                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Blog</a>
            @endif
            <a href="/contact" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Contact</a>
            <div class="border-t border-gray-100 pt-4 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white text-sm font-bold px-4 py-2.5 rounded-lg text-center">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 text-center py-2">Sign in</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white text-sm font-bold px-4 py-2.5 rounded-lg text-center">
                        Get started &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
