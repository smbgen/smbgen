<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin — {{ config('app.name', 'smbgen') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite('resources/js/app.js')
    @stack('styles')
</head>
<body class="bg-slate-950 font-sans antialiased text-gray-100">
    <div class="flex min-h-screen flex-col lg:flex-row">

        {{-- Sidebar --}}
        <aside id="super-admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 flex-shrink-0 bg-slate-900 border-r border-slate-800 flex flex-col transform -translate-x-full transition-transform duration-300 lg:static lg:translate-x-0">
            <div class="px-6 py-5 border-b border-slate-800 flex items-start justify-between gap-3">
                <a href="{{ route('super-admin.dashboard') }}" class="flex items-start gap-3 text-white">
                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-cyan-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-cyan-950/30">
                        <i class="fas fa-shield-halved text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-cyan-300">Platform Control</p>
                        <p class="text-lg font-semibold leading-tight">Super Admin Console</p>
                        <p class="text-xs text-slate-400 mt-1">Central-only operations surface</p>
                    </div>
                </a>
                <button id="super-admin-sidebar-close" type="button" aria-label="Close sidebar" class="lg:hidden text-slate-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Overview</p>

                <a href="{{ route('super-admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('super-admin.dashboard') ? 'bg-cyan-500/15 text-cyan-200 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }}">
                    <i class="fas fa-tachometer-alt w-4"></i>
                    Dashboard
                </a>

                <a href="{{ route('super-admin.guided-setup') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('super-admin.guided-setup') ? 'bg-cyan-500/15 text-cyan-200 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }}">
                    <i class="fas fa-wand-magic-sparkles w-4"></i>
                    Guided Setup
                </a>

                <p class="px-3 mt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Tenancy</p>

                <a href="{{ route('super-admin.tenants.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('super-admin.tenants.*') ? 'bg-cyan-500/15 text-cyan-200 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }}">
                    <i class="fas fa-building w-4"></i>
                    Tenants
                </a>

                     <a href="{{ route('super-admin.billing.index') }}"
                         class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('super-admin.billing.*') ? 'bg-cyan-500/15 text-cyan-200 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }}">
                          <i class="fas fa-credit-card w-4"></i>
                          Platform Billing
                     </a>

                <a href="{{ route('super-admin.diagnostics') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('super-admin.diagnostics') ? 'bg-cyan-500/15 text-cyan-200 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }}">
                    <i class="fas fa-stethoscope w-4"></i>
                    Diagnostics
                </a>

                <p class="px-3 mt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Deployment</p>

                <a href="{{ route('super-admin.deployment-console') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('super-admin.deployment-console') ? 'bg-cyan-500/15 text-cyan-200 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }}">
                    <i class="fas fa-terminal w-4"></i>
                    Deployment Console
                </a>

                <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-950/70 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Boundary</p>
                    <p class="mt-2 text-sm text-slate-300">This console is central-only and separate from tenant admin surfaces.</p>
                    <div class="mt-3 flex flex-col gap-2 text-xs">
                        <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-white transition-colors">Open tenant admin</a>
                        <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white transition-colors">Open client portal</a>
                    </div>
                </div>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-cyan-600 flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:text-white transition-colors" title="Sign out">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top bar --}}
            <header class="h-16 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-3 sm:px-6 flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <button id="super-admin-sidebar-toggle" type="button" aria-label="Open sidebar" class="lg:hidden text-slate-200 hover:text-white hover:bg-slate-800 rounded-lg p-2 transition-colors">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="lg:hidden">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Super Admin</p>
                        <p class="text-sm text-slate-300">Platform control</p>
                    </div>
                    @if (isset($breadcrumbs) && count($breadcrumbs))
                        <nav class="hidden lg:flex items-center gap-2 text-sm">
                            @foreach ($breadcrumbs as $crumb)
                                @if (!$loop->last)
                                    <a href="{{ $crumb['url'] }}" class="text-slate-400 hover:text-white transition-colors">{{ $crumb['label'] }}</a>
                                    <span class="text-slate-600">/</span>
                                @else
                                    <span class="text-slate-200">{{ $crumb['label'] }}</span>
                                @endif
                            @endforeach
                        </nav>
                    @else
                        <div class="hidden lg:block">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Super Admin Surface</p>
                            <p class="text-sm text-slate-300">Central platform operations</p>
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if (session('super_admin_impersonating'))
                        <form method="POST" action="{{ route('super-admin.stop-impersonating') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-user-secret"></i>
                                Stop Impersonating
                            </button>
                        </form>
                    @endif
                </div>
            </header>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="mx-3 sm:mx-6 mt-4 px-4 py-3 bg-green-900/50 border border-green-700 rounded-lg text-green-300 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mx-3 sm:mx-6 mt-4 px-4 py-3 bg-red-900/50 border border-red-700 rounded-lg text-red-300 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <main class="flex-1 p-3 sm:p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
        <div id="super-admin-sidebar-overlay" class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm hidden lg:hidden"></div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('super-admin-sidebar');
            const overlay = document.getElementById('super-admin-sidebar-overlay');
            const toggleButton = document.getElementById('super-admin-sidebar-toggle');
            const closeButton = document.getElementById('super-admin-sidebar-close');

            function openSidebar() {
                if (!sidebar || !overlay) {
                    return;
                }
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (!sidebar || !overlay) {
                    return;
                }
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            toggleButton?.addEventListener('click', openSidebar);
            closeButton?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);

            sidebar?.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', function () {
                    const href = link.getAttribute('href') || '';
                    const target = link.getAttribute('target');

                    if (href.startsWith('#') || target === '_blank') {
                        return;
                    }

                    closeSidebar();
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && overlay && !overlay.classList.contains('hidden')) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
