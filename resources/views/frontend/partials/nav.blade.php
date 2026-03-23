{{-- ── smbgen Frontend Nav ─────────────────────────────────────────── --}}
@php
    $portalHref = auth()->check()
        ? (auth()->user()->isAdministrator() ? route('admin.dashboard') : route('dashboard'))
        : route('login');

    $solutionsMenu = [
        [
            'heading' => 'smbgen-core',
            'description' => 'Contact, Book, Pay, Client Portal, CRM, CMS',
            'cardStyle' => 'background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%); border-color: #dbeafe;',
            'links' => [
                ['label' => 'Contact', 'href' => route('product.contact')],
                ['label' => 'Book', 'href' => route('product.book')],
                ['label' => 'Pay', 'href' => route('product.pay')],
                ['label' => 'Client Portal', 'href' => route('product.portal')],
                ['label' => 'CRM', 'href' => route('product.crm')],
                ['label' => 'CMS', 'href' => route('product.cms')],
            ],
        ],
        [
            'heading' => 'Extreme',
            'description' => 'Rapid app development',
            'cardStyle' => 'background: linear-gradient(135deg, #fef2f2 0%, #fff1f2 100%); border-color: #fee2e2;',
            'links' => [
                ['label' => 'Extreme Overview', 'href' => route('extreme')],
                ['label' => 'Extreme Demo', 'href' => route('extreme.demo')],
            ],
        ],
        [
            'heading' => 'Google Workspace',
            'description' => 'Integrate and eliminate data silos',
            'cardStyle' => 'background: linear-gradient(135deg, #eef2ff 0%, #f0f9ff 35%, #ecfdf5 70%, #fff7ed 100%); border-color: #ddd6fe;',
            'links' => [
                ['label' => 'Google Workspace Integrations', 'href' => route('google.workspace')],
            ],
        ],
    ];

    $salesMenu = [
        [
            'heading' => 'By Industry',
            'description' => 'Talk through your market and operating model',
            'links' => [
                ['label' => 'Book an Industry Discovery Call', 'href' => Route::has('booking.wizard') ? route('booking.wizard') . '?intent=industry' : route('contact')],
            ],
        ],
        [
            'heading' => 'By Solutions',
            'description' => 'Start from business bottlenecks and outcomes',
            'links' => [
                ['label' => 'Book a Solutions Call', 'href' => Route::has('booking.wizard') ? route('booking.wizard') . '?intent=solutions' : route('contact')],
                ['label' => 'View All Solution Areas', 'href' => route('solutions')],
            ],
        ],
    ];

    $supportMenu = [
        [
            'heading' => 'GitHub Docs',
            'description' => 'Technical references and implementation docs',
            'links' => [
                ['label' => 'smbgen org', 'href' => 'https://github.com/smbgen', 'external' => true],
            ],
        ],
        [
            'heading' => 'Support Area',
            'description' => 'Account access and direct support workflows',
            'links' => [
                ['label' => auth()->check() ? 'Open Support Area' : 'Sign In to Support', 'href' => $portalHref],
                ['label' => 'Contact Support', 'href' => route('contact') . '?topic=support'],
            ],
        ],
    ];
@endphp

<nav
    x-data="{
        mobileOpen: false,
        activePanel: null,
        scrolled: false,
        mobileSections: {
            solutions: false,
            sales: false,
            support: false,
        },
        openPanel(panel) {
            this.activePanel = panel;
        },
        closePanel() {
            this.activePanel = null;
        },
        togglePanel(panel) {
            this.activePanel = this.activePanel === panel ? null : panel;
        },
        toggleMobileSection(section) {
            this.mobileSections[section] = !this.mobileSections[section];
        }
    }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    :class="scrolled ? 'shadow-sm' : ''"
    class="bg-white border-b border-gray-100 sticky top-0 z-50 transition-shadow"
>
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

        <a href="{{ route('home') }}" class="flex items-center gap-2.5 group shrink-0">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="flex flex-col leading-none">
                <span class="font-extrabold text-gray-900 text-lg tracking-tight">smbgen</span>
                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">platform</span>
            </div>
        </a>

        <div class="hidden lg:flex items-center gap-7 text-sm font-semibold text-gray-600">
            <button
                type="button"
                @mouseenter="openPanel('solutions')"
                @focus="openPanel('solutions')"
                @click="togglePanel('solutions')"
                class="inline-flex items-center gap-2 hover:text-gray-900 transition-colors"
                :class="activePanel === 'solutions' ? 'text-gray-900' : ''"
            >
                Solutions
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <button
                type="button"
                @mouseenter="openPanel('sales')"
                @focus="openPanel('sales')"
                @click="togglePanel('sales')"
                class="inline-flex items-center gap-2 hover:text-gray-900 transition-colors"
                :class="activePanel === 'sales' ? 'text-gray-900' : ''"
            >
                Engage with Sales
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <button
                type="button"
                @mouseenter="openPanel('support')"
                @focus="openPanel('support')"
                @click="togglePanel('support')"
                class="inline-flex items-center gap-2 hover:text-gray-900 transition-colors"
                :class="activePanel === 'support' ? 'text-gray-900' : ''"
            >
                Get Support
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <div class="hidden lg:flex items-center gap-3">
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
            @endauth
        </div>

        <button
            @click="mobileOpen = !mobileOpen"
            class="lg:hidden text-gray-500 hover:text-gray-900 transition-colors p-1"
            aria-label="Toggle navigation menu"
        >
            <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

    </div>

    <div
        x-show="activePanel !== null"
        x-transition
        @mouseleave="closePanel()"
        class="hidden lg:block border-t border-gray-100 bg-white"
    >
        <div class="max-w-6xl mx-auto px-6 py-6">
            <template x-if="activePanel === 'solutions'">
                <div class="grid grid-cols-3 gap-6">
                    @foreach($solutionsMenu as $group)
                        <div class="rounded-2xl border p-5" style="{{ $group['cardStyle'] }}">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">{{ $group['heading'] }}</h3>
                            <p class="mt-1 text-xs text-gray-500 leading-relaxed">{{ $group['description'] }}</p>
                            <div class="mt-4 flex flex-col gap-2">
                                @foreach($group['links'] as $item)
                                    <a href="{{ $item['href'] }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </template>

            <template x-if="activePanel === 'sales'">
                <div class="grid grid-cols-2 gap-6">
                    @foreach($salesMenu as $group)
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">{{ $group['heading'] }}</h3>
                            <p class="mt-1 text-xs text-gray-500 leading-relaxed">{{ $group['description'] }}</p>
                            <div class="mt-4 flex flex-col gap-2">
                                @foreach($group['links'] as $item)
                                    <a href="{{ $item['href'] }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </template>

            <template x-if="activePanel === 'support'">
                <div class="grid grid-cols-2 gap-6">
                    @foreach($supportMenu as $group)
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">{{ $group['heading'] }}</h3>
                            <p class="mt-1 text-xs text-gray-500 leading-relaxed">{{ $group['description'] }}</p>
                            <div class="mt-4 flex flex-col gap-2">
                                @foreach($group['links'] as $item)
                                    <a href="{{ $item['href'] }}" @if(!empty($item['external'])) target="_blank" rel="noreferrer" @endif class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </template>
        </div>
    </div>

    <div x-show="mobileOpen" x-transition class="lg:hidden border-t border-gray-100 bg-white">
        <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col gap-3">
            <div class="rounded-xl border border-gray-200">
                <button @click="toggleMobileSection('solutions')" class="w-full flex items-center justify-between px-4 py-3 text-left text-sm font-bold text-gray-800">
                    <span>Solutions</span>
                    <span x-text="mobileSections.solutions ? '−' : '+'"></span>
                </button>
                <div x-show="mobileSections.solutions" x-transition class="px-4 pb-4 pt-1 flex flex-col gap-4">
                    @foreach($solutionsMenu as $group)
                        <div class="rounded-xl border p-3" style="{{ $group['cardStyle'] }}">
                            <div class="text-[11px] uppercase font-black tracking-wide text-gray-700">{{ $group['heading'] }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $group['description'] }}</div>
                            <div class="mt-2 flex flex-col gap-1.5">
                                @foreach($group['links'] as $item)
                                    <a href="{{ $item['href'] }}" @click="mobileOpen=false" class="text-sm text-gray-600 hover:text-gray-900">{{ $item['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-gray-200">
                <button @click="toggleMobileSection('sales')" class="w-full flex items-center justify-between px-4 py-3 text-left text-sm font-bold text-gray-800">
                    <span>Engage with Sales</span>
                    <span x-text="mobileSections.sales ? '−' : '+'"></span>
                </button>
                <div x-show="mobileSections.sales" x-transition class="px-4 pb-4 pt-1 flex flex-col gap-4">
                    @foreach($salesMenu as $group)
                        <div>
                            <div class="text-[11px] uppercase font-black tracking-wide text-gray-700">{{ $group['heading'] }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $group['description'] }}</div>
                            <div class="mt-2 flex flex-col gap-1.5">
                                @foreach($group['links'] as $item)
                                    <a href="{{ $item['href'] }}" @click="mobileOpen=false" class="text-sm text-gray-600 hover:text-gray-900">{{ $item['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-gray-200">
                <button @click="toggleMobileSection('support')" class="w-full flex items-center justify-between px-4 py-3 text-left text-sm font-bold text-gray-800">
                    <span>Get Support</span>
                    <span x-text="mobileSections.support ? '−' : '+'"></span>
                </button>
                <div x-show="mobileSections.support" x-transition class="px-4 pb-4 pt-1 flex flex-col gap-4">
                    @foreach($supportMenu as $group)
                        <div>
                            <div class="text-[11px] uppercase font-black tracking-wide text-gray-700">{{ $group['heading'] }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $group['description'] }}</div>
                            <div class="mt-2 flex flex-col gap-1.5">
                                @foreach($group['links'] as $item)
                                    <a href="{{ $item['href'] }}" @if(!empty($item['external'])) target="_blank" rel="noreferrer" @endif @click="mobileOpen=false" class="text-sm text-gray-600 hover:text-gray-900">{{ $item['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-100 pt-3 flex flex-col gap-2">
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
