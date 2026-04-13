@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
    $loginHref   = route('login');
@endphp

@section('title', 'smbgen for Law Firms & Attorneys — Consultation Booking, Intake Forms & Client Portal')
@section('description', 'Built for attorneys and law firms. Online consultation scheduling, confidential intake forms, a secure client portal, and document sharing — all in one professional platform.')

@push('head')
<style>
    .legal-hero-bg {
        background:
            radial-gradient(ellipse at 65% -5%, rgba(59,130,246,0.15) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(99,102,241,0.10) 0%, transparent 50%),
            radial-gradient(ellipse at 95% 75%, rgba(234,179,8,0.07) 0%, transparent 45%),
            #06101d;
    }
    .legal-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .legal-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(59,130,246,0.25), 0 8px 32px rgba(59,130,246,0.08);
        transform: translateY(-2px);
    }
    .legal-gradient-text {
        background: linear-gradient(135deg, #60a5fa, #a78bfa, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .legal-badge {
        background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(99,102,241,0.12));
        border: 1px solid rgba(59,130,246,0.25);
    }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO                                                          --}}
{{-- ============================================================ --}}
<section class="legal-hero-bg min-h-[90vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full legal-badge text-blue-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                    Built for Law Firms & Attorneys
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    Book more consults.<br>
                    Intake clients<br>
                    <span class="legal-gradient-text">professionally.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    Prospective clients book a consultation online, fill out a confidential intake form before the call,
                    and receive a professional confirmation email automatically.
                    You walk into every consult fully prepared.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=legal"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-colors shadow-xl shadow-blue-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $loginHref }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Sign in to your account
                    </a>
                </div>

                <div class="flex items-center gap-3 text-gray-500 text-xs flex-wrap">
                    @foreach(['No credit card to start', 'Live in under a day', 'Works with Google Calendar'] as $p)
                        <span class="flex items-center gap-1 text-blue-400 font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $p }}
                        </span>
                        @if(!$loop->last)<span class="text-gray-700">·</span>@endif
                    @endforeach
                </div>
            </div>

            {{-- Right: Intake card preview --}}
            <div class="relative">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl backdrop-blur-sm">

                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
                        <div>
                            <p class="text-white font-bold text-sm">Initial Consultation Request</p>
                            <p class="text-gray-400 text-xs mt-0.5">Family Law · Estate Planning · Business Law</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Practice area --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Practice area</p>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach(['Family Law', 'Business Law', 'Estate Planning', 'Real Estate Law'] as $i => $area)
                            <button class="px-3 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 0
                                    ? 'bg-blue-600 text-white border-blue-500'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-blue-500/40' }}">
                                {{ $area }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Time slots --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Available consultations</p>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        @foreach(['Mon 10:00am', 'Mon 2:00pm', 'Tue 9:00am', 'Tue 3:00pm', 'Wed 11:00am', 'Thu 10:00am'] as $i => $slot)
                            <button class="px-2 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 3
                                    ? 'bg-blue-600 text-white border-blue-500 shadow-lg shadow-blue-900/30'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-blue-500/40' }}">
                                {{ $slot }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Intake question --}}
                    <div class="bg-white/5 border border-blue-500/30 rounded-lg px-3 py-2.5 mb-4">
                        <p class="text-blue-300 text-xs font-medium mb-1">Briefly describe your legal matter</p>
                        <p class="text-gray-500 text-xs">This helps us prepare before your consultation...</p>
                    </div>

                    <button class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm transition-colors shadow-lg shadow-blue-900/20">
                        Request Consultation &rarr;
                    </button>

                    <p class="text-center text-gray-600 text-xs mt-3">
                        <svg class="w-3 h-3 inline mr-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Intake information is kept strictly confidential
                    </p>
                </div>

                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -left-4 bg-gray-900 border border-blue-500/30 rounded-xl px-4 py-3 shadow-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-bold">Consultation booked</p>
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
        <span class="text-blue-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">How it works</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            A better intake experience for every client.
        </h2>
    </div>

    <div class="max-w-5xl mx-auto grid md:grid-cols-4 gap-6">
        @php
            $steps = [
                ['num' => '01', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'title' => 'You configure your page', 'body' => 'Set your practice areas, consultation types, availability windows, and intake questions.'],
                ['num' => '02', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Prospect fills intake form', 'body' => 'They select a practice area, pick a consultation time, and describe their legal situation.'],
                ['num' => '03', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'It lands in your calendar', 'body' => 'A Google Calendar event is created with all intake notes so you review them before the call.'],
                ['num' => '04', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'title' => 'Consultation, then portal', 'body' => 'New clients get access to the client portal to securely exchange documents and messages.'],
            ];
        @endphp

        @foreach($steps as $step)
            <div class="legal-card-hover bg-white/3 border border-white/8 rounded-2xl p-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 border border-blue-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-blue-500/60 text-xs font-black uppercase tracking-widest mb-2 block">{{ $step['num'] }}</span>
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
            <span class="text-blue-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">What you get</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                Everything a modern law practice needs<br class="hidden md:block"> to run efficiently.
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $features = [
                    ['color' => 'blue', 'title' => 'Consultation Scheduling', 'body' => 'Prospects book initial consultations online. Practice area, time slot, and intake all captured upfront.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['color' => 'violet', 'title' => 'Confidential Intake Forms', 'body' => 'Custom questions per practice area. Collect case background before the call — walk in prepared every time.', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['color' => 'emerald', 'title' => 'Secure Client Portal', 'body' => 'Clients log in to share documents, review files you upload, and communicate through a private messaging channel.', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    ['color' => 'amber', 'title' => 'Document Management', 'body' => 'Upload retainer agreements, case documents, and invoices directly to the client portal for secure access.', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['color' => 'sky', 'title' => 'Google Meet for Virtual Consults', 'body' => 'Every virtual consultation automatically generates a Google Meet link included in the confirmation email.', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    ['color' => 'rose', 'title' => 'Lead & Prospect CRM', 'body' => 'Every consultation request becomes a lead. Track follow-ups, conversions, and active matters in one place.', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
                $colorMap = [
                    'blue'    => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-400',    'hover' => 'hover:border-blue-500/30'],
                    'violet'  => ['bg' => 'bg-violet-500/10',  'border' => 'border-violet-500/20',  'text' => 'text-violet-400',  'hover' => 'hover:border-violet-500/30'],
                    'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400', 'hover' => 'hover:border-emerald-500/30'],
                    'amber'   => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-400',   'hover' => 'hover:border-amber-500/30'],
                    'sky'     => ['bg' => 'bg-sky-500/10',     'border' => 'border-sky-500/20',     'text' => 'text-sky-400',     'hover' => 'hover:border-sky-500/30'],
                    'rose'    => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-400',    'hover' => 'hover:border-rose-500/30'],
                ];
            @endphp

            @foreach($features as $feature)
                @php $c = $colorMap[$feature['color']]; @endphp
                <div class="legal-card-hover bg-white/3 border border-white/8 {{ $c['hover'] }} rounded-2xl p-6">
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
{{-- PRACTICE TYPES                                                --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-14">
        <span class="text-blue-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Practice areas</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            Works across every practice.
        </h2>
    </div>
    <div class="max-w-3xl mx-auto grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach(['Family Law', 'Estate Planning', 'Business Law', 'Real Estate Law', 'Immigration', 'Personal Injury', 'Criminal Defense', 'Employment Law', 'Bankruptcy', 'Intellectual Property', 'Tax Law', 'Civil Litigation'] as $area)
            <div class="bg-white/3 border border-white/8 rounded-xl px-4 py-3 text-center">
                <p class="text-gray-300 text-sm font-medium">{{ $area }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============================================================ --}}
{{-- PRICING                                                       --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-16">
        <span class="text-blue-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Simple pricing</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">One plan. No surprises.</h2>
        <p class="text-gray-400 mt-4 text-lg">Less than one billed hour covers a full month.</p>
    </div>

    <div class="max-w-md mx-auto">
        <div class="bg-white/5 border border-blue-500/30 rounded-2xl p-8 shadow-2xl shadow-blue-900/10 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent pointer-events-none"></div>

            <div class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-blue-400 text-xs font-black uppercase tracking-widest mb-1">Firm Plan</p>
                        <div class="flex items-end gap-1">
                            <span class="text-5xl font-black text-white">$79</span>
                            <span class="text-gray-400 text-sm mb-2">/month</span>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full bg-blue-500/15 border border-blue-500/30 text-blue-300 text-xs font-bold">
                        Most Popular
                    </div>
                </div>

                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Consultation booking with practice area selection',
                        'Confidential intake forms per practice area',
                        'Google Calendar booking sync',
                        'Google Meet links for virtual consults',
                        'Secure client portal with document sharing',
                        'Lead & prospect CRM',
                        'Automated confirmation emails',
                        'Admin dashboard for all matters',
                        'Setup support included',
                    ] as $item)
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ $bookHref }}?intent=legal-signup"
                   class="block w-full text-center py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-colors shadow-lg shadow-blue-900/30 text-sm">
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
            <span class="text-blue-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">FAQ</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Common questions</h2>
        </div>

        @php
            $faqs = [
                ['q' => 'Is the intake information kept private?', 'a' => 'Yes. Intake data is only visible to your firm admin account. It is never shared, sold, or used for any purpose other than to prepare you for your consultation.'],
                ['q' => 'Can I set different intake forms for different practice areas?', 'a' => 'Yes. You can configure custom intake questions per practice area so you always get the specific context you need before a consultation.'],
                ['q' => 'Does it work for solo attorneys and larger firms?', 'a' => 'Both. A solo attorney gets a streamlined personal booking page. Larger firms can add multiple users, each with their own calendar connection and booking schedule.'],
                ['q' => 'Can clients securely upload documents during intake?', 'a' => 'Clients can share files through the portal after their first consultation. The intake form captures text answers — document exchange happens through the secure client portal.'],
                ['q' => 'Does it integrate with our existing practice management software?', 'a' => 'smbgen is a standalone intake and client portal platform. It does not currently integrate with practice management software (Clio, MyCase, etc.) but works well alongside them.'],
                ['q' => 'How quickly can we go live?', 'a' => 'Most firms are live in under a day. Connect Google Calendar, configure your practice areas and availability, and share your booking link. Setup support is included.'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $i => $faq)
                <div class="bg-white/3 border border-white/8 rounded-xl overflow-hidden">
                    <button
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left text-white font-semibold text-sm hover:text-blue-300 transition-colors">
                        <span>{{ $faq['q'] }}</span>
                        <svg class="w-4 h-4 shrink-0 transition-transform duration-200 text-gray-400"
                             :class="open === {{ $i }} ? 'rotate-45 text-blue-400' : ''"
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

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full legal-badge text-blue-300 text-xs font-semibold mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
            Ready to modernise your intake?
        </div>

        <h2 class="text-4xl md:text-5xl font-black text-white mb-5 tracking-tight">
            Your next consultation<br>could book itself.
        </h2>

        <p class="text-gray-400 text-lg mb-10 leading-relaxed">
            Book a 20-minute call and we'll walk through how smbgen maps to your firm's intake workflow —
            and get you live the same day.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=legal"
               class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-colors shadow-xl shadow-blue-900/30 text-sm">
                Book a 20-min demo &rarr;
            </a>
            <a href="{{ $contactHref }}?topic=legal"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                Send a question first
            </a>
        </div>

        <p class="text-gray-600 text-xs mt-8">$79/month · Cancel anytime · Setup included</p>
    </div>
</section>

@endsection
