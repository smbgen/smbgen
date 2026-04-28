@extends('layouts.frontend')

@php
    $bookHref = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
@endphp

@section('title', 'Solution Areas — Start From the Bottleneck | smbgen')
@section('description', 'smbgen is built around the real problems small businesses face: not enough leads, scheduling chaos, slow payments, client churn, and no referral system. Start from the outcome you need.')

@push('head')
<style>
    .sa-hero-bg {
        background:
            radial-gradient(ellipse at 60% -10%, rgba(99,102,241,0.15) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(16,185,129,0.10) 0%, transparent 50%),
            #06101d;
    }
    .sa-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease, border-color 0.18s ease;
    }
    .sa-card-hover:hover {
        transform: translateY(-3px);
    }
</style>
@endpush

@section('content')

{{-- ================================================================ --}}
{{-- HERO                                                              --}}
{{-- ================================================================ --}}
<section class="sa-hero-bg py-28 px-6">
    <div class="max-w-5xl mx-auto text-center">

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-gray-400 text-xs font-semibold mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
            Built around your biggest bottlenecks
        </div>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
            Start from the outcome<br>
            you actually need.
        </h1>

        <p class="text-gray-300 text-lg leading-relaxed mb-14 max-w-2xl mx-auto">
            smbgen-core covers the entire business journey — from the first contact to repeat referrals.
            Pick the bottleneck that's costing you the most right now.
        </p>

        {{-- Journey bar --}}
        <div class="flex flex-wrap justify-center gap-2 mb-16">
            @php
                $journey = [
                    ['label' => 'Lead',    'color' => 'bg-blue-600/20 border-blue-600/30 text-blue-300'],
                    ['label' => 'Nurture', 'color' => 'bg-violet-600/20 border-violet-600/30 text-violet-300'],
                    ['label' => 'Propose', 'color' => 'bg-indigo-600/20 border-indigo-600/30 text-indigo-300'],
                    ['label' => 'Close',   'color' => 'bg-cyan-600/20 border-cyan-600/30 text-cyan-300'],
                    ['label' => 'Pay',     'color' => 'bg-emerald-600/20 border-emerald-600/30 text-emerald-300'],
                    ['label' => 'Deliver', 'color' => 'bg-orange-600/20 border-orange-600/30 text-orange-300'],
                    ['label' => 'Retain',  'color' => 'bg-amber-600/20 border-amber-600/30 text-amber-300'],
                    ['label' => 'Refer',   'color' => 'bg-yellow-600/20 border-yellow-600/30 text-yellow-300'],
                ];
            @endphp
            @foreach($journey as $i => $stage)
                <span class="px-4 py-1.5 rounded-xl border {{ $stage['color'] }} text-xs font-bold">{{ $stage['label'] }}</span>
                @if($i < count($journey) - 1)
                    <svg class="w-4 h-4 text-gray-700 self-center shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @endif
            @endforeach
        </div>

        {{-- Solution area cards --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 text-left">

            {{-- More Leads --}}
            <a href="{{ route('solutions.more-leads') }}" class="sa-card-hover group bg-white/4 hover:bg-blue-500/8 border border-white/8 hover:border-blue-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/15 border border-blue-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-blue-300 transition-colors">Get More Leads</p>
                    <p class="text-gray-400 text-sm leading-relaxed">"My website gets traffic but it doesn't convert into real business conversations."</p>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-auto">
                    <span class="text-xs px-2 py-0.5 rounded-md bg-blue-600/15 text-blue-400 border border-blue-600/20">Contact</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-indigo-600/15 text-indigo-400 border border-indigo-600/20">CRM</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-cyan-600/15 text-cyan-400 border border-cyan-600/20">CMS</span>
                </div>
                <span class="text-blue-400 text-xs font-semibold flex items-center gap-1">
                    See how &rarr;
                </span>
            </a>

            {{-- Streamline Bookings --}}
            <a href="{{ route('solutions.streamline-bookings') }}" class="sa-card-hover group bg-white/4 hover:bg-violet-500/8 border border-white/8 hover:border-violet-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-violet-500/15 border border-violet-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-violet-300 transition-colors">Streamline Bookings</p>
                    <p class="text-gray-400 text-sm leading-relaxed">"I'm losing an hour a day to back-and-forth scheduling. Clients forget appointments. No reminders."</p>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-auto">
                    <span class="text-xs px-2 py-0.5 rounded-md bg-violet-600/15 text-violet-400 border border-violet-600/20">Book</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-blue-600/15 text-blue-400 border border-blue-600/20">Contact</span>
                </div>
                <span class="text-violet-400 text-xs font-semibold flex items-center gap-1">
                    See how &rarr;
                </span>
            </a>

            {{-- Get Paid Faster --}}
            <a href="{{ route('solutions.get-paid-faster') }}" class="sa-card-hover group bg-white/4 hover:bg-emerald-500/8 border border-white/8 hover:border-emerald-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-emerald-300 transition-colors">Get Paid Faster</p>
                    <p class="text-gray-400 text-sm leading-relaxed">"I delivered the work two weeks ago. I'm still waiting on the invoice. I hate chasing clients."</p>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-auto">
                    <span class="text-xs px-2 py-0.5 rounded-md bg-emerald-600/15 text-emerald-400 border border-emerald-600/20">Pay</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-orange-600/15 text-orange-400 border border-orange-600/20">Client Portal</span>
                </div>
                <span class="text-emerald-400 text-xs font-semibold flex items-center gap-1">
                    See how &rarr;
                </span>
            </a>

            {{-- Retain Clients --}}
            <a href="{{ route('solutions.retain-clients') }}" class="sa-card-hover group bg-white/4 hover:bg-orange-500/8 border border-white/8 hover:border-orange-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-500/15 border border-orange-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-orange-300 transition-colors">Retain Clients</p>
                    <p class="text-gray-400 text-sm leading-relaxed">"Clients love the work but go cold after delivery. No portal, no ongoing communication, no reason to come back."</p>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-auto">
                    <span class="text-xs px-2 py-0.5 rounded-md bg-orange-600/15 text-orange-400 border border-orange-600/20">Client Portal</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-indigo-600/15 text-indigo-400 border border-indigo-600/20">CRM</span>
                </div>
                <span class="text-orange-400 text-xs font-semibold flex items-center gap-1">
                    See how &rarr;
                </span>
            </a>

            {{-- Grow Through Referrals --}}
            <a href="{{ route('solutions.grow-referrals') }}" class="sa-card-hover group bg-white/4 hover:bg-yellow-500/8 border border-white/8 hover:border-yellow-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-500/15 border border-yellow-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-yellow-300 transition-colors">Grow Through Referrals</p>
                    <p class="text-gray-400 text-sm leading-relaxed">"Most of my best clients came via word-of-mouth but I have no system to ask, track, or thank referrers."</p>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-auto">
                    <span class="text-xs px-2 py-0.5 rounded-md bg-indigo-600/15 text-indigo-400 border border-indigo-600/20">CRM</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-orange-600/15 text-orange-400 border border-orange-600/20">Client Portal</span>
                </div>
                <span class="text-yellow-400 text-xs font-semibold flex items-center gap-1">
                    See how &rarr;
                </span>
            </a>

            {{-- AI Solutions --}}
            <a href="{{ route('solutions.ai') }}" class="sa-card-hover group bg-white/4 hover:bg-fuchsia-500/8 border border-white/8 hover:border-fuchsia-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-fuchsia-500/15 border border-fuchsia-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3a2.25 2.25 0 00-2.25 2.25V9H5.25A2.25 2.25 0 003 11.25v1.5A2.25 2.25 0 005.25 15H7.5v3.75A2.25 2.25 0 009.75 21h4.5a2.25 2.25 0 002.25-2.25V15h2.25A2.25 2.25 0 0021 12.75v-1.5A2.25 2.25 0 0018.75 9H16.5V5.25A2.25 2.25 0 0014.25 3h-4.5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-fuchsia-300 transition-colors">AI Solutions</p>
                    <p class="text-gray-400 text-sm leading-relaxed">"I need AI assistants that know my business context for hiring, pipeline growth, and go-to-market execution."</p>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-auto">
                    <span class="text-xs px-2 py-0.5 rounded-md bg-fuchsia-600/15 text-fuchsia-400 border border-fuchsia-600/20">HR</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-indigo-600/15 text-indigo-400 border border-indigo-600/20">Biz Dev</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-cyan-600/15 text-cyan-400 border border-cyan-600/20">GTM</span>
                </div>
                <span class="text-fuchsia-400 text-xs font-semibold flex items-center gap-1">
                    Explore AI assistants &rarr;
                </span>
            </a>

            {{-- Just talk to us --}}
            <a href="{{ $bookHref }}?intent=solutions" class="sa-card-hover group bg-gradient-to-br from-white/5 to-white/3 hover:from-indigo-500/10 hover:to-violet-500/8 border border-white/8 hover:border-indigo-500/30 rounded-2xl p-6 flex flex-col gap-4 justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/15 border border-indigo-500/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-indigo-300 transition-colors">Not sure where to start?</p>
                    <p class="text-gray-400 text-sm leading-relaxed">Book a 20-minute call. We'll identify the single highest-leverage thing to fix right now.</p>
                </div>
                <span class="text-indigo-400 text-xs font-semibold flex items-center gap-1">
                    Book a solutions call &rarr;
                </span>
            </a>

        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- OR BROWSE BY PRODUCT                                              --}}
{{-- ================================================================ --}}
<section class="bg-[#060e1a] py-16 px-6 border-t border-white/5">
    <div class="max-w-3xl mx-auto text-center">
        <p class="text-gray-500 text-sm mb-6">
            Prefer to browse by product?
            <a href="{{ route('solutions') }}" class="text-blue-400 font-semibold hover:text-blue-300 transition-colors ml-1">
                See all smbgen-core features &rarr;
            </a>
        </p>
    </div>
</section>

@endsection
