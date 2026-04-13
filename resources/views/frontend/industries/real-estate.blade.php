@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
    $loginHref   = route('login');
    $registerHref = Route::has('register') ? route('register') : route('contact');
@endphp

@section('title', 'smbgen for Real Estate Agents — Online Booking, Google Calendar & Client Management')
@section('description', 'Give your real estate business a professional web presence in days. Online booking connected to Google Calendar, property-aware intake forms, automated confirmation emails, and a client portal — all in one place.')

@push('head')
<style>
    .re-hero-bg {
        background:
            radial-gradient(ellipse at 65% -5%, rgba(16,185,129,0.15) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(99,102,241,0.10) 0%, transparent 50%),
            radial-gradient(ellipse at 95% 75%, rgba(245,158,11,0.07) 0%, transparent 45%),
            #06101d;
    }
    .re-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .re-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(16,185,129,0.25), 0 8px 32px rgba(16,185,129,0.08);
        transform: translateY(-2px);
    }
    .re-gradient-text {
        background: linear-gradient(135deg, #34d399, #60a5fa, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .re-badge {
        background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(99,102,241,0.15));
        border: 1px solid rgba(16,185,129,0.25);
    }
    .re-step-line::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 100%;
        width: 1px;
        height: 2rem;
        background: linear-gradient(180deg, rgba(16,185,129,0.4), transparent);
    }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO                                                          --}}
{{-- ============================================================ --}}
<section class="re-hero-bg min-h-[90vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div>
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full re-badge text-emerald-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Built for Real Estate Agents
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    Never lose leads<br>
                    to a bad<br>
                    <span class="re-gradient-text">first impression.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    smbgen gives you a professional booking page connected directly to your Google Calendar.
                    Buyers and sellers book showings online. You get notified. It lands in your calendar automatically.
                    No more back-and-forth texts.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=real-estate"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-bold transition-colors shadow-xl shadow-emerald-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $loginHref }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Sign in to your account
                    </a>
                </div>

                {{-- Social proof strip --}}
                <div class="flex items-center gap-3 text-gray-500 text-xs">
                    <span class="flex items-center gap-1 text-emerald-400 font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        No credit card to start
                    </span>
                    <span class="text-gray-700">·</span>
                    <span class="flex items-center gap-1 text-emerald-400 font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Live in under a day
                    </span>
                    <span class="text-gray-700">·</span>
                    <span class="flex items-center gap-1 text-emerald-400 font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Works with Google Calendar
                    </span>
                </div>
            </div>

            {{-- Right: Booking preview card --}}
            <div class="relative">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl backdrop-blur-sm">

                    {{-- Card header --}}
                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
                        <div>
                            <p class="text-white font-bold text-sm">Schedule a Showing</p>
                            <p class="text-gray-400 text-xs mt-0.5">123 Maple St, Keedysville MD</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Time slots --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Available this week</p>
                    <div class="grid grid-cols-3 gap-2 mb-5">
                        @foreach(['Mon 9:00am', 'Mon 11:00am', 'Tue 10:00am', 'Tue 2:00pm', 'Wed 9:00am', 'Thu 1:00pm'] as $i => $slot)
                            <button class="px-2 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 2
                                    ? 'bg-emerald-500 text-white border-emerald-400 shadow-lg shadow-emerald-900/30'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-emerald-500/40 hover:text-emerald-300' }}">
                                {{ $slot }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Form fields --}}
                    <div class="space-y-2 mb-4">
                        <div class="bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="text-gray-400 text-xs">Your name</span>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-gray-400 text-xs">Email address</span>
                        </div>
                        <div class="bg-white/5 border border-emerald-500/30 rounded-lg px-3 py-2.5 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-emerald-300 text-xs">123 Maple St, Keedysville MD 21756</span>
                        </div>
                    </div>

                    <button class="w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-sm transition-colors shadow-lg shadow-emerald-900/20">
                        Confirm Showing &rarr;
                    </button>

                    {{-- Google Calendar badge --}}
                    <div class="flex items-center justify-center gap-2 mt-4 text-gray-500 text-xs">
                        <svg class="w-4 h-4" viewBox="0 0 48 48"><rect x="4" y="4" width="40" height="40" rx="4" fill="#fff"/><rect x="12" y="4" width="4" height="8" rx="2" fill="#1a73e8"/><rect x="32" y="4" width="4" height="8" rx="2" fill="#1a73e8"/><rect x="4" y="16" width="40" height="2" fill="#1a73e8"/><text x="24" y="34" font-size="14" font-weight="700" text-anchor="middle" fill="#1a73e8">{{ now()->format('d') }}</text></svg>
                        Syncs instantly to Google Calendar
                    </div>
                </div>

                {{-- Floating confirmation badge --}}
                <div class="absolute -bottom-4 -left-4 bg-gray-900 border border-emerald-500/30 rounded-xl px-4 py-3 shadow-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-bold">Booking confirmed</p>
                        <p class="text-gray-400 text-xs">Invite sent · Calendar updated</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- HOW IT WORKS                                                  --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-16">
        <span class="text-emerald-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">How it works</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            From zero to bookings in one day.
        </h2>
    </div>

    <div class="max-w-5xl mx-auto grid md:grid-cols-4 gap-6">
        @php
            $steps = [
                ['num' => '01', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'title' => 'You sign up', 'body' => 'Create your account and connect your Google Calendar in under 5 minutes.'],
                ['num' => '02', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'Set your availability', 'body' => 'Choose which days and times clients can book showings or consultations.'],
                ['num' => '03', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'title' => 'Share your link', 'body' => 'Drop your booking link in your email signature, Zillow profile, or anywhere online.'],
                ['num' => '04', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0', 'title' => 'Clients book, calendar fills', 'body' => 'Every booking creates a Google Calendar event and sends a confirmation email automatically.'],
            ];
        @endphp

        @foreach($steps as $step)
            <div class="re-card-hover bg-white/3 border border-white/8 rounded-2xl p-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-emerald-500/60 text-xs font-black uppercase tracking-widest mb-2 block">{{ $step['num'] }}</span>
                <h3 class="text-white font-bold text-sm mb-2">{{ $step['title'] }}</h3>
                <p class="text-gray-400 text-xs leading-relaxed">{{ $step['body'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============================================================ --}}
{{-- FEATURES                                                      --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-emerald-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">What you get</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                Everything a solo agent needs.<br class="hidden md:block"> Nothing they don't.
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $features = [
                    ['color' => 'emerald', 'title' => 'Google Calendar Booking', 'body' => 'Clients pick a time, it lands in your Google Calendar instantly. No double-booking. No phone tag.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['color' => 'blue', 'title' => 'Property-Aware Forms', 'body' => 'Intake forms that capture property address, client type, and notes — structured data you can actually use.', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['color' => 'violet', 'title' => 'Automatic Confirmations', 'body' => 'Professional confirmation emails go out the moment a booking is made. Google Meet link included.', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['color' => 'amber', 'title' => 'Lead Capture', 'body' => "Every inquiry and booking becomes a lead in your CRM. See who's interested and follow up faster.", 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['color' => 'sky', 'title' => 'Contact Page', 'body' => 'A clean contact form that routes inquiries directly to your inbox with full tracking on opens and clicks.', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                    ['color' => 'rose', 'title' => 'Your Own Dashboard', 'body' => 'Log in and see all your upcoming bookings, leads, and client activity in one place. No spreadsheets.', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ];

                $colorMap = [
                    'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400', 'hover' => 'hover:border-emerald-500/30'],
                    'blue'    => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-400',    'hover' => 'hover:border-blue-500/30'],
                    'violet'  => ['bg' => 'bg-violet-500/10',  'border' => 'border-violet-500/20',  'text' => 'text-violet-400',  'hover' => 'hover:border-violet-500/30'],
                    'amber'   => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-400',   'hover' => 'hover:border-amber-500/30'],
                    'sky'     => ['bg' => 'bg-sky-500/10',     'border' => 'border-sky-500/20',     'text' => 'text-sky-400',     'hover' => 'hover:border-sky-500/30'],
                    'rose'    => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-400',    'hover' => 'hover:border-rose-500/30'],
                ];
            @endphp

            @foreach($features as $feature)
                @php $c = $colorMap[$feature['color']]; @endphp
                <div class="re-card-hover bg-white/3 border border-white/8 {{ $c['hover'] }} rounded-2xl p-6">
                    <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} border {{ $c['border'] }} flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-sm mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $feature['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- PRICING                                                       --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-16">
        <span class="text-emerald-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Simple pricing</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            One plan. No surprises.
        </h2>
        <p class="text-gray-400 mt-4 text-lg">Less than one lost commission would cover years of this.</p>
    </div>

    <div class="max-w-md mx-auto">
        <div class="bg-white/5 border border-emerald-500/30 rounded-2xl p-8 shadow-2xl shadow-emerald-900/10 relative overflow-hidden">

            {{-- Glow --}}
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none"></div>

            <div class="relative">
                {{-- Plan name --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-emerald-400 text-xs font-black uppercase tracking-widest mb-1">Agent Plan</p>
                        <div class="flex items-end gap-1">
                            <span class="text-5xl font-black text-white">$79</span>
                            <span class="text-gray-400 text-sm mb-2">/month</span>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-bold">
                        Most Popular
                    </div>
                </div>

                {{-- Features --}}
                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Google Calendar booking page',
                        'Property-aware intake forms',
                        'Automated confirmation emails',
                        'Google Meet link on every booking',
                        'Lead CRM — all inquiries in one place',
                        'Contact form with email notifications',
                        'Admin dashboard to manage bookings',
                        'Unlimited bookings per month',
                        'Setup support included',
                    ] as $item)
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ $bookHref }}?intent=real-estate-signup"
                   class="block w-full text-center py-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-bold transition-colors shadow-lg shadow-emerald-900/30 text-sm">
                    Get started today &rarr;
                </a>

                <p class="text-center text-gray-500 text-xs mt-4">
                    30-day money-back guarantee. Cancel anytime.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- GOOGLE WORKSPACE                                              --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5">
    <div class="max-w-6xl mx-auto">

        <div class="grid md:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div>
                {{-- Google badge --}}
                <div class="flex items-center gap-3 mb-7">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-medium text-gray-300">
                        {{-- Google G --}}
                        <svg class="w-4 h-4" viewBox="0 0 48 48"><path fill="#4285F4" d="M44.5 20H24v8.5h11.8C34.7 33.9 30.1 37 24 37c-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4C34.6 4.1 29.6 2 24 2 11.8 2 2 11.8 2 24s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"/><path fill="#34A853" d="M6.3 14.7l7 5.1C15.1 16.1 19.2 13 24 13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4C34.6 4.1 29.6 2 24 2c-7.6 0-14.2 4.4-17.7 10.7z" opacity=".9"/><path fill="#FBBC05" d="M24 46c5.5 0 10.5-1.9 14.3-5l-6.6-5.4C29.7 37 27 38 24 38c-6 0-10.8-3.9-12.3-9.2l-7 5.4C8.1 41.7 15.5 46 24 46z" opacity=".9"/><path fill="#EA4335" d="M44.5 20H24v8.5h11.8c-.9 2.8-2.8 5.1-5.3 6.6l6.6 5.4C41.1 37.3 45 31.2 45 24c0-1.3-.2-2.7-.5-4z" opacity=".9"/></svg>
                        Works best with Google Workspace
                    </div>
                </div>

                <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-5">
                    Sign in with Google.<br>
                    <span class="re-gradient-text">Everything connects automatically.</span>
                </h2>

                <p class="text-gray-400 text-lg leading-relaxed mb-8">
                    If you're on Google Workspace — or even a regular Gmail account — connecting your calendar takes about 90 seconds.
                    Click "Connect Google Calendar," sign in, approve access, and every booking goes straight to your calendar from that moment on.
                </p>

                <div class="space-y-4 mb-8">
                    @php
                        $gwBenefits = [
                            ['color' => 'blue', 'title' => 'One-click calendar connection', 'body' => 'OAuth handles authentication securely. No passwords shared, no manual sync needed.'],
                            ['color' => 'emerald', 'title' => 'Always-fresh tokens', 'body' => 'Your connection stays active automatically. If access ever expires, it refreshes silently in the background.'],
                            ['color' => 'violet', 'title' => 'Google Meet links included', 'body' => 'Every virtual booking auto-generates a Google Meet link and includes it in the confirmation email.'],
                            ['color' => 'amber', 'title' => 'Google Workspace = best experience', 'body' => 'Workspace accounts get smoother OAuth, better email deliverability, and a more professional look for your clients.'],
                        ];
                        $gwColors = [
                            'blue'    => ['dot' => 'bg-blue-400',    'text' => 'text-blue-400'],
                            'emerald' => ['dot' => 'bg-emerald-400', 'text' => 'text-emerald-400'],
                            'violet'  => ['dot' => 'bg-violet-400',  'text' => 'text-violet-400'],
                            'amber'   => ['dot' => 'bg-amber-400',   'text' => 'text-amber-400'],
                        ];
                    @endphp

                    @foreach($gwBenefits as $b)
                        @php $gc = $gwColors[$b['color']]; @endphp
                        <div class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full {{ $gc['dot'] }} shrink-0 mt-2"></span>
                            <div>
                                <p class="text-white text-sm font-semibold">{{ $b['title'] }}</p>
                                <p class="text-gray-400 text-sm leading-relaxed">{{ $b['body'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="{{ $bookHref }}?intent=real-estate-gw"
                   class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                    Ask us about Google Workspace setup &rarr;
                </a>
            </div>

            {{-- Right: Connection flow visual --}}
            <div class="space-y-3">

                {{-- Step 1 --}}
                <div class="bg-white/4 border border-white/8 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-lg bg-blue-500/15 border border-blue-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-white text-sm font-semibold">Click "Connect Google Calendar"</p>
                        <p class="text-gray-500 text-xs">In your smbgen dashboard</p>
                    </div>
                    <span class="text-gray-600 text-xs font-mono">Step 1</span>
                </div>

                {{-- Arrow --}}
                <div class="flex justify-center">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Step 2 --}}
                <div class="bg-white/4 border border-white/8 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-lg bg-white/8 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 48 48"><path fill="#4285F4" d="M44.5 20H24v8.5h11.8C34.7 33.9 30.1 37 24 37c-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4C34.6 4.1 29.6 2 24 2 11.8 2 2 11.8 2 24s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"/><path fill="#34A853" d="M6.3 14.7l7 5.1C15.1 16.1 19.2 13 24 13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4C34.6 4.1 29.6 2 24 2c-7.6 0-14.2 4.4-17.7 10.7z" opacity=".9"/><path fill="#FBBC05" d="M24 46c5.5 0 10.5-1.9 14.3-5l-6.6-5.4C29.7 37 27 38 24 38c-6 0-10.8-3.9-12.3-9.2l-7 5.4C8.1 41.7 15.5 46 24 46z" opacity=".9"/><path fill="#EA4335" d="M44.5 20H24v8.5h11.8c-.9 2.8-2.8 5.1-5.3 6.6l6.6 5.4C41.1 37.3 45 31.2 45 24c0-1.3-.2-2.7-.5-4z" opacity=".9"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-white text-sm font-semibold">Sign in with Google & approve access</p>
                        <p class="text-gray-500 text-xs">Secure OAuth — we never see your password</p>
                    </div>
                    <span class="text-gray-600 text-xs font-mono">Step 2</span>
                </div>

                {{-- Arrow --}}
                <div class="flex justify-center">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Step 3 - active/confirmed --}}
                <div class="bg-emerald-500/8 border border-emerald-500/25 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-emerald-300 text-sm font-semibold">Calendar connected — bookings are live</p>
                        <p class="text-gray-500 text-xs">New bookings appear in Google Calendar instantly</p>
                    </div>
                    <span class="text-emerald-600 text-xs font-mono">Done</span>
                </div>

                {{-- Workspace upgrade nudge --}}
                <div class="mt-4 bg-gradient-to-r from-blue-500/8 to-violet-500/8 border border-blue-500/15 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-blue-300 text-xs font-semibold mb-1">Works with Gmail · Best with Google Workspace</p>
                            <p class="text-gray-500 text-xs leading-relaxed">Regular Gmail accounts connect fine. Google Workspace gives you a branded email address, smoother OAuth, and better email deliverability — worth it if you're serious about your business presence.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FAQ                                                           --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5" x-data="{ open: null }">
    <div class="max-w-3xl mx-auto">

        <div class="text-center mb-14">
            <span class="text-emerald-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">FAQ</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Common questions</h2>
        </div>

        @php
            $faqs = [
                ['q' => 'Do I need a website already?', 'a' => 'No. smbgen gives you a booking page and contact form you can link to from anywhere — your email signature, Zillow profile, social media, or business card. A full website is optional.'],
                ['q' => 'Which Google Calendar account does it connect to?', 'a' => 'Your existing Google Workspace or personal Gmail calendar. You connect it once through your dashboard and all bookings sync automatically from that point on.'],
                ['q' => 'What does the client experience look like?', 'a' => 'They land on your booking page, pick a time slot, fill in their name, email, phone, and the property address. They get a confirmation email instantly with a Google Meet link if it\'s a virtual meeting.'],
                ['q' => 'Can I customize the booking form?', 'a' => 'Yes. You can add or remove form fields from your dashboard. The property address field is built in — especially useful for scheduling showings at specific listings.'],
                ['q' => 'What if I need to cancel or reschedule a booking?', 'a' => 'You manage all bookings from your admin dashboard. Cancellations update your Google Calendar automatically.'],
                ['q' => 'Is setup hard?', 'a' => 'No. We walk you through connecting your Google Calendar, setting your availability, and getting your booking link. Most agents are live in under an hour. Setup support is included.'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $i => $faq)
                <div class="bg-white/3 border border-white/8 rounded-xl overflow-hidden">
                    <button
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left text-white font-semibold text-sm hover:text-emerald-300 transition-colors">
                        <span>{{ $faq['q'] }}</span>
                        <svg class="w-4 h-4 shrink-0 transition-transform duration-200 text-gray-400"
                             :class="open === {{ $i }} ? 'rotate-45 text-emerald-400' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-transition x-cloak class="px-6 pb-5">
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FINAL CTA                                                     --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-28 px-6 border-t border-white/5">
    <div class="max-w-3xl mx-auto text-center">

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full re-badge text-emerald-300 text-xs font-semibold mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Ready to get started?
        </div>

        <h2 class="text-4xl md:text-5xl font-black text-white mb-5 tracking-tight">
            Your first booking<br>could be today.
        </h2>

        <p class="text-gray-400 text-lg mb-10 leading-relaxed">
            Book a 20-minute call and we'll get your Google Calendar connected,
            your availability set, and your booking link ready to share — same day.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=real-estate"
               class="px-8 py-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-bold transition-colors shadow-xl shadow-emerald-900/30 text-sm">
                Book a 20-min demo &rarr;
            </a>
            <a href="{{ $contactHref }}?topic=real-estate"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                Send a question first
            </a>
        </div>

        <p class="text-gray-600 text-xs mt-8">$79/month · Cancel anytime · Setup included</p>

    </div>
</section>

@endsection
