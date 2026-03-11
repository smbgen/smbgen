<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Extreme') — Laravel Full-Stack App Generator</title>
    <meta name="description" content="Extreme by smbgen — describe your app in plain English, get a production-ready Laravel full-stack codebase back.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .cs-hero-bg {
            background: radial-gradient(ellipse at 60% 0%, rgba(6,182,212,0.12) 0%, transparent 60%),
                        radial-gradient(ellipse at 10% 80%, rgba(139,92,246,0.10) 0%, transparent 50%),
                        #060d1a;
        }
        .cs-gradient-text {
            background: linear-gradient(135deg, #06b6d4, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#060d1a] text-gray-100 antialiased font-sans">

    {{-- ─── NAV ─────────────────────────────────────────────────── --}}
    <nav class="sticky top-0 z-50 border-b border-white/5 backdrop-blur-md bg-[#060d1a]/80">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('extreme') }}" class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-md bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286z" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-tight">Extreme</span>
                <span class="hidden sm:block text-gray-500 text-sm">by smbgen</span>
            </a>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('cleanslate.dashboard') }}" class="hidden md:block text-gray-400 hover:text-white text-sm transition-colors">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-white text-sm transition-colors">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hidden md:block text-gray-400 hover:text-white text-sm transition-colors">Sign in</a>
                @endauth
                <a href="{{ route('cleanslate.billing.plans') }}" class="px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-white text-sm font-medium transition-colors">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    {{-- ─── FLASH MESSAGES ──────────────────────────────────────── --}}
    @foreach(['success', 'error'] as $type)
        @if(session($type))
        <div class="max-w-2xl mx-auto px-6 pt-6">
            <div class="px-4 py-3 rounded-xl text-sm border
                {{ $type === 'success' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20' }}">
                {{ session($type) }}
            </div>
        </div>
        @endif
    @endforeach

    {{-- ─── PAGE CONTENT ────────────────────────────────────────── --}}
    <main>
        @yield('content')
    </main>

    {{-- ─── FOOTER ──────────────────────────────────────────────── --}}
    <footer class="border-t border-white/5 py-10 mt-20">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-md bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286z" />
                    </svg>
                </div>
                <span class="text-gray-400 text-sm">Extreme by <span class="text-white">smbgen</span></span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('extreme') }}#start" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">Get Early Access</a>
            </div>
            <p class="text-gray-700 text-xs">© {{ date('Y') }} smbgen. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
