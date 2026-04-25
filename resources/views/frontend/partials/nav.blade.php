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
            'description' => 'Solutions tailored to your market',
            'links' => [
                ['label' => 'All Industries', 'href' => Route::has('industries.index') ? route('industries.index') : route('contact')],
                ['label' => 'Real Estate Agents', 'href' => Route::has('industries.real-estate') ? route('industries.real-estate') : route('contact')],
                ['label' => 'Home Service Pros', 'href' => Route::has('industries.home-services') ? route('industries.home-services') : route('contact')],
                ['label' => 'Legal & Law Firms', 'href' => Route::has('industries.legal') ? route('industries.legal') : route('contact')],
                ['label' => 'Health & Wellness', 'href' => Route::has('industries.health-wellness') ? route('industries.health-wellness') : route('contact')],
                ['label' => 'Consultants & Advisors', 'href' => Route::has('industries.consulting') ? route('industries.consulting') : route('contact')],
            ],
        ],
        [
            'heading' => 'By Solutions',
            'description' => 'Start from business bottlenecks and outcomes',
            'links' => [
                ['label' => 'All Solution Areas', 'href' => Route::has('solutions.areas') ? route('solutions.areas') : route('contact')],
                ['label' => 'Get More Leads', 'href' => Route::has('solutions.more-leads') ? route('solutions.more-leads') : route('contact')],
                ['label' => 'Streamline Bookings', 'href' => Route::has('solutions.streamline-bookings') ? route('solutions.streamline-bookings') : route('contact')],
                ['label' => 'Get Paid Faster', 'href' => Route::has('solutions.get-paid-faster') ? route('solutions.get-paid-faster') : route('contact')],
                ['label' => 'Retain Clients', 'href' => Route::has('solutions.retain-clients') ? route('solutions.retain-clients') : route('contact')],
                ['label' => 'Grow Through Referrals', 'href' => Route::has('solutions.grow-referrals') ? route('solutions.grow-referrals') : route('contact')],
                ['label' => 'Book a Solutions Call', 'href' => Route::has('booking.wizard') ? route('booking.wizard') . '?intent=solutions' : route('contact')],
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
            open: false,
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
                Pro Solutions
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
                @mouseenter="openPanel('open')"
                @focus="openPanel('open')"
                @click="togglePanel('open')"
                class="inline-flex items-center gap-1.5 hover:text-gray-900 transition-colors"
                :class="activePanel === 'open' ? 'text-gray-900' : ''"
            >
                <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.648.5.5 5.648.5 12c0 5.084 3.292 9.396 7.86 10.918.575.106.785-.25.785-.556 0-.274-.01-1-.015-1.962-3.197.694-3.872-1.54-3.872-1.54-.523-1.328-1.277-1.682-1.277-1.682-1.044-.714.079-.699.079-.699 1.154.08 1.761 1.186 1.761 1.186 1.026 1.758 2.693 1.25 3.35.956.104-.743.402-1.25.731-1.537-2.552-.29-5.236-1.276-5.236-5.68 0-1.255.449-2.282 1.184-3.086-.119-.29-.513-1.459.112-3.042 0 0 .966-.31 3.166 1.179A10.98 10.98 0 0 1 12 6.032c.977.005 1.961.132 2.881.387 2.198-1.49 3.163-1.18 3.163-1.18.627 1.584.233 2.753.114 3.043.737.804 1.182 1.83 1.182 3.086 0 4.415-2.688 5.387-5.25 5.671.413.355.781 1.055.781 2.126 0 1.536-.014 2.774-.014 3.151 0 .309.207.668.79.555C20.21 21.392 23.5 17.082 23.5 12 23.5 5.648 18.352.5 12 .5Z"/></svg>
                Free &amp; Open
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
            <a href="tel:2406067443" data-magnetic class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-green-600 transition-colors hover:border-gray-300 hover:text-gray-900">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                Let's talk solutions
            </a>
            <a href="https://github.com/smbgen" data-magnetic target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 transition-colors hover:border-gray-300 hover:text-gray-900">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 .5C5.648.5.5 5.648.5 12c0 5.084 3.292 9.396 7.86 10.918.575.106.785-.25.785-.556 0-.274-.01-1-.015-1.962-3.197.694-3.872-1.54-3.872-1.54-.523-1.328-1.277-1.682-1.277-1.682-1.044-.714.079-.699.079-.699 1.154.08 1.761 1.186 1.761 1.186 1.026 1.758 2.693 1.25 3.35.956.104-.743.402-1.25.731-1.537-2.552-.29-5.236-1.276-5.236-5.68 0-1.255.449-2.282 1.184-3.086-.119-.29-.513-1.459.112-3.042 0 0 .966-.31 3.166 1.179A10.98 10.98 0 0 1 12 6.032c.977.005 1.961.132 2.881.387 2.198-1.49 3.163-1.18 3.163-1.18.627 1.584.233 2.753.114 3.043.737.804 1.182 1.83 1.182 3.086 0 4.415-2.688 5.387-5.25 5.671.413.355.781 1.055.781 2.126 0 1.536-.014 2.774-.014 3.151 0 .309.207.668.79.555C20.21 21.392 23.5 17.082 23.5 12 23.5 5.648 18.352.5 12 .5Z"/>
                </svg>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                    Dashboard &rarr;
                </a>
            @else
                <a href="https://smbgen-construction-co-demo.on-forge.com/demo" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    Give it a try
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

            <template x-if="activePanel === 'open'">
                <div class="grid grid-cols-3 gap-6">

                    {{-- Left: unified card --}}
                    <div class="col-span-1 rounded-2xl border border-emerald-200 overflow-hidden flex flex-col">

                        {{-- Emerald section --}}
                        <div class="bg-gradient-to-br from-emerald-50 to-slate-50 p-5 flex-1">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.648.5.5 5.648.5 12c0 5.084 3.292 9.396 7.86 10.918.575.106.785-.25.785-.556 0-.274-.01-1-.015-1.962-3.197.694-3.872-1.54-3.872-1.54-.523-1.328-1.277-1.682-1.277-1.682-1.044-.714.079-.699.079-.699 1.154.08 1.761 1.186 1.761 1.186 1.026 1.758 2.693 1.25 3.35.956.104-.743.402-1.25.731-1.537-2.552-.29-5.236-1.276-5.236-5.68 0-1.255.449-2.282 1.184-3.086-.119-.29-.513-1.459.112-3.042 0 0 .966-.31 3.166 1.179A10.98 10.98 0 0 1 12 6.032c.977.005 1.961.132 2.881.387 2.198-1.49 3.163-1.18 3.163-1.18.627 1.584.233 2.753.114 3.043.737.804 1.182 1.83 1.182 3.086 0 4.415-2.688 5.387-5.25 5.671.413.355.781 1.055.781 2.126 0 1.536-.014 2.774-.014 3.151 0 .309.207.668.79.555C20.21 21.392 23.5 17.082 23.5 12 23.5 5.648 18.352.5 12 .5Z"/></svg>
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Free &amp; Open Source</h3>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed mb-4">Enterprise-grade web presence tools, freely available. Built in the open so every small business can compete on equal footing.</p>
                            <div class="flex flex-col gap-2">
                                <a href="https://github.com/smbgen" target="_blank" rel="noreferrer" class="flex items-center gap-2 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.648.5.5 5.648.5 12c0 5.084 3.292 9.396 7.86 10.918.575.106.785-.25.785-.556 0-.274-.01-1-.015-1.962-3.197.694-3.872-1.54-3.872-1.54-.523-1.328-1.277-1.682-1.277-1.682-1.044-.714.079-.699.079-.699 1.154.08 1.761 1.186 1.761 1.186 1.026 1.758 2.693 1.25 3.35.956.104-.743.402-1.25.731-1.537-2.552-.29-5.236-1.276-5.236-5.68 0-1.255.449-2.282 1.184-3.086-.119-.29-.513-1.459.112-3.042 0 0 .966-.31 3.166 1.179A10.98 10.98 0 0 1 12 6.032c.977.005 1.961.132 2.881.387 2.198-1.49 3.163-1.18 3.163-1.18.627 1.584.233 2.753.114 3.043.737.804 1.182 1.83 1.182 3.086 0 4.415-2.688 5.387-5.25 5.671.413.355.781 1.055.781 2.126 0 1.536-.014 2.774-.014 3.151 0 .309.207.668.79.555C20.21 21.392 23.5 17.082 23.5 12 23.5 5.648 18.352.5 12 .5Z"/></svg>
                                    smbgen on GitHub &rarr;
                                </a>
                                <a href="https://github.com/smbgen" target="_blank" rel="noreferrer" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">Browse repositories</a>
                                <a href="https://github.com/smbgen" target="_blank" rel="noreferrer" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">Open an issue / contribute</a>
                            </div>
                        </div>

                        {{-- Pledge section --}}
                        <div class="bg-gradient-to-br from-violet-50 to-purple-50 border-t border-violet-200/60 px-5 py-4">
                            <div class="flex items-center gap-1.5 mb-2">
                                <span class="text-sm">✌️</span>
                                <span class="text-[10px] font-black uppercase tracking-[0.18em] text-violet-500">Our Pledge</span>
                            </div>
                            <p class="text-[11px] text-violet-700 leading-relaxed">We believe access to great software is a human right. Every small business deserves the same tools as the Fortune 500 — without the price tag.</p>
                        </div>

                    </div>

                    {{-- Right: Learn & Build --}}
                    <div class="col-span-2 flex flex-col gap-4 pl-6 border-l border-gray-100">
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-0.5">Learn &amp; Build Your Own</p>
                            <p class="text-xs text-gray-400">Everything you need to fork, extend, or build on top of smbgen.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([
                                ['M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'Documentation', 'Setup guides, API references, and architecture docs.', 'https://github.com/smbgen'],
                                ['M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'Starter Template', 'Clone the repo and be running locally in under 5 minutes.', 'https://github.com/smbgen'],
                                ['M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'Module Library', 'Drop-in modules — Contact, Book, Pay, CRM, and more.', 'https://github.com/smbgen'],
                                ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'Community & Issues', 'Ask questions, request features, or open a PR.', 'https://github.com/smbgen'],
                            ] as [$icon, $label, $desc, $href])
                                <a href="{{ $href }}" target="_blank" rel="noreferrer"
                                   class="flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50 hover:bg-white hover:border-gray-200 hover:shadow-sm transition-all p-3.5 group">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-emerald-200 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800 group-hover:text-gray-900">{{ $label }}</p>
                                        <p class="text-[11px] text-gray-400 leading-relaxed mt-0.5">{{ $desc }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

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

            <a href="tel:2406067443" class="flex items-center justify-center gap-2 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                (240) 606-7443
            </a>

            <div class="rounded-xl border border-emerald-200 bg-emerald-50">
                <button @click="toggleMobileSection('open')" class="w-full flex items-center justify-between px-4 py-3 text-left text-sm font-bold text-gray-800">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.648.5.5 5.648.5 12c0 5.084 3.292 9.396 7.86 10.918.575.106.785-.25.785-.556 0-.274-.01-1-.015-1.962-3.197.694-3.872-1.54-3.872-1.54-.523-1.328-1.277-1.682-1.277-1.682-1.044-.714.079-.699.079-.699 1.154.08 1.761 1.186 1.761 1.186 1.026 1.758 2.693 1.25 3.35.956.104-.743.402-1.25.731-1.537-2.552-.29-5.236-1.276-5.236-5.68 0-1.255.449-2.282 1.184-3.086-.119-.29-.513-1.459.112-3.042 0 0 .966-.31 3.166 1.179A10.98 10.98 0 0 1 12 6.032c.977.005 1.961.132 2.881.387 2.198-1.49 3.163-1.18 3.163-1.18.627 1.584.233 2.753.114 3.043.737.804 1.182 1.83 1.182 3.086 0 4.415-2.688 5.387-5.25 5.671.413.355.781 1.055.781 2.126 0 1.536-.014 2.774-.014 3.151 0 .309.207.668.79.555C20.21 21.392 23.5 17.082 23.5 12 23.5 5.648 18.352.5 12 .5Z"/></svg>
                        Free &amp; Open Source
                    </span>
                    <span x-text="mobileSections.open ? '−' : '+'"></span>
                </button>
                <div x-show="mobileSections.open" x-transition class="px-4 pb-4 pt-1 flex flex-col gap-2">
                    <p class="text-xs text-gray-500 mb-2">Enterprise-grade tools, free for every small business.</p>
                    <a href="https://github.com/smbgen" target="_blank" rel="noreferrer" @click="mobileOpen=false" class="text-sm font-semibold text-gray-700 hover:text-gray-900">smbgen on GitHub &rarr;</a>
                    <a href="https://github.com/smbgen" target="_blank" rel="noreferrer" @click="mobileOpen=false" class="text-sm text-gray-600 hover:text-gray-900">Browse repositories</a>
                    <a href="https://github.com/smbgen" target="_blank" rel="noreferrer" @click="mobileOpen=false" class="text-sm text-gray-600 hover:text-gray-900">Contribute</a>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-3 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white text-sm font-bold px-4 py-2.5 rounded-lg text-center">
                        Dashboard
                    </a>
                @else
                    <a href="https://smbgen-construction-co-demo.on-forge.com/demo" class="text-sm font-medium text-gray-600 text-center py-2">Give it a try</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white text-sm font-bold px-4 py-2.5 rounded-lg text-center">
                        Get started &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
