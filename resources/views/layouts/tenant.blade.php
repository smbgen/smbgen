<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ tenant('name') ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-950 text-gray-100">

    <div class="flex min-h-screen flex-col lg:flex-row">

        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 border-r border-gray-800 flex flex-col lg:static transition-transform duration-300 -translate-x-full lg:translate-x-0" id="tenant-sidebar">

            {{-- Logo --}}
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center">
                        <span class="text-white font-bold text-sm">{{ strtoupper(substr(tenant('name') ?? 'T', 0, 2)) }}</span>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm leading-tight">{{ tenant('name') ?? 'Portal' }}</p>
                        <p class="text-gray-500 text-xs">{{ ucfirst(tenant('plan') ?? 'starter') }} plan</p>
                    </div>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('tenant.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tenant.dashboard') ? 'bg-violet-600/20 text-violet-300' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="text-base">🏠</span> Overview
                </a>

                @php $modules = tenant('modules_enabled') ?? []; @endphp

                @if(in_array('signal', $modules))
                <a href="{{ route('tenant.signal') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tenant.signal') ? 'bg-violet-600/20 text-violet-300' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="text-base">📡</span> SIGNAL
                    <span class="ml-auto text-xs text-gray-600">Social</span>
                </a>
                @endif

                @if(in_array('relay', $modules))
                <a href="{{ route('tenant.relay') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tenant.relay') ? 'bg-violet-600/20 text-violet-300' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="text-base">📬</span> RELAY
                    <span class="ml-auto text-xs text-gray-600">Email</span>
                </a>
                @endif

                @if(in_array('surge', $modules))
                <a href="{{ route('tenant.surge') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tenant.surge') ? 'bg-violet-600/20 text-violet-300' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="text-base">⚡</span> SURGE
                    <span class="ml-auto text-xs text-gray-600">CRM</span>
                </a>
                @endif

                @if(in_array('cast', $modules))
                <a href="{{ route('tenant.cast') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tenant.cast') ? 'bg-violet-600/20 text-violet-300' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="text-base">🌐</span> CAST
                    <span class="ml-auto text-xs text-gray-600">Web</span>
                </a>
                @endif

                @if(in_array('vault', $modules))
                <a href="{{ route('tenant.vault') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tenant.vault') ? 'bg-violet-600/20 text-violet-300' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="text-base">🔐</span> VAULT
                    <span class="ml-auto text-xs text-gray-600">Backup</span>
                </a>
                @endif

                @if(in_array('extreme', $modules))
                <a href="{{ route('tenant.extreme') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tenant.extreme') ? 'bg-violet-600/20 text-violet-300' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="text-base">🚀</span> EXTREME
                    <span class="ml-auto text-xs text-gray-600">Deploy</span>
                </a>
                @endif

                {{-- Divider --}}
                <div class="border-t border-gray-800 my-2"></div>

                @auth
                <a href="{{ route('tenant.profile') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                    <span class="text-base">👤</span> Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-colors text-left">
                        <span class="text-base">🚪</span> Sign Out
                    </button>
                </form>
                @endauth
            </nav>
        </aside>

        {{-- Mobile toggle --}}
        <button id="tenant-sidebar-toggle"
                class="lg:hidden fixed top-4 left-4 z-50 w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center text-white">
            ☰
        </button>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top bar --}}
            <header class="h-14 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6 sticky top-0 z-40">
                <div class="lg:hidden w-8"></div>
                <div class="text-sm text-gray-500">
                    @yield('breadcrumb', tenant('name') ?? 'Portal')
                </div>
                @auth
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <span>{{ auth()->user()->name }}</span>
                </div>
                @endauth
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-500/10 border border-green-500/30 text-green-300 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-500/10 border border-red-500/30 text-red-300 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        const toggle = document.getElementById('tenant-sidebar-toggle');
        const sidebar = document.getElementById('tenant-sidebar');
        if (toggle && sidebar) {
            toggle.addEventListener('click', () => sidebar.classList.toggle('-translate-x-full'));
        }
    </script>
</body>
</html>
