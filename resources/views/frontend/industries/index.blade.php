@extends('layouts.frontend')

@section('title', 'smbgen by Industry — Online Booking & Client Management for Small Businesses')
@section('description', 'smbgen is built for the way your industry actually works. Pick your industry to see how real estate agents, home service pros, legal professionals, and consultants use smbgen to book more clients and run a tighter operation.')

@push('head')
<style>
    .ind-hero-bg {
        background:
            radial-gradient(ellipse at 60% -10%, rgba(99,102,241,0.15) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(16,185,129,0.10) 0%, transparent 50%),
            #06101d;
    }
    .ind-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease, border-color 0.18s ease;
    }
    .ind-card-hover:hover {
        transform: translateY(-3px);
    }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO                                                          --}}
{{-- ============================================================ --}}
<section class="ind-hero-bg py-28 px-6">
    <div class="max-w-5xl mx-auto text-center">

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-300 text-xs font-semibold mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-pulse"></span>
            Built for your industry
        </div>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
            The tools your industry needs.<br>
            <span class="bg-gradient-to-r from-violet-400 via-blue-400 to-emerald-400 bg-clip-text text-transparent">
                None of the bloat.
            </span>
        </h1>

        <p class="text-gray-300 text-lg leading-relaxed mb-12 max-w-2xl mx-auto">
            smbgen adapts to your workflow. Online booking, Google Calendar sync, custom intake forms,
            and a client portal — configured for the way your business actually operates.
        </p>

        {{-- Industry cards --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 text-left">

            {{-- Real Estate --}}
            <a href="{{ route('industries.real-estate') }}" class="ind-card-hover group bg-white/4 hover:bg-emerald-500/8 border border-white/8 hover:border-emerald-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-emerald-300 transition-colors">Real Estate</p>
                    <p class="text-gray-400 text-sm leading-relaxed">Online showing scheduling, property intake forms, Google Calendar sync.</p>
                </div>
                <span class="text-emerald-400 text-xs font-semibold mt-auto flex items-center gap-1">
                    See how it works
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>

            {{-- Home Services --}}
            <a href="{{ route('industries.home-services') }}" class="ind-card-hover group bg-white/4 hover:bg-orange-500/8 border border-white/8 hover:border-orange-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-500/15 border border-orange-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-orange-300 transition-colors">Home Services</p>
                    <p class="text-gray-400 text-sm leading-relaxed">Service dispatch booking, service area forms, job tracking for plumbers, HVAC, electricians.</p>
                </div>
                <span class="text-orange-400 text-xs font-semibold mt-auto flex items-center gap-1">
                    See how it works
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>

            {{-- Legal --}}
            <a href="{{ route('industries.legal') }}" class="ind-card-hover group bg-white/4 hover:bg-blue-500/8 border border-white/8 hover:border-blue-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/15 border border-blue-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-blue-300 transition-colors">Legal</p>
                    <p class="text-gray-400 text-sm leading-relaxed">Consultation scheduling, confidential intake, client portal for attorneys and law firms.</p>
                </div>
                <span class="text-blue-400 text-xs font-semibold mt-auto flex items-center gap-1">
                    See how it works
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>

            {{-- Health & Wellness --}}
            <a href="{{ route('industries.health-wellness') }}" class="ind-card-hover group bg-white/4 hover:bg-teal-500/8 border border-white/8 hover:border-teal-500/30 rounded-2xl p-6 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-500/15 border border-teal-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base mb-1 group-hover:text-teal-300 transition-colors">Health & Wellness</p>
                    <p class="text-gray-400 text-sm leading-relaxed">Appointment booking for therapists, trainers, chiropractors, and practitioners.</p>
                </div>
                <span class="text-teal-400 text-xs font-semibold mt-auto flex items-center gap-1">
                    See how it works
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>

        </div>

        {{-- More coming --}}
        <p class="text-gray-600 text-sm mt-10">
            More industries coming soon &mdash; consultants, financial advisors, photographers, and more.
        </p>

    </div>
</section>

{{-- ============================================================ --}}
{{-- SHARED PLATFORM                                               --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5">
    <div class="max-w-5xl mx-auto text-center mb-14">
        <span class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">One platform, every industry</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            The same powerful core.<br>Configured for how you work.
        </h2>
    </div>

    <div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-5">
        @php
            $shared = [
                ['color' => 'blue', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'Google Calendar Booking', 'body' => 'Every industry gets the same seamless booking-to-calendar connection. Clients book, it lands in your calendar.'],
                ['color' => 'violet', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Custom Intake Forms', 'body' => 'Collect the right info for your work — property addresses, case types, health history, project scope.'],
                ['color' => 'emerald', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Client Portal & CRM', 'body' => 'Every lead and booking goes into your CRM. Share documents, track conversations, stay organised.'],
                ['color' => 'amber', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Automated Emails', 'body' => 'Confirmation emails fire the moment a booking is made. Google Meet links included for virtual sessions.'],
                ['color' => 'sky', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Admin Dashboard', 'body' => "All your bookings, leads, and clients in one place. See what's coming up, manage schedules, follow up fast."],
                ['color' => 'rose', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Live in under a day', 'body' => 'Connect your calendar, configure your availability, share your link. Setup support is included.'],
            ];
            $colorMap = [
                'blue'    => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-400'],
                'violet'  => ['bg' => 'bg-violet-500/10',  'border' => 'border-violet-500/20',  'text' => 'text-violet-400'],
                'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400'],
                'amber'   => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-400'],
                'sky'     => ['bg' => 'bg-sky-500/10',     'border' => 'border-sky-500/20',     'text' => 'text-sky-400'],
                'rose'    => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-400'],
            ];
        @endphp

        @foreach($shared as $item)
            @php $c = $colorMap[$item['color']]; @endphp
            <div class="bg-white/3 border border-white/8 rounded-2xl p-6">
                <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} border {{ $c['border'] }} flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-sm mb-2">{{ $item['title'] }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed">{{ $item['body'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============================================================ --}}
{{-- CTA                                                           --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-5">
            Not sure which plan fits?<br>Let's talk.
        </h2>
        <p class="text-gray-400 text-lg mb-10">
            Book a 20-minute call and we'll walk through exactly how smbgen maps to your workflow.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ Route::has('booking.wizard') ? route('booking.wizard') : route('contact') }}"
               class="px-8 py-4 rounded-xl bg-violet-500 hover:bg-violet-400 text-white font-bold transition-colors shadow-xl shadow-violet-900/30 text-sm">
                Book a free demo &rarr;
            </a>
            <a href="{{ route('contact') }}"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                Ask a question first
            </a>
        </div>
    </div>
</section>

@endsection
