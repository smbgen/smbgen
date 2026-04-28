@extends('layouts.frontend')

@section('title', 'smbgen — Web Presence That Converts')
@section('description', 'smbgen gives small and mid-market businesses one connected platform to capture leads, book clients, collect payments, manage relationships, and grow — built specifically for your industry.')

@push('head')
<style>
    .hp-hero-bg {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background:
            radial-gradient(1200px 560px at 88% -12%, rgba(79,70,229,0.20) 0%, rgba(79,70,229,0.00) 62%),
            radial-gradient(860px 460px at -8% 94%, rgba(16,185,129,0.14) 0%, rgba(16,185,129,0.00) 70%),
            radial-gradient(760px 420px at 100% 66%, rgba(56,189,248,0.12) 0%, rgba(56,189,248,0.00) 66%),
            linear-gradient(160deg, #f8fbff 0%, #eef4ff 44%, #eaf7ff 100%);
    }
    .hp-hero-bg::before {
        content: '';
        position: absolute;
        inset: -40% -20% auto;
        height: 120%;
        background: conic-gradient(from 180deg at 50% 50%, rgba(99,102,241,0.16), rgba(6,182,212,0.08), rgba(16,185,129,0.12), rgba(99,102,241,0.16));
        filter: blur(80px);
        opacity: 0.62;
        animation: hpDrift 20s linear infinite;
        pointer-events: none;
        z-index: 0;
    }
    .hp-hero-orb {
        position: absolute;
        border-radius: 9999px;
        filter: blur(32px);
        opacity: 0.34;
        pointer-events: none;
        z-index: 0;
    }
    .hp-hero-orb-a {
        width: 22rem;
        height: 22rem;
        background: radial-gradient(circle at 30% 30%, rgba(129,140,248,0.64), rgba(79,70,229,0.04));
        top: 8%;
        right: 4%;
        animation: hpFloatA 13s ease-in-out infinite;
    }
    .hp-hero-orb-b {
        width: 18rem;
        height: 18rem;
        background: radial-gradient(circle at 30% 30%, rgba(52,211,153,0.52), rgba(16,185,129,0.03));
        bottom: 2%;
        left: -4%;
        animation: hpFloatB 11s ease-in-out infinite;
    }
    .hp-hero-shell {
        position: relative;
        z-index: 1;
    }
    .hp-float-card {
        animation: hpCardFloat 8s ease-in-out infinite;
    }
    @keyframes hpDrift {
        0% { transform: translate3d(0, 0, 0) rotate(0deg); }
        100% { transform: translate3d(0, 0, 0) rotate(360deg); }
    }
    @keyframes hpFloatA {
        0%, 100% { transform: translate3d(0, 0, 0); }
        50% { transform: translate3d(22px, -14px, 0); }
    }
    @keyframes hpFloatB {
        0%, 100% { transform: translate3d(0, 0, 0); }
        50% { transform: translate3d(-18px, 16px, 0); }
    }
    @keyframes hpCardFloat {
        0%, 100% { transform: translate3d(0, 0, 0); }
        50% { transform: translate3d(0, -8px, 0); }
    }
    @media (prefers-reduced-motion: reduce) {
        .hp-hero-bg::before,
        .hp-hero-orb,
        .hp-float-card {
            animation: none;
        }
    }

    .hp-gradient-headline {
        background: linear-gradient(135deg, #1e1b4b 0%, #4f46e5 46%, #0284c7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hp-industry-hover {
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .hp-industry-hover:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')

{{-- ================================================================ --}}
{{-- HERO                                                              --}}
{{-- ================================================================ --}}
<section class="hp-hero-bg min-h-[92vh] flex items-center px-6">
    <div class="hp-hero-orb hp-hero-orb-a" aria-hidden="true"></div>
    <div class="hp-hero-orb hp-hero-orb-b" aria-hidden="true"></div>
    <div class="hp-hero-shell max-w-6xl mx-auto w-full py-20 grid lg:grid-cols-2 gap-12 xl:gap-20 items-center">

        {{-- Left: copy --}}
        <div data-reveal="left">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-indigo-200 bg-white/70 text-slate-600 text-xs font-bold uppercase tracking-widest mb-10 backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse inline-block"></span>
                Built for small &amp; mid-market businesses
            </div>

            <h1 class="text-5xl md:text-6xl xl:text-7xl font-black leading-[1.02] tracking-tight mb-8">
                <span class="text-slate-900">Web Presence</span><br>
                <span class="hp-gradient-headline">That Converts.</span>
            </h1>

            <p class="text-slate-600 text-lg md:text-xl mb-10 leading-relaxed font-light">
                One platform where your leads come in, appointments get booked, payments are collected,
                and client relationships are managed — without juggling six different tools.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 mb-10" data-reveal>
                <a href="https://smbgen-construction-co-demo.on-forge.com/demo"
                   class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition-colors shadow-xl shadow-indigo-900/30 border border-indigo-500/40"
                   data-magnetic>
                    Give it a try &rarr;
                </a>
                <a href="{{ Route::has('booking.wizard') ? route('booking.wizard') : route('contact') }}"
                   class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-white/70 hover:bg-white text-slate-700 font-semibold text-sm border border-slate-200 transition-colors shadow-sm">
                    Book a discovery call
                </a>
            </div>

            {{-- Journey tags --}}
            <div class="mb-8" data-reveal>
                @php
                    $journeyGroups = [
                        [
                            'label' => 'Attract',
                            'stageClass' => 'text-blue-700',
                            'steps' => [
                                ['Lead', 'bg-blue-50 border-blue-200 text-blue-800'],
                                ['Nurture', 'bg-violet-50 border-violet-200 text-violet-800'],
                            ],
                        ],
                        [
                            'label' => 'Convert',
                            'stageClass' => 'text-indigo-700',
                            'steps' => [
                                ['Propose', 'bg-indigo-50 border-indigo-200 text-indigo-800'],
                                ['Close', 'bg-cyan-50 border-cyan-200 text-cyan-800'],
                            ],
                        ],
                        [
                            'label' => 'Fulfill',
                            'stageClass' => 'text-emerald-700',
                            'steps' => [
                                ['Pay', 'bg-emerald-50 border-emerald-200 text-emerald-800'],
                                ['Deliver', 'bg-orange-50 border-orange-200 text-orange-800'],
                            ],
                        ],
                        [
                            'label' => 'Expand',
                            'stageClass' => 'text-amber-700',
                            'steps' => [
                                ['Retain', 'bg-amber-50 border-amber-200 text-amber-800'],
                                ['Refer', 'bg-yellow-50 border-yellow-200 text-yellow-800'],
                            ],
                        ],
                    ];
                @endphp
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($journeyGroups as $groupIndex => $group)
                        <div class="rounded-2xl border border-slate-200 bg-white/70 px-3 py-3 backdrop-blur-sm h-full min-w-0">
                            <div class="mb-2 text-[10px] font-black uppercase tracking-[0.16em] {{ $group['stageClass'] }} text-center">
                                {{ $group['label'] }}
                            </div>
                            <div class="flex flex-wrap items-center justify-center gap-2 min-h-[40px]">
                                @foreach($group['steps'] as $stepIndex => [$label, $cls])
                                    <span class="inline-flex min-w-[82px] items-center justify-center px-3 py-1.5 rounded-xl border {{ $cls }} text-xs font-bold leading-tight text-center">{{ $label }}</span>
                                    @if($stepIndex < count($group['steps']) - 1)
                                        <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: rotating booking form mockup --}}
        @php
            $heroForms = [
                [
                    'industry' => 'Real Estate',
                    'color'    => 'emerald',
                    'badge'    => 'bg-emerald-500/20 border-emerald-500/30 text-emerald-300',
                    'dot'      => 'bg-emerald-400',
                    'btn'      => 'bg-emerald-600 hover:bg-emerald-500',
                    'title'    => 'Schedule a Showing',
                    'subtitle' => "We'll confirm within 2 hours.",
                    'fields'   => [
                        ['label' => 'Property Address', 'value' => '142 Maple Drive, Rockville MD'],
                        ['label' => 'Preferred Date & Time', 'value' => 'Tomorrow — afternoon'],
                        ['label' => 'Your Name', 'value' => 'Sarah Johnson'],
                    ],
                    'cta' => 'Request Showing',
                ],
                [
                    'industry' => 'Home Services',
                    'color'    => 'orange',
                    'badge'    => 'bg-orange-500/20 border-orange-500/30 text-orange-300',
                    'dot'      => 'bg-orange-400',
                    'btn'      => 'bg-orange-600 hover:bg-orange-500',
                    'title'    => 'Book a Service Call',
                    'subtitle' => 'Same-day availability in your area.',
                    'fields'   => [
                        ['label' => 'Service Type', 'value' => 'HVAC Repair'],
                        ['label' => 'Service Address', 'value' => '78 Oak Street, Frederick MD'],
                        ['label' => 'Best Time', 'value' => 'Weekday morning, flexible'],
                    ],
                    'cta' => 'Book Now',
                ],
                [
                    'industry' => 'Legal',
                    'color'    => 'blue',
                    'badge'    => 'bg-blue-500/20 border-blue-500/30 text-blue-300',
                    'dot'      => 'bg-blue-400',
                    'btn'      => 'bg-blue-600 hover:bg-blue-500',
                    'title'    => 'Request a Consultation',
                    'subtitle' => 'Confidential. No obligation.',
                    'fields'   => [
                        ['label' => 'Matter Type', 'value' => 'Business Contract Review'],
                        ['label' => 'Brief Description', 'value' => 'Reviewing a partnership agreement'],
                        ['label' => 'Preferred Time', 'value' => 'This week, flexible'],
                    ],
                    'cta' => 'Request Consultation',
                ],
                [
                    'industry' => 'Health & Wellness',
                    'color'    => 'pink',
                    'badge'    => 'bg-pink-500/20 border-pink-500/30 text-pink-300',
                    'dot'      => 'bg-pink-400',
                    'btn'      => 'bg-pink-600 hover:bg-pink-500',
                    'title'    => 'Book an Appointment',
                    'subtitle' => 'New and returning clients welcome.',
                    'fields'   => [
                        ['label' => 'Service', 'value' => 'Deep Tissue Massage — 60 min'],
                        ['label' => 'Provider', 'value' => 'Any available'],
                        ['label' => 'Preferred Date', 'value' => 'Saturday morning'],
                    ],
                    'cta' => 'Book Appointment',
                ],
                [
                    'industry' => 'Consulting',
                    'color'    => 'violet',
                    'badge'    => 'bg-violet-500/20 border-violet-500/30 text-violet-300',
                    'dot'      => 'bg-violet-400',
                    'btn'      => 'bg-violet-600 hover:bg-violet-500',
                    'title'    => 'Schedule a Discovery Call',
                    'subtitle' => '30 minutes. No prep required.',
                    'fields'   => [
                        ['label' => 'Company Name', 'value' => 'Apex Growth Partners'],
                        ['label' => 'Primary Challenge', 'value' => 'Scaling our sales process'],
                        ['label' => 'Team Size', 'value' => '10–50 employees'],
                    ],
                    'cta' => 'Schedule Call',
                ],
            ];
        @endphp

        <div
            class="hidden lg:flex flex-col hp-float-card"
            data-reveal="right"
            x-data="{
                current: 0,
                total: {{ count($heroForms) }},
                init() { setInterval(() => { this.current = (this.current + 1) % this.total }, 3800) }
            }"
        >
            {{-- Card stack --}}
            <div class="relative" style="min-height: 340px;">
                @foreach($heroForms as $idx => $form)
                    <div
                        x-show="current === {{ $idx }}"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-3"
                        class="absolute inset-0"
                    >
                        {{-- Browser chrome card --}}
                        <div class="bg-[#0d1829] border border-white/10 rounded-2xl shadow-2xl overflow-hidden">

                            {{-- Chrome bar --}}
                            <div class="bg-white/5 border-b border-white/8 px-4 py-3 flex items-center gap-3">
                                <div class="flex gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-red-500/60"></span>
                                    <span class="w-3 h-3 rounded-full bg-yellow-500/60"></span>
                                    <span class="w-3 h-3 rounded-full bg-green-500/60"></span>
                                </div>
                                <div class="flex-1 bg-white/5 rounded-md px-3 py-1 text-[11px] text-gray-500 font-mono">
                                    yourbusiness.smbgen.com/book
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg border {{ $form['badge'] }} shrink-0">
                                    {{ $form['industry'] }}
                                </span>
                            </div>

                            {{-- Form body --}}
                            <div class="px-6 py-6">
                                <p class="text-white font-black text-lg mb-0.5">{{ $form['title'] }}</p>
                                <p class="text-gray-500 text-xs mb-5">{{ $form['subtitle'] }}</p>

                                <div class="flex flex-col gap-4">
                                    @foreach($form['fields'] as $field)
                                        <div>
                                            <label class="text-gray-400 text-xs font-semibold block mb-1.5">{{ $field['label'] }}</label>
                                            <div class="bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-sm text-gray-300 font-light">
                                                {{ $field['value'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="mt-5 w-full {{ $form['btn'] }} text-white font-bold text-sm py-3 rounded-xl transition-colors pointer-events-none">
                                    {{ $form['cta'] }} &rarr;
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Dot indicators + label --}}
            <div class="flex items-center gap-3 mt-6 px-1">
                @foreach($heroForms as $idx => $form)
                    <button
                        type="button"
                        @click="current = {{ $idx }}"
                        class="transition-all duration-300 rounded-full"
                        :class="current === {{ $idx }} ? 'w-5 h-2 {{ $form['dot'] }}' : 'w-2 h-2 bg-white/20 hover:bg-white/40'"
                    ></button>
                @endforeach
                <span class="text-gray-600 text-xs ml-2">
                    @foreach($heroForms as $idx => $form)
                        <span x-show="current === {{ $idx }}" class="font-semibold text-gray-400">{{ $form['industry'] }}</span>
                    @endforeach
                    &mdash; industry-specific by default
                </span>
            </div>
        </div>

    </div>
</section>

{{-- ================================================================ --}}
{{-- THE CENTRAL HUB PITCH                                            --}}
{{-- ================================================================ --}}
<section id="platform" class="bg-[#060e1a] py-24 px-6 border-t border-white/5">
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            <div>
                <span class="text-indigo-400 text-xs font-black uppercase tracking-[0.2em] mb-5 block">One Connected Platform</span>
                <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                    Every client interaction.<br>One place.
                </h2>
                <p class="text-gray-300 text-lg leading-relaxed mb-6">
                    Most small businesses run on a patchwork of tools that don't talk to each other — a form here, a calendar there, an invoice in email, files in Google Drive. Every gap between tools is a place where leads leak, clients get frustrated, and time gets wasted.
                </p>
                <p class="text-gray-400 text-lg leading-relaxed mb-10">
                    smbgen closes every gap. From the moment a prospect fills out your contact form to the moment they refer their first colleague, every interaction runs through the same connected system.
                </p>
                <div class="space-y-3">
                    @foreach([
                        ['indigo', 'Every lead captured, qualified, and routed into your CRM automatically'],
                        ['blue', 'Bookings live in your calendar — no manual entry, no back-and-forth'],
                        ['emerald', 'Payments collected the moment work is delivered — not 30 days later'],
                        ['orange', 'Clients have one private portal: files, messages, invoices, history'],
                    ] as [$col, $text])
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-lg bg-{{ $col }}-600/20 border border-{{ $col }}-600/30 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-{{ $col }}-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            <span class="text-gray-300 text-sm leading-relaxed">{{ $text }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Communication flow diagram --}}
            <div class="bg-white/3 border border-white/8 rounded-2xl p-6 space-y-3">
                <div class="flex items-center justify-between mb-2 pb-4 border-b border-white/8">
                    <p class="text-white font-bold text-sm">All Channels. One Dashboard.</p>
                    <span class="flex items-center gap-1.5 text-emerald-400 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        Live
                    </span>
                </div>
                @foreach([
                    ['M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'Website Contact Form', 'New lead — routed to CRM', 'blue'],
                    ['M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'Online Booking', 'Confirmed — in Google Calendar', 'violet'],
                    ['M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'Invoice & Payment', '$2,400 collected — instant', 'emerald'],
                    ['M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z', 'Client Portal Message', '1 unread — reply in context', 'orange'],
                    ['M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'CRM Follow-up', 'Referral scheduled — 30 days', 'indigo'],
                ] as [$icon, $label, $status, $col])
                    <div class="flex items-center gap-3 bg-white/4 rounded-xl px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-{{ $col }}-600/20 border border-{{ $col }}-600/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-{{ $col }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-xs font-semibold truncate">{{ $label }}</p>
                            <p class="text-gray-500 text-[11px] truncate">{{ $status }}</p>
                        </div>
                        <div class="w-1.5 h-1.5 rounded-full bg-{{ $col }}-400 shrink-0"></div>
                    </div>
                @endforeach
                <div class="pt-3 border-t border-white/8 text-center">
                    <span class="text-gray-600 text-xs">All interactions in one place. Nothing slips.</span>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- smbgen-CORE PRODUCT CARDS                                         --}}
{{-- ================================================================ --}}
<section id="start-here" class="bg-white px-6 py-20 md:py-24 border-t border-gray-100">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <span class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-3 block">smbgen-core</span>
            <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-4">Six tools. One connected system.</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto font-light">Everything moves through the same platform, so data flows freely and nothing needs to be manually synced.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-reveal-stagger>
            @php
                $startHereItems = [
                    [
                        'step' => '01',
                        'title' => 'Contact',
                        'body' => 'A superior contact experience with structured intake, clearer qualification, and smarter routing than a generic form page.',
                        'href' => route('product.contact'),
                        'cta' => 'Explore Contact',
                        'pillClass' => 'bg-blue-100 text-blue-700',
                        'ctaClass' => 'group-hover:text-blue-700',
                        'demo' => 'contact',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Book',
                        'body' => 'Scheduling that removes friction with availability, confirmations, reminders, and a smoother path from interest to appointment.',
                        'href' => route('product.book'),
                        'cta' => 'Explore Booking',
                        'pillClass' => 'bg-violet-100 text-violet-700',
                        'ctaClass' => 'group-hover:text-violet-700',
                        'demo' => 'book',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Pay',
                        'body' => 'A simpler payment experience that feels trustworthy and fast, with a cleaner handoff from approval to invoice to paid.',
                        'href' => route('product.pay'),
                        'cta' => 'Explore Pay',
                        'pillClass' => 'bg-emerald-100 text-emerald-700',
                        'ctaClass' => 'group-hover:text-emerald-700',
                        'demo' => 'pay',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Client Portal',
                        'body' => 'One clear place for clients to log in, view files, track progress, manage billing, and stay aligned without extra back-and-forth.',
                        'href' => route('product.portal'),
                        'cta' => 'Explore Portal',
                        'pillClass' => 'bg-orange-100 text-orange-700',
                        'ctaClass' => 'group-hover:text-orange-700',
                        'demo' => 'portal',
                    ],
                    [
                        'step' => '05',
                        'title' => 'CRM',
                        'body' => 'Track leads, conversations, deals, follow-ups, and customer history in one place so nothing falls through the cracks.',
                        'href' => route('product.crm'),
                        'cta' => 'Explore CRM',
                        'pillClass' => 'bg-indigo-100 text-indigo-700',
                        'ctaClass' => 'group-hover:text-indigo-700',
                        'demo' => 'crm',
                    ],
                    [
                        'step' => '06',
                        'title' => 'CMS',
                        'body' => 'Update pages, publish offers, and manage content without turning every site change into a development ticket.',
                        'href' => route('product.cms'),
                        'cta' => 'Explore CMS',
                        'pillClass' => 'bg-cyan-100 text-cyan-700',
                        'ctaClass' => 'group-hover:text-cyan-700',
                        'demo' => 'cms',
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

                    <div class="mb-6 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        @if($item['demo'] === 'contact')
                            <div class="space-y-2">
                                <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                    <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-blue-50 border border-blue-100 px-3 py-2 text-[11px] text-blue-700">
                                    <span>Qualified routing</span>
                                    <span class="inline-block h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                                </div>
                            </div>
                        @elseif($item['demo'] === 'book')
                            <div class="space-y-2">
                                <div class="grid grid-cols-3 gap-2 text-[10px] font-bold text-violet-700">
                                    <div class="rounded-lg bg-violet-100 py-1.5 text-center">Mon</div>
                                    <div class="rounded-lg bg-violet-100 py-1.5 text-center">Tue</div>
                                    <div class="rounded-lg bg-violet-100 py-1.5 text-center">Wed</div>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-white border border-gray-200 px-3 py-2 text-[11px]">
                                    <span>11:30 AM</span>
                                    <span class="text-violet-700 font-semibold">Available</span>
                                </div>
                            </div>
                        @elseif($item['demo'] === 'pay')
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                                    <span>Invoice #1048</span>
                                    <span>$1,250.00</span>
                                </div>
                                <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                <div class="rounded-lg bg-emerald-600 text-white text-xs font-bold py-2 text-center animate-pulse">Pay now</div>
                            </div>
                        @elseif($item['demo'] === 'portal')
                            <div class="space-y-2 text-[11px]">
                                @foreach(['Files', 'Messages', 'Billing'] as $portalItem)
                                    <div class="flex items-center justify-between rounded-lg bg-white border border-gray-200 px-3 py-2">
                                        <span>{{ $portalItem }}</span>
                                        <span class="text-orange-700 font-semibold">Open</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($item['demo'] === 'crm')
                            <div class="space-y-2 text-[11px]">
                                <div class="flex items-center justify-between rounded-lg bg-indigo-50 border border-indigo-100 px-3 py-2">
                                    <span>New lead</span>
                                    <span class="font-semibold text-indigo-700">High intent</span>
                                </div>
                                <div class="h-2 rounded-full bg-indigo-100 overflow-hidden">
                                    <div class="h-2 w-2/3 bg-indigo-500 animate-pulse"></div>
                                </div>
                            </div>
                        @else
                            <div class="space-y-2">
                                <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                <div class="h-16 rounded-lg bg-white border border-gray-200"></div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="h-10 rounded-lg bg-cyan-100"></div>
                                    <div class="h-10 rounded-lg bg-cyan-100"></div>
                                    <div class="h-10 rounded-lg bg-cyan-100"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 transition-colors {{ $item['ctaClass'] }}">
                        {{ $item['cta'] }}
                        <span>&rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- INDUSTRY SECTION                                                  --}}
{{-- ================================================================ --}}
<section id="industries" class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-6xl mx-auto">

        <div class="grid md:grid-cols-2 gap-16 items-start mb-16">
            <div>
                <span class="text-violet-400 text-xs font-black uppercase tracking-[0.2em] mb-5 block">Built for Your Industry</span>
                <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                    Custom solutions for the way your business actually works.
                </h2>
                <p class="text-gray-300 text-lg leading-relaxed">
                    smbgen isn't a generic platform dropped on top of your business. We build and configure each implementation around your industry's specific workflows, intake requirements, terminology, and client expectations.
                </p>
            </div>
            <div class="md:pt-8">
                <p class="text-gray-400 text-lg leading-relaxed mb-6">
                    A real estate agent's booking flow looks nothing like a law firm's intake process. A home service company collects different information than a health &amp; wellness practice. Industry-specific defaults mean you go live faster and with less friction.
                </p>
                <a href="{{ route('industries.index') }}" class="inline-flex items-center gap-2 text-violet-400 font-bold text-sm hover:text-violet-300 transition-colors">
                    Browse all industries &rarr;
                </a>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5" data-reveal-stagger>
            @php
                $industries = [
                    [
                        'label' => 'Real Estate',
                        'tagline' => 'Stop losing leads to a bad first impression.',
                        'desc' => 'Online showing scheduling connected to Google Calendar, property-aware intake forms, and a client portal for buyers and sellers.',
                        'href' => route('industries.real-estate'),
                        'color' => 'emerald',
                        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        'points' => ['Showing & open house booking', 'Property intake & buyer pre-qual', 'Google Calendar sync'],
                    ],
                    [
                        'label' => 'Home Services',
                        'tagline' => 'Get dispatched faster. Get paid on the job.',
                        'desc' => 'Service booking with address capture, job type routing, and automated confirmation — built for plumbers, HVAC, electricians, and more.',
                        'href' => route('industries.home-services'),
                        'color' => 'orange',
                        'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        'points' => ['Service area & job type intake', 'Dispatch-ready booking flow', 'On-site payment collection'],
                    ],
                    [
                        'label' => 'Legal',
                        'tagline' => 'First impressions for law firms that win.',
                        'desc' => 'Confidential consultation booking, structured matter intake, and a secure client portal for attorneys and law firms.',
                        'href' => route('industries.legal'),
                        'color' => 'blue',
                        'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
                        'points' => ['Consultation scheduling', 'Confidential client intake', 'Secure document portal'],
                    ],
                    [
                        'label' => 'Health & Wellness',
                        'tagline' => 'A practice management layer that feels effortless.',
                        'desc' => 'Health history intake, appointment booking with service type selection, payment collection, and private client records.',
                        'href' => route('industries.health-wellness'),
                        'color' => 'pink',
                        'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                        'points' => ['Service-type appointment booking', 'Health history & intake forms', 'Package & session billing'],
                    ],
                    [
                        'label' => 'Consulting',
                        'tagline' => 'Run a tighter practice. Deliver a better experience.',
                        'desc' => 'Proposal delivery, discovery call booking, retainer billing, and a client portal that makes engagement management seamless.',
                        'href' => route('industries.consulting'),
                        'color' => 'violet',
                        'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                        'points' => ['Discovery call & proposal flow', 'Retainer & milestone billing', 'Deliverable portal access'],
                    ],
                    [
                        'label' => 'Your Industry',
                        'tagline' => 'Not listed? We build custom.',
                        'desc' => 'Every business has workflows that don\'t fit a generic template. Book a call and we\'ll scope an implementation built for how you actually operate.',
                        'href' => Route::has('booking.wizard') ? route('booking.wizard') . '?intent=custom-industry' : route('contact'),
                        'color' => 'slate',
                        'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                        'points' => ['Custom intake & workflows', 'Industry-specific CRM fields', 'Scoped in a single call'],
                    ],
                ];
                $colorDark = [
                    'emerald' => ['hover' => 'hover:border-emerald-500/30', 'icon_bg' => 'bg-emerald-500/15 border-emerald-500/20', 'icon' => 'text-emerald-400', 'tag' => 'bg-emerald-600/20 border-emerald-600/30 text-emerald-300', 'cta' => 'text-emerald-400'],
                    'orange'  => ['hover' => 'hover:border-orange-500/30',  'icon_bg' => 'bg-orange-500/15 border-orange-500/20',  'icon' => 'text-orange-400',  'tag' => 'bg-orange-600/20 border-orange-600/30 text-orange-300',  'cta' => 'text-orange-400'],
                    'blue'    => ['hover' => 'hover:border-blue-500/30',    'icon_bg' => 'bg-blue-500/15 border-blue-500/20',    'icon' => 'text-blue-400',    'tag' => 'bg-blue-600/20 border-blue-600/30 text-blue-300',    'cta' => 'text-blue-400'],
                    'pink'    => ['hover' => 'hover:border-pink-500/30',    'icon_bg' => 'bg-pink-500/15 border-pink-500/20',    'icon' => 'text-pink-400',    'tag' => 'bg-pink-600/20 border-pink-600/30 text-pink-300',    'cta' => 'text-pink-400'],
                    'violet'  => ['hover' => 'hover:border-violet-500/30',  'icon_bg' => 'bg-violet-500/15 border-violet-500/20',  'icon' => 'text-violet-400',  'tag' => 'bg-violet-600/20 border-violet-600/30 text-violet-300',  'cta' => 'text-violet-400'],
                    'slate'   => ['hover' => 'hover:border-slate-500/30',   'icon_bg' => 'bg-slate-500/15 border-slate-500/20',   'icon' => 'text-slate-400',   'tag' => 'bg-slate-600/20 border-slate-600/30 text-slate-300',   'cta' => 'text-slate-400'],
                ];
            @endphp

            @foreach($industries as $ind)
                @php $c = $colorDark[$ind['color']]; @endphp
                <a href="{{ $ind['href'] }}" class="hp-industry-hover group bg-white/3 border border-white/8 {{ $c['hover'] }} rounded-2xl p-7 flex flex-col gap-5">
                    <div class="w-12 h-12 rounded-xl {{ $c['icon_bg'] }} border flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ind['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-black text-lg mb-1">{{ $ind['label'] }}</p>
                        <p class="text-sm font-semibold {{ $c['cta'] }} mb-3">{{ $ind['tagline'] }}</p>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $ind['desc'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-auto">
                        @foreach($ind['points'] as $pt)
                            <span class="text-[11px] px-2.5 py-1 rounded-lg border {{ $c['tag'] }} font-medium">{{ $pt }}</span>
                        @endforeach
                    </div>
                    <span class="{{ $c['cta'] }} text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all">
                        @if($ind['color'] === 'slate') Book a custom scoping call @else See how it works for {{ strtolower($ind['label']) }} @endif
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </a>
            @endforeach
        </div>

    </div>
</section>

{{-- ================================================================ --}}
{{-- HOW IT WORKS                                                      --}}
{{-- ================================================================ --}}
<section class="bg-[#03040d] py-24 px-6 border-t border-white/5">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-indigo-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">The Process</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-4">From first call to live platform.</h2>
            <p class="text-gray-400 text-lg max-w-xl mx-auto font-light">We move fast. Most clients are live within days, not months.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-0 relative">
            @php
                $steps = [
                    [
                        'num'   => '01',
                        'color' => 'indigo',
                        'title' => 'Book a free discovery call',
                        'desc'  => 'Tell us about your business, your bottlenecks, and your goals. No pitch deck — just a real conversation.',
                        'extra' => 'Complimentary. No commitment.',
                    ],
                    [
                        'num'   => '02',
                        'color' => 'blue',
                        'title' => 'We scope & configure your setup',
                        'desc'  => 'We map your industry\'s workflows into smbgen-core — intake forms, booking rules, payment flows, and CRM fields built for how you work.',
                        'extra' => 'Industry-specific out of the box.',
                    ],
                    [
                        'num'   => '03',
                        'color' => 'cyan',
                        'title' => 'Go live in days, not months',
                        'desc'  => 'Your platform is live and capturing leads, bookings, and payments. We handle the technical setup; you focus on clients.',
                        'extra' => 'Typical setup: 3–7 business days.',
                    ],
                    [
                        'num'   => '04',
                        'color' => 'emerald',
                        'title' => 'Grow with ongoing support',
                        'desc'  => 'Monthly reviews, new features as your business evolves, and a direct line to the team — not a support ticket queue.',
                        'extra' => 'We grow as you grow.',
                    ],
                ];
                $stepColors = [
                    'indigo'  => ['num_bg' => 'bg-indigo-600/20 border-indigo-600/30',  'num_text' => 'text-indigo-300',  'dot' => 'bg-indigo-500',  'pill' => 'bg-indigo-600/15 border-indigo-600/25 text-indigo-300'],
                    'blue'    => ['num_bg' => 'bg-blue-600/20 border-blue-600/30',       'num_text' => 'text-blue-300',    'dot' => 'bg-blue-500',    'pill' => 'bg-blue-600/15 border-blue-600/25 text-blue-300'],
                    'cyan'    => ['num_bg' => 'bg-cyan-600/20 border-cyan-600/30',       'num_text' => 'text-cyan-300',    'dot' => 'bg-cyan-500',    'pill' => 'bg-cyan-600/15 border-cyan-600/25 text-cyan-300'],
                    'emerald' => ['num_bg' => 'bg-emerald-600/20 border-emerald-600/30', 'num_text' => 'text-emerald-300', 'dot' => 'bg-emerald-500', 'pill' => 'bg-emerald-600/15 border-emerald-600/25 text-emerald-300'],
                ];
            @endphp

            @foreach($steps as $i => $step)
                @php $c = $stepColors[$step['color']]; @endphp
                <div class="relative flex flex-col items-center text-center px-6 pb-10 md:pb-0">
                    {{-- Connector line between steps --}}
                    @if($i < count($steps) - 1)
                        <div class="hidden md:block absolute top-7 left-[calc(50%+2rem)] right-0 h-px bg-gradient-to-r from-white/15 to-transparent"></div>
                    @endif

                    {{-- Step number --}}
                    <div class="w-14 h-14 rounded-2xl {{ $c['num_bg'] }} border flex items-center justify-center mb-5 shrink-0 relative z-10">
                        <span class="text-xl font-black {{ $c['num_text'] }}">{{ $step['num'] }}</span>
                    </div>

                    <h3 class="text-white font-black text-base mb-2 leading-tight">{{ $step['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-3">{{ $step['desc'] }}</p>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg border {{ $c['pill'] }}">{{ $step['extra'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-14">
            <a href="{{ Route::has('booking.wizard') ? route('booking.wizard') : route('contact') }}"
               class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition-colors shadow-xl shadow-indigo-900/40">
                Book your free discovery call &rarr;
            </a>
            <p class="text-gray-600 text-xs mt-3">No commitment. No pitch deck. Just a conversation.</p>
        </div>

    </div>
</section>

{{-- ================================================================ --}}
{{-- OPEN SOURCE / DEMOCRATIZING                                       --}}
{{-- ================================================================ --}}
<section class="bg-[#010a05] py-24 px-6 border-t border-white/5">
    <div class="max-w-6xl mx-auto">

        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-xs font-bold uppercase tracking-widest mb-8">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.648.5.5 5.648.5 12c0 5.084 3.292 9.396 7.86 10.918.575.106.785-.25.785-.556 0-.274-.01-1-.015-1.962-3.197.694-3.872-1.54-3.872-1.54-.523-1.328-1.277-1.682-1.277-1.682-1.044-.714.079-.699.079-.699 1.154.08 1.761 1.186 1.761 1.186 1.026 1.758 2.693 1.25 3.35.956.104-.743.402-1.25.731-1.537-2.552-.29-5.236-1.276-5.236-5.68 0-1.255.449-2.282 1.184-3.086-.119-.29-.513-1.459.112-3.042 0 0 .966-.31 3.166 1.179A10.98 10.98 0 0 1 12 6.032c.977.005 1.961.132 2.881.387 2.198-1.49 3.163-1.18 3.163-1.18.627 1.584.233 2.753.114 3.043.737.804 1.182 1.83 1.182 3.086 0 4.415-2.688 5.387-5.25 5.671.413.355.781 1.055.781 2.126 0 1.536-.014 2.774-.014 3.151 0 .309.207.668.79.555C20.21 21.392 23.5 17.082 23.5 12 23.5 5.648 18.352.5 12 .5Z"/></svg>
                    Free &amp; Open Source
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                    Enterprise tools.<br>
                    <span style="background: linear-gradient(135deg, #34d399 0%, #6ee7b7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Levelled for everyone.</span>
                </h2>
                <p class="text-gray-300 text-lg leading-relaxed mb-6">
                    For too long, the tools that make businesses look credible and run efficiently have been locked behind enterprise pricing. smbgen is built in the open — on open source foundations — so a solo real estate agent can have the same web presence as a national franchise.
                </p>
                <p class="text-gray-400 text-lg leading-relaxed mb-10">
                    No vendor lock-in. No "call for pricing." The core platform is free, the code is public, and the community helps drive what gets built next.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="https://github.com/smbgen" target="_blank" rel="noreferrer"
                       class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-bold text-sm border border-white/10 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.648.5.5 5.648.5 12c0 5.084 3.292 9.396 7.86 10.918.575.106.785-.25.785-.556 0-.274-.01-1-.015-1.962-3.197.694-3.872-1.54-3.872-1.54-.523-1.328-1.277-1.682-1.277-1.682-1.044-.714.079-.699.079-.699 1.154.08 1.761 1.186 1.761 1.186 1.026 1.758 2.693 1.25 3.35.956.104-.743.402-1.25.731-1.537-2.552-.29-5.236-1.276-5.236-5.68 0-1.255.449-2.282 1.184-3.086-.119-.29-.513-1.459.112-3.042 0 0 .966-.31 3.166 1.179A10.98 10.98 0 0 1 12 6.032c.977.005 1.961.132 2.881.387 2.198-1.49 3.163-1.18 3.163-1.18.627 1.584.233 2.753.114 3.043.737.804 1.182 1.83 1.182 3.086 0 4.415-2.688 5.387-5.25 5.671.413.355.781 1.055.781 2.126 0 1.536-.014 2.774-.014 3.151 0 .309.207.668.79.555C20.21 21.392 23.5 17.082 23.5 12 23.5 5.648 18.352.5 12 .5Z"/></svg>
                        View on GitHub &rarr;
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm transition-colors shadow-lg shadow-emerald-900/40">
                        Start free &rarr;
                    </a>
                </div>
            </div>

            {{-- Right: stack + principles --}}
            <div class="flex flex-col gap-5">

                {{-- Built on open source --}}
                <div class="bg-white/3 border border-white/8 rounded-2xl p-6">
                    <p class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-4">Built on open source</p>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['Laravel', 'The open source PHP framework powering millions of apps.'],
                            ['Livewire', 'Full-stack components, zero JavaScript files.'],
                            ['Alpine.js', 'Minimal reactive behaviour, directly in your markup.'],
                            ['Tailwind CSS', 'Utility-first CSS. No bloated stylesheets.'],
                        ] as [$name, $desc])
                            <div class="bg-emerald-500/5 border border-emerald-500/15 rounded-xl p-3.5">
                                <p class="text-emerald-300 text-xs font-black mb-1">{{ $name }}</p>
                                <p class="text-gray-500 text-[11px] leading-relaxed">{{ $desc }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Principles --}}
                <div class="flex flex-col gap-3">
                    @foreach([
                        ['No vendor lock-in', 'Your data is yours. Export everything, always.', 'text-emerald-400'],
                        ['No "call for pricing"', 'The core platform is free. Paid tiers are clear and fair.', 'text-blue-400'],
                        ['Built in public', 'Roadmap, issues, and contributions are all on GitHub.', 'text-violet-400'],
                    ] as [$title, $desc, $color])
                        <div class="flex items-start gap-3 bg-white/3 border border-white/8 rounded-xl px-4 py-3.5">
                            <svg class="w-4 h-4 {{ $color }} shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <div>
                                <p class="text-white text-sm font-bold">{{ $title }}</p>
                                <p class="text-gray-500 text-xs mt-0.5">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

    </div>
</section>

{{-- ================================================================ --}}
{{-- BOTTLENECK CTA STRIP                                              --}}
{{-- ================================================================ --}}
<section class="bg-[#060e1a] py-16 px-6 border-t border-white/5">
    <div class="max-w-5xl mx-auto">
        <div class="bg-gradient-to-r from-indigo-900/40 via-violet-900/30 to-blue-900/40 border border-indigo-700/30 rounded-2xl p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mb-2">Not sure where to start?</p>
                <h3 class="text-2xl md:text-3xl font-black text-white mb-2">Start from the bottleneck.</h3>
                <p class="text-gray-400 text-sm leading-relaxed max-w-lg">Tell us what's costing you the most — leads, bookings, payments, retention, or referrals — and we'll show you exactly how smbgen fixes it.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                <a href="{{ route('solutions.areas') }}" class="px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition-colors shadow-lg shadow-indigo-900/30 whitespace-nowrap">
                    Browse solution areas &rarr;
                </a>
                <a href="{{ Route::has('booking.wizard') ? route('booking.wizard') : route('contact') }}" class="px-6 py-3.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-semibold text-sm border border-white/10 transition-colors whitespace-nowrap">
                    Book a call
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
