@extends('layouts.frontend')

@section('title', 'smbgen-core — Contact, Book, Pay, Client Portal, CRM, CMS')
@section('description', 'smbgen-core is the simple operating layer for growing businesses: contact capture, booking, payments, client portal access, CRM, and CMS in one connected product.')

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────────── --}}
<section class="bg-slate-950 py-20 md:py-28 px-6 relative overflow-hidden">

    {{-- Ambient glow --}}
    <div class="absolute top-0 left-1/4 w-[600px] h-[400px] bg-blue-600/8 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute inset-0 opacity-[0.025]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 48px 48px;"></div>

    <div class="max-w-6xl mx-auto relative">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- ── Left: copy ──────────────────────────────────────────── --}}
            <div>
                <div class="inline-flex items-center gap-2 bg-blue-600/15 text-blue-400 text-xs font-bold px-3.5 py-1.5 rounded-full mb-10 border border-blue-500/25 tracking-widest uppercase">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full inline-block animate-pulse"></span>
                    smbgen-core &bull; customer-facing operating layer
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-white leading-[1.05] tracking-tight mb-7">
                    The product layer<br>
                    <span class="text-blue-400">your customers use</span><br>
                    every day.
                </h1>

                <p class="text-lg text-slate-400 max-w-lg mb-11 font-light leading-relaxed">
                    smbgen-core turns your website into a working business system: better contact capture,
                    booking, payments, a real client portal, CRM visibility, and a CMS your team can use.
                </p>

                <div class="flex flex-wrap items-center gap-4 mb-14">
                    <a href="{{ route('solutions') }}#contact-core" class="bg-blue-600 text-white font-bold px-7 py-3.5 rounded-xl hover:bg-blue-500 transition-all text-base shadow-lg shadow-blue-900/30">
                        Explore smbgen-core &rarr;
                    </a>
                    <a href="#start-here" class="text-slate-400 font-semibold hover:text-white transition-colors text-base flex items-center gap-2">
                        See the six core pages <span>&darr;</span>
                    </a>
                </div>

                {{-- Social proof --}}
                <div class="pt-8 border-t border-slate-800 grid grid-cols-2 gap-5">
                    @foreach([
                        ['Contact to Close',     'Capture, book, bill, and serve from one system'],
                        ['Client-Ready UX',      'Simple paths customers understand immediately'],
                        ['Connected Operations', 'Portal, CRM, CMS, and payments stay in sync'],
                        ['Built to Grow',        'A clear core product with services layered around it'],
                    ] as [$title, $sub])
                        <div>
                            <div class="text-white text-sm font-bold">{{ $title }}</div>
                            <div class="text-slate-600 text-xs mt-0.5">{{ $sub }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Right: platform fabric SVG ──────────────────────────── --}}
            <div class="hidden md:flex items-center justify-center">
                <div class="relative w-full max-w-[480px] aspect-square" x-data="platformFabric()" x-init="init()">

                    {{-- SVG fabric --}}
                    <svg viewBox="0 0 480 480" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <defs>
                            {{-- Edge gradients --}}
                            <linearGradient id="eg-rb" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#ef4444" stop-opacity="0.6"/><stop offset="100%" stop-color="#3b82f6" stop-opacity="0.6"/></linearGradient>
                            <linearGradient id="eg-vb" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.6"/><stop offset="100%" stop-color="#3b82f6" stop-opacity="0.6"/></linearGradient>
                            <linearGradient id="eg-cb" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#06b6d4" stop-opacity="0.6"/><stop offset="100%" stop-color="#3b82f6" stop-opacity="0.6"/></linearGradient>
                            <linearGradient id="eg-ob" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#f97316" stop-opacity="0.6"/><stop offset="100%" stop-color="#3b82f6" stop-opacity="0.6"/></linearGradient>
                            <linearGradient id="eg-em" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#10b981" stop-opacity="0.6"/><stop offset="100%" stop-color="#3b82f6" stop-opacity="0.6"/></linearGradient>
                            <linearGradient id="eg-ib" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#6366f1" stop-opacity="0.6"/><stop offset="100%" stop-color="#3b82f6" stop-opacity="0.6"/></linearGradient>
                            {{-- Node glows --}}
                            <filter id="glow-blue"   x="-80%" y="-80%" width="260%" height="260%"><feGaussianBlur stdDeviation="8" result="blur"/><feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                            <filter id="glow-center" x="-100%" y="-100%" width="300%" height="300%"><feGaussianBlur stdDeviation="14" result="blur"/><feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                        </defs>

                        {{-- ── Edges (spokes from center to each product, plus ring) --}}
                        {{-- Centre → EXTREME (top-left) --}}
                        <line x1="240" y1="240" x2="108" y2="108" stroke="url(#eg-rb)" stroke-width="1.5" stroke-dasharray="6 4" opacity="0.5"/>
                        {{-- Centre → SIGNAL (top-right) --}}
                        <line x1="240" y1="240" x2="372" y2="108" stroke="url(#eg-vb)" stroke-width="1.5" stroke-dasharray="6 4" opacity="0.5"/>
                        {{-- Centre → RELAY (right) --}}
                        <line x1="240" y1="240" x2="420" y2="240" stroke="url(#eg-cb)" stroke-width="1.5" stroke-dasharray="6 4" opacity="0.5"/>
                        {{-- Centre → SURGE (bottom-right) --}}
                        <line x1="240" y1="240" x2="372" y2="372" stroke="url(#eg-ob)" stroke-width="1.5" stroke-dasharray="6 4" opacity="0.5"/>
                        {{-- Centre → CAST (bottom-left) --}}
                        <line x1="240" y1="240" x2="108" y2="372" stroke="url(#eg-em)" stroke-width="1.5" stroke-dasharray="6 4" opacity="0.5"/>
                        {{-- Centre → VAULT (left) --}}
                        <line x1="240" y1="240" x2="60" y2="240" stroke="url(#eg-ib)" stroke-width="1.5" stroke-dasharray="6 4" opacity="0.5"/>

                        {{-- Ring edges between adjacent products --}}
                        <line x1="108" y1="108" x2="372" y2="108" stroke="rgba(255,255,255,0.06)" stroke-width="1" stroke-dasharray="4 6"/>
                        <line x1="372" y1="108" x2="420" y2="240" stroke="rgba(255,255,255,0.06)" stroke-width="1" stroke-dasharray="4 6"/>
                        <line x1="420" y1="240" x2="372" y2="372" stroke="rgba(255,255,255,0.06)" stroke-width="1" stroke-dasharray="4 6"/>
                        <line x1="372" y1="372" x2="108" y2="372" stroke="rgba(255,255,255,0.06)" stroke-width="1" stroke-dasharray="4 6"/>
                        <line x1="108" y1="372" x2="60"  y2="240" stroke="rgba(255,255,255,0.06)" stroke-width="1" stroke-dasharray="4 6"/>
                        <line x1="60"  y1="240" x2="108" y2="108" stroke="rgba(255,255,255,0.06)" stroke-width="1" stroke-dasharray="4 6"/>

                        {{-- Cross-links for density --}}
                        <line x1="108" y1="108" x2="420" y2="240" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="3 8"/>
                        <line x1="372" y1="108" x2="60"  y2="240" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="3 8"/>
                        <line x1="108" y1="108" x2="372" y2="372" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="3 8"/>
                        <line x1="372" y1="108" x2="108" y2="372" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="3 8"/>

                        {{-- ── Animated data pulses (SVG circles on paths) --}}
                        {{-- EXTREME pulse --}}
                        <circle r="3" fill="#ef4444" opacity="0.9">
                            <animateMotion dur="2.8s" repeatCount="indefinite" begin="0s">
                                <mpath href="#path-extreme"/>
                            </animateMotion>
                            <animate attributeName="opacity" values="0;0.9;0" dur="2.8s" repeatCount="indefinite" begin="0s"/>
                        </circle>
                        {{-- SIGNAL pulse --}}
                        <circle r="3" fill="#8b5cf6" opacity="0.9">
                            <animateMotion dur="3.2s" repeatCount="indefinite" begin="0.6s">
                                <mpath href="#path-signal"/>
                            </animateMotion>
                            <animate attributeName="opacity" values="0;0.9;0" dur="3.2s" repeatCount="indefinite" begin="0.6s"/>
                        </circle>
                        {{-- RELAY pulse --}}
                        <circle r="3" fill="#06b6d4" opacity="0.9">
                            <animateMotion dur="2.5s" repeatCount="indefinite" begin="1.1s">
                                <mpath href="#path-relay"/>
                            </animateMotion>
                            <animate attributeName="opacity" values="0;0.9;0" dur="2.5s" repeatCount="indefinite" begin="1.1s"/>
                        </circle>
                        {{-- SURGE pulse --}}
                        <circle r="3" fill="#f97316" opacity="0.9">
                            <animateMotion dur="3.0s" repeatCount="indefinite" begin="0.3s">
                                <mpath href="#path-surge"/>
                            </animateMotion>
                            <animate attributeName="opacity" values="0;0.9;0" dur="3.0s" repeatCount="indefinite" begin="0.3s"/>
                        </circle>
                        {{-- CAST pulse --}}
                        <circle r="3" fill="#10b981" opacity="0.9">
                            <animateMotion dur="2.7s" repeatCount="indefinite" begin="1.8s">
                                <mpath href="#path-cast"/>
                            </animateMotion>
                            <animate attributeName="opacity" values="0;0.9;0" dur="2.7s" repeatCount="indefinite" begin="1.8s"/>
                        </circle>
                        {{-- VAULT pulse --}}
                        <circle r="3" fill="#6366f1" opacity="0.9">
                            <animateMotion dur="3.4s" repeatCount="indefinite" begin="0.9s">
                                <mpath href="#path-vault"/>
                            </animateMotion>
                            <animate attributeName="opacity" values="0;0.9;0" dur="3.4s" repeatCount="indefinite" begin="0.9s"/>
                        </circle>

                        {{-- Hidden paths for animateMotion --}}
                        <path id="path-extreme" d="M240,240 L108,108" visibility="hidden"/>
                        <path id="path-signal"  d="M240,240 L372,108" visibility="hidden"/>
                        <path id="path-relay"   d="M240,240 L420,240" visibility="hidden"/>
                        <path id="path-surge"   d="M240,240 L372,372" visibility="hidden"/>
                        <path id="path-cast"    d="M240,240 L108,372" visibility="hidden"/>
                        <path id="path-vault"   d="M240,240 L60,240"  visibility="hidden"/>

                        {{-- ── Centre node (smbgen) --}}
                        <circle cx="240" cy="240" r="28" fill="rgba(59,130,246,0.12)" stroke="rgba(59,130,246,0.35)" stroke-width="1.5" filter="url(#glow-center)"/>
                        <circle cx="240" cy="240" r="18" fill="rgba(59,130,246,0.2)" stroke="rgba(59,130,246,0.5)" stroke-width="1">
                            <animate attributeName="r" values="18;21;18" dur="3s" repeatCount="indefinite"/>
                            <animate attributeName="stroke-opacity" values="0.5;0.9;0.5" dur="3s" repeatCount="indefinite"/>
                        </circle>
                        <text x="240" y="237" text-anchor="middle" fill="white" font-size="7" font-family="Inter,sans-serif" font-weight="900" letter-spacing="1" opacity="0.9">smb</text>
                        <text x="240" y="247" text-anchor="middle" fill="#60a5fa" font-size="7" font-family="Inter,sans-serif" font-weight="900" letter-spacing="1" opacity="0.9">gen</text>

                        {{-- ── Product nodes --}}

                        {{-- EXTREME — top-left (108,108) --}}
                        <circle cx="108" cy="108" r="36" fill="rgba(239,68,68,0.07)" stroke="rgba(239,68,68,0.0)" stroke-width="0"/>
                        <circle cx="108" cy="108" r="24" fill="rgba(15,10,10,0.8)" stroke="rgba(239,68,68,0.45)" stroke-width="1.5">
                            <animate attributeName="stroke-opacity" values="0.45;0.8;0.45" dur="4s" repeatCount="indefinite" begin="0s"/>
                        </circle>
                        <text x="108" y="108" text-anchor="middle" fill="#f87171" font-size="7" font-family="Inter,sans-serif" font-weight="900" letter-spacing="0.8" opacity="0.95">CONTACT</text>
                        <text x="108" y="140" text-anchor="middle" fill="rgba(239,68,68,0.5)" font-size="5.5" font-family="Inter,sans-serif" font-weight="600" letter-spacing="0.5">Lead&nbsp;intake</text>

                        {{-- SIGNAL — top-right (372,108) --}}
                        <circle cx="372" cy="108" r="36" fill="rgba(139,92,246,0.07)" stroke="rgba(139,92,246,0.0)" stroke-width="0"/>
                        <circle cx="372" cy="108" r="24" fill="rgba(10,8,18,0.8)" stroke="rgba(139,92,246,0.45)" stroke-width="1.5">
                            <animate attributeName="stroke-opacity" values="0.45;0.8;0.45" dur="4s" repeatCount="indefinite" begin="0.7s"/>
                        </circle>
                        <text x="372" y="108" text-anchor="middle" fill="#c4b5fd" font-size="7" font-family="Inter,sans-serif" font-weight="900" letter-spacing="0.8" opacity="0.95">BOOK</text>
                        <text x="372" y="140" text-anchor="middle" fill="rgba(139,92,246,0.5)" font-size="5.5" font-family="Inter,sans-serif" font-weight="600" letter-spacing="0.5">Scheduling</text>

                        {{-- RELAY — right (420,240) --}}
                        <circle cx="420" cy="240" r="36" fill="rgba(6,182,212,0.07)" stroke="rgba(6,182,212,0.0)" stroke-width="0"/>
                        <circle cx="420" cy="240" r="24" fill="rgba(2,14,18,0.8)" stroke="rgba(6,182,212,0.45)" stroke-width="1.5">
                            <animate attributeName="stroke-opacity" values="0.45;0.8;0.45" dur="4s" repeatCount="indefinite" begin="1.4s"/>
                        </circle>
                        <text x="420" y="240" text-anchor="middle" fill="#67e8f9" font-size="7" font-family="Inter,sans-serif" font-weight="900" letter-spacing="0.8" opacity="0.95">PAY</text>
                        <text x="420" y="272" text-anchor="middle" fill="rgba(6,182,212,0.5)" font-size="5.5" font-family="Inter,sans-serif" font-weight="600" letter-spacing="0.5">Invoices</text>

                        {{-- SURGE — bottom-right (372,372) --}}
                        <circle cx="372" cy="372" r="36" fill="rgba(249,115,22,0.07)" stroke="rgba(249,115,22,0.0)" stroke-width="0"/>
                        <circle cx="372" cy="372" r="24" fill="rgba(14,6,0,0.8)" stroke="rgba(249,115,22,0.45)" stroke-width="1.5">
                            <animate attributeName="stroke-opacity" values="0.45;0.8;0.45" dur="4s" repeatCount="indefinite" begin="2.1s"/>
                        </circle>
                        <text x="372" y="369" text-anchor="middle" fill="#fdba74" font-size="6" font-family="Inter,sans-serif" font-weight="900" letter-spacing="0.8" opacity="0.95">PORTAL</text>
                        <text x="372" y="404" text-anchor="middle" fill="rgba(249,115,22,0.5)" font-size="5.5" font-family="Inter,sans-serif" font-weight="600" letter-spacing="0.5">Client&nbsp;access</text>

                        {{-- CAST — bottom-left (108,372) --}}
                        <circle cx="108" cy="372" r="36" fill="rgba(16,185,129,0.07)" stroke="rgba(16,185,129,0.0)" stroke-width="0"/>
                        <circle cx="108" cy="372" r="24" fill="rgba(2,14,8,0.8)" stroke="rgba(16,185,129,0.45)" stroke-width="1.5">
                            <animate attributeName="stroke-opacity" values="0.45;0.8;0.45" dur="4s" repeatCount="indefinite" begin="2.8s"/>
                        </circle>
                        <text x="108" y="372" text-anchor="middle" fill="#6ee7b7" font-size="7" font-family="Inter,sans-serif" font-weight="900" letter-spacing="0.8" opacity="0.95">CMS</text>
                        <text x="108" y="404" text-anchor="middle" fill="rgba(16,185,129,0.5)" font-size="5.5" font-family="Inter,sans-serif" font-weight="600" letter-spacing="0.5">Content&nbsp;ops</text>

                        {{-- VAULT — left (60,240) --}}
                        <circle cx="60" cy="240" r="36" fill="rgba(99,102,241,0.07)" stroke="rgba(99,102,241,0.0)" stroke-width="0"/>
                        <circle cx="60" cy="240" r="24" fill="rgba(5,6,15,0.8)" stroke="rgba(99,102,241,0.45)" stroke-width="1.5">
                            <animate attributeName="stroke-opacity" values="0.45;0.8;0.45" dur="4s" repeatCount="indefinite" begin="3.5s"/>
                        </circle>
                        <text x="60" y="240" text-anchor="middle" fill="#a5b4fc" font-size="7" font-family="Inter,sans-serif" font-weight="900" letter-spacing="0.8" opacity="0.95">CRM</text>
                        <text x="60" y="272" text-anchor="middle" fill="rgba(99,102,241,0.5)" font-size="5.5" font-family="Inter,sans-serif" font-weight="600" letter-spacing="0.5">Pipeline</text>
                    </svg>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── START HERE ───────────────────────────────────────────────────── --}}
<section id="start-here" class="bg-white py-20 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="max-w-3xl mb-12">
            <span class="text-blue-600 text-xs font-black uppercase tracking-[0.2em] mb-4 block">smbgen-core</span>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight mb-5">
                Six explanation pages people instantly understand.
            </h2>
            <p class="text-gray-600 text-lg leading-relaxed">
                No jargon. No internal product names. Start with the exact job people need done, in the exact order they naturally think about it.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @php
                $startHereItems = [
                    [
                        'step' => '01',
                        'title' => 'Contact',
                        'body' => 'A superior contact experience with structured intake, clearer qualification, and smarter routing than a generic form page.',
                        'href' => route('solutions') . '#contact-core',
                        'cta' => 'See contact page',
                        'pillClass' => 'bg-blue-100 text-blue-700',
                        'ctaClass' => 'group-hover:text-blue-700',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Book',
                        'body' => 'Scheduling that removes friction with availability, confirmations, reminders, and a smoother path from interest to appointment.',
                        'href' => route('solutions') . '#book-core',
                        'cta' => 'See booking page',
                        'pillClass' => 'bg-violet-100 text-violet-700',
                        'ctaClass' => 'group-hover:text-violet-700',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Pay',
                        'body' => 'A simpler payment experience that feels trustworthy and fast, with a cleaner handoff from approval to invoice to paid.',
                        'href' => route('solutions') . '#pay-core',
                        'cta' => 'See payments page',
                        'pillClass' => 'bg-emerald-100 text-emerald-700',
                        'ctaClass' => 'group-hover:text-emerald-700',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Client Portal',
                        'body' => 'One clear place for clients to log in, view files, track progress, manage billing, and stay aligned without extra back-and-forth.',
                        'href' => route('solutions') . '#portal-core',
                        'cta' => 'See portal page',
                        'pillClass' => 'bg-orange-100 text-orange-700',
                        'ctaClass' => 'group-hover:text-orange-700',
                    ],
                    [
                        'step' => '05',
                        'title' => 'CRM',
                        'body' => 'Track leads, conversations, deals, follow-ups, and customer history in one place so nothing falls through the cracks.',
                        'href' => route('solutions') . '#crm-core',
                        'cta' => 'See CRM',
                        'pillClass' => 'bg-indigo-100 text-indigo-700',
                        'ctaClass' => 'group-hover:text-indigo-700',
                    ],
                    [
                        'step' => '06',
                        'title' => 'CMS',
                        'body' => 'Update pages, publish offers, and manage content without turning every site change into a development ticket.',
                        'href' => route('solutions') . '#cms-core',
                        'cta' => 'See CMS',
                        'pillClass' => 'bg-cyan-100 text-cyan-700',
                        'ctaClass' => 'group-hover:text-cyan-700',
                    ],
                ];
            @endphp

            @foreach($startHereItems as $item)
                <a href="{{ $item['href'] }}" class="group rounded-3xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-7 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-xl hover:border-gray-300">
                    <div class="mb-5 flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-[0.25em] text-gray-400">{{ $item['step'] }}</span>
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.2em] {{ $item['pillClass'] }}">{{ $item['title'] }}</span>
                    </div>
                    <h3 class="mb-3 text-2xl font-black tracking-tight text-gray-900">{{ $item['title'] }}</h3>
                    <p class="mb-6 text-sm leading-relaxed text-gray-600">{{ $item['body'] }}</p>
                    <div class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 transition-colors {{ $item['ctaClass'] }}">
                        {{ $item['cta'] }}
                        <span>&rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ── PLATFORM OVERVIEW ─────────────────────────────────────────────── --}}
<section id="platform" class="bg-indigo-700 py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        <div>
            <span class="text-indigo-300 text-xs font-black uppercase tracking-[0.2em] mb-5 block">How smbgen-core works</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                One connected core instead of six disconnected tools.
            </h2>
            <p class="text-indigo-200 text-lg leading-relaxed mb-8">
                Contact, booking, payments, portal access, CRM, and CMS all live in the same operating layer.
                That means less friction for customers and less cleanup work for your team.
            </p>
            <div class="flex flex-col gap-3 mb-9">
                @foreach([
                    'Laravel &middot; Livewire &middot; Alpine.js &middot; Tailwind CSS',
                    'API design, integrations & event-driven architecture',
                    'AI-augmented workflows built-in, not bolted on',
                    'From idea to live product in 3&ndash;6 weeks',
                ] as $item)
                    <div class="flex items-start gap-3 text-white text-sm font-medium">
                        <span class="w-5 h-5 bg-indigo-500/60 rounded flex items-center justify-center text-indigo-200 text-xs shrink-0 mt-0.5">&#10003;</span>
                        <span>{!! $item !!}</span>
                    </div>
                @endforeach
            </div>
            <a href="/contact" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-bold px-5 py-3 rounded-xl hover:bg-indigo-50 transition-colors text-sm">
                Start a project &rarr;
            </a>
        </div>

        {{-- Code panel --}}
        <div class="bg-indigo-900/60 rounded-2xl p-8 border border-indigo-600/40 font-mono text-xs leading-relaxed">
            <div class="flex items-center gap-2 mb-5">
                <span class="w-3 h-3 rounded-full bg-red-400/70"></span>
                <span class="w-3 h-3 rounded-full bg-yellow-400/70"></span>
                <span class="w-3 h-3 rounded-full bg-green-400/70"></span>
                <span class="text-indigo-400 ml-2 text-[10px] tracking-widest uppercase">smbgen.platform</span>
            </div>
            <div class="space-y-1.5 text-indigo-300">
                <div><span class="text-blue-300">const</span> <span class="text-white">platform</span> = <span class="text-green-300">smbgen</span>.build({</div>
                <div class="pl-5"><span class="text-indigo-200">stack:</span>    <span class="text-yellow-300">'laravel + ai'</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">deploy:</span>   <span class="text-yellow-300">'cloud-native'</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">channels:</span> <span class="text-yellow-300">['web', 'social', 'email', 'leads']</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">ai:</span>       <span class="text-yellow-300">true</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">timeline:</span> <span class="text-yellow-300">'weeks, not months'</span>,</div>
                <div>});</div>
                <div class="mt-5 pt-4 border-t border-indigo-700/50 space-y-1.5">
                    <div class="text-green-400 font-semibold">&#10003; Build complete &mdash; deploying to prod</div>
                    <div class="text-indigo-400">&#9654; 7 modules &middot; 0 errors &middot; AI active</div>
                    <div class="text-blue-400">&#9654; Leads pipeline: active</div>
                    <div class="text-violet-400">&#9654; Social scheduler: running</div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ── CLOUD DELIVERY ────────────────────────────────────────────────── --}}
<section class="bg-slate-900 py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        {{-- Stats panel --}}
        <div class="bg-slate-800 rounded-2xl p-8 border border-slate-700 order-2 md:order-1">
            <div class="grid grid-cols-3 gap-4 mb-5">
                <div class="bg-blue-600/15 border border-blue-500/25 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-blue-400">99.9%</div>
                    <div class="text-slate-500 text-xs mt-1 font-medium">Uptime SLA</div>
                </div>
                <div class="bg-emerald-600/15 border border-emerald-500/25 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-emerald-400">&lt;50ms</div>
                    <div class="text-slate-500 text-xs mt-1 font-medium">Response</div>
                </div>
                <div class="bg-violet-600/15 border border-violet-500/25 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-violet-400">&#8734;</div>
                    <div class="text-slate-500 text-xs mt-1 font-medium">Scale</div>
                </div>
            </div>
            <div class="bg-slate-700/40 rounded-xl p-5">
                <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-3">Infrastructure Stack</div>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['AWS', 'Cloudflare', 'Docker', 'CI/CD', 'Auto-scale', 'Redis', 'Horizon', 'Queues'] as $tag)
                        <span class="bg-slate-700 text-slate-300 text-xs font-semibold px-3 py-1.5 rounded-lg">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="order-1 md:order-2">
            <span class="text-slate-500 text-xs font-black uppercase tracking-[0.2em] mb-5 block">02 &mdash; Cloud Delivery</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                Cloud-native. Globally distributed.
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed mb-8">
                Deploy anywhere on the planet. Auto-scaling infrastructure, blazing-fast edge delivery,
                and zero-downtime deployments — built for wherever your customers are.
            </p>
            <a href="/contact" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-5 py-3 rounded-xl hover:bg-blue-500 transition-colors text-sm">
                Explore infrastructure &rarr;
            </a>
        </div>

    </div>
</section>

{{-- ── PLATFORM MODULES GRID ─────────────────────────────────────────── --}}
<section id="modules" class="bg-gray-50 py-24 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Platform Modules</span>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight mb-4">One platform. All the tools.</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto font-light">
                Every module is deeply integrated. Data flows freely. AI works across everything.
                Nothing is siloed, nothing is bolted on.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">

            @foreach([
                [
                    'color'   => 'violet',
                    'icon'    => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',
                    'num'     => '03',
                    'label'   => 'Social Automation',
                    'title'   => 'Social Media at Scale',
                    'body'    => 'AI-generated posts, scheduled across platforms, with engagement analytics feeding back into your lead pipeline.',
                ],
                [
                    'color'   => 'cyan',
                    'icon'    => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    'num'     => '04',
                    'label'   => 'Email Marketing',
                    'title'   => 'Email That Converts',
                    'body'    => 'Broadcast campaigns, drip sequences, and AI-written copy. Deliverability monitoring included. Tight CRM integration out of the box.',
                ],
                [
                    'color'   => 'emerald',
                    'icon'    => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                    'num'     => '05',
                    'label'   => 'Lead Generation',
                    'title'   => 'Full-Funnel Lead Gen',
                    'body'    => 'SEO, paid, content strategy, and referral loops working in concert. Capture forms that feed directly into your CRM.',
                ],
                [
                    'color'   => 'orange',
                    'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    'num'     => '06',
                    'label'   => 'Lead Management',
                    'title'   => 'Built-In CRM',
                    'body'    => 'Contact management, deal tracking, automated follow-ups, and AI-powered lead scoring. Know exactly who to call next.',
                ],
                [
                    'color'   => 'blue',
                    'icon'    => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                    'num'     => '07',
                    'label'   => 'AI Content Engine',
                    'title'   => 'Content at Machine Speed',
                    'body'    => 'Blog posts, landing copy, email sequences, social captions — all generated, reviewed, and published from one AI-powered content hub.',
                ],
                [
                    'color'   => 'slate',
                    'icon'    => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                    'num'     => '08',
                    'label'   => 'Document Management',
                    'title'   => 'Project Files. Organised.',
                    'body'    => 'Secure client portals, document delivery, project tracking, and approval workflows. Every engagement, documented end-to-end.',
                ],
            ] as $module)
                @php $c = $module['color']; @endphp
                <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all flex flex-col">
                    <div class="w-10 h-10 bg-{{ $c }}-100 rounded-xl flex items-center justify-center mb-5 shrink-0">
                        <svg class="w-5 h-5 text-{{ $c }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-{{ $c }}-600 text-xs font-black uppercase tracking-[0.2em] mb-2 block">{{ $module['num'] }} &mdash; {{ $module['label'] }}</span>
                    <h3 class="text-lg font-black text-gray-900 mb-2 tracking-tight">{{ $module['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-1">{{ $module['body'] }}</p>
                </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ── AI DIFFERENTIATOR ─────────────────────────────────────────────── --}}
<section id="ai" class="bg-slate-950 py-24 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-slate-600 text-xs font-black uppercase tracking-[0.2em] mb-4 block">AI-Derived Intelligence</span>
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">
                Not AI features. An AI-native platform.
            </h2>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto font-light">
                smbgen&rsquo;s AI isn&rsquo;t a chatbot widget bolted to a legacy system.
                It&rsquo;s woven into every module — generating, optimising, scoring, and automating
                across your entire operation, 24/7.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['Generate', 'Blog posts, email copy, social captions, landing page content — produced at scale, tuned to your voice.', 'text-blue-400', 'bg-blue-600/10 border-blue-500/20'],
                ['Optimise', 'Every piece of content scored and improved for SEO, readability, and conversion before it goes out.', 'text-violet-400', 'bg-violet-600/10 border-violet-500/20'],
                ['Automate', 'Workflows that run themselves. Social posting, email sequences, lead follow-up — handled without manual intervention.', 'text-emerald-400', 'bg-emerald-600/10 border-emerald-500/20'],
                ['Score', 'AI ranks your leads by conversion probability so your team focuses energy where it matters most.', 'text-orange-400', 'bg-orange-600/10 border-orange-500/20'],
            ] as [$title, $body, $textColor, $bg])
                <div class="rounded-2xl p-7 border {{ $bg }}">
                    <div class="text-2xl font-black {{ $textColor }} mb-3">{{ $title }}</div>
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $body }}</p>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ── GROWTH SECTION ────────────────────────────────────────────────── --}}
<section id="growth" class="bg-orange-600 py-24 px-6">
    <div class="max-w-5xl mx-auto text-center">
        <span class="text-orange-200 text-xs font-black uppercase tracking-[0.2em] mb-6 block">Growth Engine</span>
        <h2 class="text-5xl md:text-6xl font-black text-white leading-tight tracking-tight mb-6 max-w-3xl mx-auto">
            Guerrilla growth for ambitious businesses.
        </h2>
        <p class="text-orange-100 text-xl max-w-2xl mx-auto mb-10 font-light leading-relaxed">
            Unconventional strategy. Aggressive execution. SEO, paid, content, partnerships, and viral loops —
            the full growth playbook deployed on one AI-powered platform.
        </p>
        <div class="flex flex-wrap justify-center gap-3 mb-12">
            @foreach(['SEO Domination', 'Paid Acquisition', 'Content Strategy', 'Partnership Growth', 'Viral Loops', 'Guerrilla Tactics', 'Social Automation', 'Email Nurturing'] as $tag)
                <span class="bg-orange-500/70 border border-orange-400/50 text-white text-sm font-bold px-4 py-2 rounded-full">{{ $tag }}</span>
            @endforeach
        </div>
        <a href="/contact" class="inline-flex items-center gap-2 bg-white text-orange-700 font-black px-8 py-4 rounded-xl hover:bg-orange-50 transition-colors text-base shadow-xl shadow-orange-900/20">
            Let&rsquo;s talk growth &rarr;
        </a>
    </div>
</section>

{{-- ── FINAL CTA ─────────────────────────────────────────────────────── --}}
<section class="bg-slate-950 py-28 px-6">
    <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 bg-blue-600/15 text-blue-400 text-xs font-bold px-3.5 py-1.5 rounded-full mb-8 border border-blue-500/25 tracking-widest uppercase">
            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full inline-block"></span>
            Ready when you are
        </div>
        <h2 class="text-5xl md:text-6xl font-black text-white tracking-tight mb-6 leading-tight">
            Ready to build<br>something great?
        </h2>
        <p class="text-slate-400 text-xl mb-11 max-w-xl mx-auto font-light leading-relaxed">
            Start on the platform, engage the services, or just get in touch. We move fast.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}" class="bg-blue-600 text-white font-bold px-8 py-4 rounded-xl hover:bg-blue-500 transition-colors text-base shadow-lg shadow-blue-900/40">
                Start for free &rarr;
            </a>
            <a href="{{ route('home.services') }}" class="border border-slate-700 text-slate-300 font-bold px-8 py-4 rounded-xl hover:border-slate-500 hover:text-white transition-colors text-base">
                View services
            </a>
        </div>
    </div>
</section>

@endsection
