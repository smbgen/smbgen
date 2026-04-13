@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
    $loginHref   = route('login');
@endphp

@section('title', 'smbgen for Health & Wellness — Appointment Booking, Client Portal & More')
@section('description', 'Built for therapists, personal trainers, chiropractors, and wellness practitioners. Online appointment booking, intake forms, Google Calendar sync, and a secure client portal — all in one place.')

@push('head')
<style>
    .hw-hero-bg {
        background:
            radial-gradient(ellipse at 65% -5%, rgba(20,184,166,0.15) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(168,85,247,0.10) 0%, transparent 50%),
            radial-gradient(ellipse at 95% 75%, rgba(16,185,129,0.07) 0%, transparent 45%),
            #06101d;
    }
    .hw-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .hw-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(20,184,166,0.25), 0 8px 32px rgba(20,184,166,0.08);
        transform: translateY(-2px);
    }
    .hw-gradient-text {
        background: linear-gradient(135deg, #2dd4bf, #a78bfa, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hw-badge {
        background: linear-gradient(135deg, rgba(20,184,166,0.15), rgba(168,85,247,0.12));
        border: 1px solid rgba(20,184,166,0.25);
    }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO                                                          --}}
{{-- ============================================================ --}}
<section class="hw-hero-bg min-h-[90vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full hw-badge text-teal-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                    Built for Health & Wellness Practitioners
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    Focus on clients.<br>
                    Not on scheduling<br>
                    <span class="hw-gradient-text">phone calls.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    Clients book appointments 24/7, fill out intake forms before their visit, and receive
                    professional confirmations automatically. You show up to every session knowing
                    exactly who you're working with.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=health-wellness"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-teal-500 hover:bg-teal-400 text-white font-bold transition-colors shadow-xl shadow-teal-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $loginHref }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Sign in to your account
                    </a>
                </div>

                <div class="flex items-center gap-3 text-gray-500 text-xs flex-wrap">
                    @foreach(['No credit card to start', 'Live in under a day', 'Works with Google Calendar'] as $p)
                        <span class="flex items-center gap-1 text-teal-400 font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $p }}
                        </span>
                        @if(!$loop->last)<span class="text-gray-700">·</span>@endif
                    @endforeach
                </div>
            </div>

            {{-- Right: Booking preview --}}
            <div class="relative">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl backdrop-blur-sm">

                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
                        <div>
                            <p class="text-white font-bold text-sm">Book an Appointment</p>
                            <p class="text-gray-400 text-xs mt-0.5">Therapy · Training · Chiropractic</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-teal-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Session type --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Session type</p>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach(['Initial Consult', 'Follow-up', 'Virtual Session', 'In-Person'] as $i => $type)
                            <button class="px-3 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 0
                                    ? 'bg-teal-500 text-white border-teal-400'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-teal-500/40' }}">
                                {{ $type }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Slots --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Available this week</p>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        @foreach(['Mon 9:00am', 'Mon 3:00pm', 'Tue 10:00am', 'Wed 9:00am', 'Wed 2:00pm', 'Thu 4:00pm'] as $i => $slot)
                            <button class="px-2 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 2
                                    ? 'bg-teal-500 text-white border-teal-400 shadow-lg shadow-teal-900/30'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-teal-500/40' }}">
                                {{ $slot }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Intake note --}}
                    <div class="bg-white/5 border border-teal-500/30 rounded-lg px-3 py-2.5 mb-4">
                        <p class="text-teal-300 text-xs font-medium mb-1">Health & wellness goals</p>
                        <p class="text-gray-500 text-xs">What are you hoping to work on?</p>
                    </div>

                    <button class="w-full py-3 rounded-xl bg-teal-500 hover:bg-teal-400 text-white font-bold text-sm transition-colors shadow-lg shadow-teal-900/20">
                        Confirm Appointment &rarr;
                    </button>

                    <div class="flex items-center justify-center gap-2 mt-4 text-gray-500 text-xs">
                        <svg class="w-4 h-4" viewBox="0 0 48 48"><rect x="4" y="4" width="40" height="40" rx="4" fill="#fff"/><rect x="12" y="4" width="4" height="8" rx="2" fill="#1a73e8"/><rect x="32" y="4" width="4" height="8" rx="2" fill="#1a73e8"/><rect x="4" y="16" width="40" height="2" fill="#1a73e8"/><text x="24" y="34" font-size="14" font-weight="700" text-anchor="middle" fill="#1a73e8">{{ now()->format('d') }}</text></svg>
                        Syncs instantly to Google Calendar
                    </div>
                </div>

                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -left-4 bg-gray-900 border border-teal-500/30 rounded-xl px-4 py-3 shadow-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-teal-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-teal-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-bold">Appointment confirmed</p>
                        <p class="text-gray-400 text-xs">Intake received · Calendar updated</p>
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
        <span class="text-teal-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">How it works</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            From first inquiry to first session in minutes.
        </h2>
    </div>

    <div class="max-w-5xl mx-auto grid md:grid-cols-4 gap-6">
        @php
            $steps = [
                ['num' => '01', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'title' => 'You set your schedule', 'body' => 'Connect your Google Calendar, set session types, durations, and available windows.'],
                ['num' => '02', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'title' => 'Client books & fills intake', 'body' => 'They pick session type, choose a time, and fill out your intake form — all before the session.'],
                ['num' => '03', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'It lands in your calendar', 'body' => 'Google Calendar event created with session type and intake notes. Review before they arrive.'],
                ['num' => '04', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'Ongoing via portal', 'body' => 'Returning clients log into the portal to rebook, view notes, and share documents securely.'],
            ];
        @endphp

        @foreach($steps as $step)
            <div class="hw-card-hover bg-white/3 border border-white/8 rounded-2xl p-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-teal-500/15 border border-teal-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-teal-500/60 text-xs font-black uppercase tracking-widest mb-2 block">{{ $step['num'] }}</span>
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
            <span class="text-teal-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">What you get</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                Built around the practitioner-client relationship.
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $features = [
                    ['color' => 'teal', 'title' => 'Appointment Booking', 'body' => 'Clients pick session type, duration, and available time. Everything syncs to your Google Calendar automatically.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['color' => 'violet', 'title' => 'Health Intake Forms', 'body' => 'Custom intake questions per session type. Capture goals, health history, and preferences before the first session.', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['color' => 'emerald', 'title' => 'Virtual Session Links', 'body' => 'Google Meet links auto-generate for online sessions. Client gets theirs in the confirmation email the moment they book.', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    ['color' => 'blue', 'title' => 'Recurring Client Portal', 'body' => 'Returning clients log in to rebook, view their schedule, and securely share documents like progress notes or plans.', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    ['color' => 'amber', 'title' => 'Client History & Notes', 'body' => "See each client's session history, intake responses, and notes before you meet — no digging through paper files.", 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['color' => 'rose', 'title' => 'Lead & Client CRM', 'body' => 'Every new booking becomes a client record. Track who is active, who needs follow-up, and who you want to reach out to.', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
                $colorMap = [
                    'teal'    => ['bg' => 'bg-teal-500/10',    'border' => 'border-teal-500/20',    'text' => 'text-teal-400',    'hover' => 'hover:border-teal-500/30'],
                    'violet'  => ['bg' => 'bg-violet-500/10',  'border' => 'border-violet-500/20',  'text' => 'text-violet-400',  'hover' => 'hover:border-violet-500/30'],
                    'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400', 'hover' => 'hover:border-emerald-500/30'],
                    'blue'    => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-400',    'hover' => 'hover:border-blue-500/30'],
                    'amber'   => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-400',   'hover' => 'hover:border-amber-500/30'],
                    'rose'    => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-400',    'hover' => 'hover:border-rose-500/30'],
                ];
            @endphp

            @foreach($features as $feature)
                @php $c = $colorMap[$feature['color']]; @endphp
                <div class="hw-card-hover bg-white/3 border border-white/8 {{ $c['hover'] }} rounded-2xl p-6">
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
{{-- WHO IT'S FOR                                                  --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-14">
        <span class="text-teal-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Who it's for</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            Any wellness practice. Any specialty.
        </h2>
    </div>
    <div class="max-w-3xl mx-auto grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach(['Therapists', 'Personal Trainers', 'Chiropractors', 'Nutritionists', 'Life Coaches', 'Massage Therapists', 'Yoga Instructors', 'Acupuncturists', 'Physical Therapists', 'Occupational Therapists', 'Speech Therapists', 'Dietitians'] as $type)
            <div class="bg-white/3 border border-white/8 rounded-xl px-4 py-3 text-center">
                <p class="text-gray-300 text-sm font-medium">{{ $type }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============================================================ --}}
{{-- PRICING                                                       --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-16">
        <span class="text-teal-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Simple pricing</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">One plan. No surprises.</h2>
        <p class="text-gray-400 mt-4 text-lg">Less than one missed session covers a full month.</p>
    </div>

    <div class="max-w-md mx-auto">
        <div class="bg-white/5 border border-teal-500/30 rounded-2xl p-8 shadow-2xl shadow-teal-900/10 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-teal-500/5 to-transparent pointer-events-none"></div>

            <div class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-teal-400 text-xs font-black uppercase tracking-widest mb-1">Practitioner Plan</p>
                        <div class="flex items-end gap-1">
                            <span class="text-5xl font-black text-white">$79</span>
                            <span class="text-gray-400 text-sm mb-2">/month</span>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full bg-teal-500/15 border border-teal-500/30 text-teal-300 text-xs font-bold">
                        Most Popular
                    </div>
                </div>

                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Google Calendar appointment booking',
                        'Session type & intake forms',
                        'Automated confirmation emails',
                        'Google Meet links for virtual sessions',
                        'Client portal for recurring clients',
                        'Client history & notes',
                        'Lead & client CRM',
                        'Unlimited bookings per month',
                        'Setup support included',
                    ] as $item)
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-teal-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ $bookHref }}?intent=health-wellness-signup"
                   class="block w-full text-center py-4 rounded-xl bg-teal-500 hover:bg-teal-400 text-white font-bold transition-colors shadow-lg shadow-teal-900/30 text-sm">
                    Get started today &rarr;
                </a>
                <p class="text-center text-gray-500 text-xs mt-4">30-day money-back guarantee. Cancel anytime.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FAQ                                                           --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5" x-data="{ open: null }">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-14">
            <span class="text-teal-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">FAQ</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Common questions</h2>
        </div>

        @php
            $faqs = [
                ['q' => 'Can I set different session lengths for different appointment types?', 'a' => 'Yes. You can configure multiple session types — each with its own duration and intake form. For example, a 60-minute initial consult and a 30-minute follow-up can both have separate availability windows.'],
                ['q' => 'Is client intake information kept private?', 'a' => 'Yes. Intake responses are only visible to your admin account. They are never shared or used for any purpose outside your practice.'],
                ['q' => 'Can I offer both in-person and virtual sessions?', 'a' => 'Yes. Clients choose their session format when booking. Virtual sessions automatically generate a Google Meet link in the confirmation email.'],
                ['q' => 'What if I need to cancel or reschedule?', 'a' => 'You manage all bookings from your admin dashboard. Changes update your Google Calendar automatically and you can contact the client through the platform.'],
                ['q' => 'Can clients rebook on their own?', 'a' => 'After their first session, clients can log into the client portal and book their next appointment directly from their account.'],
                ['q' => 'Is it hard to set up?', 'a' => 'No. Most practitioners are live in under an hour. We walk you through Google Calendar setup, your session types, and your booking link. Setup support is always included.'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $i => $faq)
                <div class="bg-white/3 border border-white/8 rounded-xl overflow-hidden">
                    <button
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left text-white font-semibold text-sm hover:text-teal-300 transition-colors">
                        <span>{{ $faq['q'] }}</span>
                        <svg class="w-4 h-4 shrink-0 transition-transform duration-200 text-gray-400"
                             :class="open === {{ $i }} ? 'rotate-45 text-teal-400' : ''"
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
<section class="bg-[#06101d] py-28 px-6 border-t border-white/5">
    <div class="max-w-3xl mx-auto text-center">

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full hw-badge text-teal-300 text-xs font-semibold mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
            Ready to get started?
        </div>

        <h2 class="text-4xl md:text-5xl font-black text-white mb-5 tracking-tight">
            Your first new client<br>could book tonight.
        </h2>

        <p class="text-gray-400 text-lg mb-10 leading-relaxed">
            Book a 20-minute call and we'll get your session types configured, your calendar connected,
            and your booking link ready to share — same day.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=health-wellness"
               class="px-8 py-4 rounded-xl bg-teal-500 hover:bg-teal-400 text-white font-bold transition-colors shadow-xl shadow-teal-900/30 text-sm">
                Book a 20-min demo &rarr;
            </a>
            <a href="{{ $contactHref }}?topic=health-wellness"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                Send a question first
            </a>
        </div>

        <p class="text-gray-600 text-xs mt-8">$79/month · Cancel anytime · Setup included</p>
    </div>
</section>

@endsection
