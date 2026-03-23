{{-- ── smbgen Frontend Nav ─────────────────────────────────────────── --}}
@php
    $portalHref = auth()->check()
        ? (auth()->user()->isAdministrator() ? route('admin.dashboard') : route('dashboard'))
        : route('login');
@endphp

<nav
    x-data="{ open: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    :class="scrolled ? 'shadow-sm' : ''"
    class="bg-white border-b border-gray-100 sticky top-0 z-50 transition-shadow"
>
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="flex flex-col leading-none">
                <span class="font-extrabold text-gray-900 text-lg tracking-tight">smbgen</span>
                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">smbgen-core</span>
            </div>
        </a>

        {{-- Desktop links --}}
        <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-blue-700">smbgen-core</span>
            <a href="{{ route('solutions') }}#contact-core" class="hover:text-gray-900 transition-colors">Contact</a>
            <a href="{{ route('solutions') }}#book-core" class="hover:text-gray-900 transition-colors">Book</a>
            <a href="{{ route('solutions') }}#pay-core" class="hover:text-gray-900 transition-colors">Pay</a>
            <a href="{{ route('solutions') }}#portal-core" class="hover:text-gray-900 transition-colors">Client Portal</a>
            <a href="{{ route('solutions') }}#crm-core" class="hover:text-gray-900 transition-colors">CRM</a>
            <a href="{{ route('solutions') }}#cms-core" class="hover:text-gray-900 transition-colors">CMS</a>
        </div>

        {{-- Desktop CTA --}}
        <div class="hidden md:flex items-center gap-3">
            <a href="https://github.com/smbgen" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition-colors hover:border-gray-300 hover:text-gray-900">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 .5C5.648.5.5 5.648.5 12c0 5.084 3.292 9.396 7.86 10.918.575.106.785-.25.785-.556 0-.274-.01-1-.015-1.962-3.197.694-3.872-1.54-3.872-1.54-.523-1.328-1.277-1.682-1.277-1.682-1.044-.714.079-.699.079-.699 1.154.08 1.761 1.186 1.761 1.186 1.026 1.758 2.693 1.25 3.35.956.104-.743.402-1.25.731-1.537-2.552-.29-5.236-1.276-5.236-5.68 0-1.255.449-2.282 1.184-3.086-.119-.29-.513-1.459.112-3.042 0 0 .966-.31 3.166 1.179A10.98 10.98 0 0 1 12 6.032c.977.005 1.961.132 2.881.387 2.198-1.49 3.163-1.18 3.163-1.18.627 1.584.233 2.753.114 3.043.737.804 1.182 1.83 1.182 3.086 0 4.415-2.688 5.387-5.25 5.671.413.355.781 1.055.781 2.126 0 1.536-.014 2.774-.014 3.151 0 .309.207.668.79.555C20.21 21.392 23.5 17.082 23.5 12 23.5 5.648 18.352.5 12 .5Z"/>
                </svg>
                smbgen org
            </a>
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
            aria-label="Toggle navigation menu"
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
        <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col gap-3">
            <div class="pb-1 text-[10px] font-black uppercase tracking-[0.2em] text-blue-700">smbgen-core</div>
            <a href="{{ route('solutions') }}#contact-core" @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Contact</a>
            <a href="{{ route('solutions') }}#book-core" @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Book</a>
            <a href="{{ route('solutions') }}#pay-core" @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Pay</a>
            <a href="{{ route('solutions') }}#portal-core" @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">Client Portal</a>
            <a href="{{ route('solutions') }}#crm-core" @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">CRM</a>
            <a href="{{ route('solutions') }}#cms-core" @click="open=false" class="text-sm font-medium text-gray-600 hover:text-gray-900 py-1">CMS</a>
            <div class="border-t border-gray-100 pt-3 flex flex-col gap-2">
                <a href="https://github.com/smbgen" target="_blank" rel="noreferrer" class="text-sm font-medium text-gray-600 text-center py-2">smbgen org on GitHub</a>
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
