@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
    $loginHref   = route('login');
@endphp

@section('title', 'smbgen for Consultants & Advisors — Discovery Calls, Client Portal & Proposals')
@section('description', 'Built for business consultants, financial advisors, and coaches. Online discovery call booking, proposal sharing, a professional client portal, and Google Calendar sync — all in one platform.')

@push('head')
<style>
    .cons-hero-bg {
        background:
            radial-gradient(ellipse at 65% -5%, rgba(245,158,11,0.15) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(99,102,241,0.10) 0%, transparent 50%),
            radial-gradient(ellipse at 95% 75%, rgba(16,185,129,0.07) 0%, transparent 45%),
            #06101d;
    }
    .cons-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .cons-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(245,158,11,0.25), 0 8px 32px rgba(245,158,11,0.08);
        transform: translateY(-2px);
    }
    .cons-gradient-text {
        background: linear-gradient(135deg, #fbbf24, #a78bfa, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .cons-badge {
        background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(99,102,241,0.12));
        border: 1px solid rgba(245,158,11,0.25);
    }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO                                                          --}}
{{-- ============================================================ --}}
<section class="cons-hero-bg min-h-[90vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full cons-badge text-amber-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Built for Consultants & Advisors
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    No more chasing leads.<br>
                    Let clients book their<br>
                    <span class="cons-gradient-text">discovery call.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    Prospects book a discovery call directly on your calendar. They fill out a brief intake form.
                    You walk into every call knowing their goals, budget, and timeline — ready to close.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=consulting"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold transition-colors shadow-xl shadow-amber-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $loginHref }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Sign in to your account
                    </a>
                </div>

                <div class="flex items-center gap-3 text-gray-500 text-xs flex-wrap">
                    @foreach(['No credit card to start', 'Live in under a day', 'Works with Google Calendar'] as $p)
                        <span class="flex items-center gap-1 text-amber-400 font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $p }}
                        </span>
                        @if(!$loop->last)<span class="text-gray-700">·</span>@endif
                    @endforeach
                </div>
            </div>

            {{-- Right: Discovery call booking card --}}
            <div class="relative">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl backdrop-blur-sm">

                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
                        <div>
                            <p class="text-white font-bold text-sm">Book a Discovery Call</p>
                            <p class="text-gray-400 text-xs mt-0.5">30 min · Business Strategy · Free</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Engagement type --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">What are you working on?</p>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach(['Business Strategy', 'Financial Planning', 'Marketing', 'Operations'] as $i => $type)
                            <button class="px-3 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 0
                                    ? 'bg-amber-500 text-white border-amber-400'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-amber-500/40' }}">
                                {{ $type }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Time slots --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Available times</p>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        @foreach(['Mon 9:00am', 'Mon 1:00pm', 'Tue 10:00am', 'Wed 2:00pm', 'Thu 9:00am', 'Thu 3:00pm'] as $i => $slot)
                            <button class="px-2 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 4
                                    ? 'bg-amber-500 text-white border-amber-400 shadow-lg shadow-amber-900/30'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-amber-500/40' }}">
                                {{ $slot }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Intake --}}
                    <div class="bg-white/5 border border-amber-500/30 rounded-lg px-3 py-2.5 mb-4">
                        <p class="text-amber-300 text-xs font-medium mb-1">What's your biggest challenge right now?</p>
                        <p class="text-gray-500 text-xs">A quick summary helps us prepare...</p>
                    </div>

                    <button class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-sm transition-colors shadow-lg shadow-amber-900/20">
                        Book Discovery Call &rarr;
                    </button>

                    <div class="flex items-center justify-center gap-2 mt-4 text-gray-500 text-xs">
                        <svg class="w-4 h-4" viewBox="0 0 48 48"><rect x="4" y="4" width="40" height="40" rx="4" fill="#fff"/><rect x="12" y="4" width="4" height="8" rx="2" fill="#1a73e8"/><rect x="32" y="4" width="4" height="8" rx="2" fill="#1a73e8"/><rect x="4" y="16" width="40" height="2" fill="#1a73e8"/><text x="24" y="34" font-size="14" font-weight="700" text-anchor="middle" fill="#1a73e8">{{ now()->format('d') }}</text></svg>
                        Syncs instantly to Google Calendar
                    </div>
                </div>

                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -left-4 bg-gray-900 border border-amber-500/30 rounded-xl px-4 py-3 shadow-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-bold">Discovery call booked</p>
                        <p class="text-gray-400 text-xs">Google Meet link sent · Brief received</p>
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
        <span class="text-amber-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">How it works</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            A slicker pipeline from stranger to client.
        </h2>
    </div>

    <div class="max-w-5xl mx-auto grid md:grid-cols-4 gap-6">
        @php
            $steps = [
                ['num' => '01', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'title' => 'Share your booking link', 'body' => 'Drop it in your email signature, LinkedIn, or website. Prospects click and book without back-and-forth.'],
                ['num' => '02', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Prospect fills intake brief', 'body' => "They describe their challenge, goals, timeline, and budget. You know what you're walking into."],
                ['num' => '03', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'title' => 'Google Meet call', 'body' => 'Meet link auto-generated and sent in the confirmation. No more hunting for links or sharing manually.'],
                ['num' => '04', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'title' => 'Send proposal via portal', 'body' => 'After a successful call, upload the proposal to the client portal. They review and sign off. Job done.'],
            ];
        @endphp

        @foreach($steps as $step)
            <div class="cons-card-hover bg-white/3 border border-white/8 rounded-2xl p-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-amber-500/60 text-xs font-black uppercase tracking-widest mb-2 block">{{ $step['num'] }}</span>
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
            <span class="text-amber-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">What you get</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                Everything you need to<br class="hidden md:block"> run a tight consulting pipeline.
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $features = [
                    ['color' => 'amber', 'title' => 'Discovery Call Booking', 'body' => 'Prospects book directly onto your calendar. Choose engagement type, fill a brief, and get a Google Meet link — zero friction.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['color' => 'violet', 'title' => 'Pre-Call Intake Briefs', 'body' => 'Custom questions per engagement type. Capture business goals, budget, timeline, and current challenges before the call.', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['color' => 'blue', 'title' => 'Google Meet on Every Call', 'body' => 'Every virtual call auto-generates a Google Meet link sent in the confirmation email. No manual scheduling links needed.', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    ['color' => 'emerald', 'title' => 'Proposal & Document Portal', 'body' => 'Share proposals, reports, and deliverables securely through the client portal. Clients review and respond in one place.', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['color' => 'sky', 'title' => 'Prospect & Client CRM', 'body' => 'Every booking becomes a lead in your CRM. Track where each prospect is in your pipeline and follow up faster.', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['color' => 'rose', 'title' => 'Secure Messaging', 'body' => 'Message clients directly through the platform. No more sifting through email threads to find the last thing you agreed on.', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                ];
                $colorMap = [
                    'amber'   => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-400',   'hover' => 'hover:border-amber-500/30'],
                    'violet'  => ['bg' => 'bg-violet-500/10',  'border' => 'border-violet-500/20',  'text' => 'text-violet-400',  'hover' => 'hover:border-violet-500/30'],
                    'blue'    => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-400',    'hover' => 'hover:border-blue-500/30'],
                    'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400', 'hover' => 'hover:border-emerald-500/30'],
                    'sky'     => ['bg' => 'bg-sky-500/10',     'border' => 'border-sky-500/20',     'text' => 'text-sky-400',     'hover' => 'hover:border-sky-500/30'],
                    'rose'    => ['bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20',    'text' => 'text-rose-400',    'hover' => 'hover:border-rose-500/30'],
                ];
            @endphp

            @foreach($features as $feature)
                @php $c = $colorMap[$feature['color']]; @endphp
                <div class="cons-card-hover bg-white/3 border border-white/8 {{ $c['hover'] }} rounded-2xl p-6">
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
        <span class="text-amber-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Who it's for</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            Any advisory business. Any niche.
        </h2>
    </div>
    <div class="max-w-3xl mx-auto grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach(['Business Consultants', 'Financial Advisors', 'Executive Coaches', 'Marketing Consultants', 'HR Consultants', 'IT Consultants', 'Management Consultants', 'Sales Coaches', 'Leadership Coaches', 'Fractional CFOs', 'Brand Strategists', 'Operations Advisors'] as $type)
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
        <span class="text-amber-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Simple pricing</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">One plan. No surprises.</h2>
        <p class="text-gray-400 mt-4 text-lg">One new client from a booked discovery call covers this for months.</p>
    </div>

    <div class="max-w-md mx-auto">
        <div class="bg-white/5 border border-amber-500/30 rounded-2xl p-8 shadow-2xl shadow-amber-900/10 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent pointer-events-none"></div>

            <div class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-amber-400 text-xs font-black uppercase tracking-widest mb-1">Advisor Plan</p>
                        <div class="flex items-end gap-1">
                            <span class="text-5xl font-black text-white">$79</span>
                            <span class="text-gray-400 text-sm mb-2">/month</span>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-bold">
                        Most Popular
                    </div>
                </div>

                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Discovery call & meeting booking',
                        'Pre-call intake brief forms',
                        'Google Calendar sync',
                        'Google Meet links on every call',
                        'Client portal with document sharing',
                        'Secure client messaging',
                        'Prospect & client CRM',
                        'Unlimited bookings per month',
                        'Setup support included',
                    ] as $item)
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ $bookHref }}?intent=consulting-signup"
                   class="block w-full text-center py-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold transition-colors shadow-lg shadow-amber-900/30 text-sm">
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
            <span class="text-amber-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">FAQ</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Common questions</h2>
        </div>

        @php
            $faqs = [
                ['q' => 'Can I offer different types of calls — discovery, check-in, strategy?', 'a' => 'Yes. You can configure multiple meeting types, each with its own duration, intake questions, and availability windows. Prospects select the right type when they book.'],
                ['q' => 'Can I share proposals and deliverables through the platform?', 'a' => 'Yes. The client portal lets you upload any file — proposals, reports, contracts, slide decks. Clients log in to view and download them securely.'],
                ['q' => 'Does it work if I have multiple consultants?', 'a' => 'Yes. You can add multiple users, each with their own Google Calendar connection and booking schedule. Useful for consulting teams or practices with multiple advisors.'],
                ['q' => 'How does the intake brief work?', 'a' => "When a prospect books a call, they fill in your intake form — typically: company overview, challenge, goals, budget range, and timeline. This lands in your dashboard alongside the booking so you're prepared before the call."],
                ['q' => 'Does it replace my CRM?', 'a' => 'Not entirely — it is a lightweight CRM focused on bookings, leads, and client communication. If you use a full CRM, smbgen works alongside it as your client-facing intake and portal layer.'],
                ['q' => 'How quickly can I get started?', 'a' => 'Most consultants are live in under an hour. Connect Google Calendar, configure your meeting types and intake questions, share your booking link. Setup support is always included.'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $i => $faq)
                <div class="bg-white/3 border border-white/8 rounded-xl overflow-hidden">
                    <button
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left text-white font-semibold text-sm hover:text-amber-300 transition-colors">
                        <span>{{ $faq['q'] }}</span>
                        <svg class="w-4 h-4 shrink-0 transition-transform duration-200 text-gray-400"
                             :class="open === {{ $i }} ? 'rotate-45 text-amber-400' : ''"
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

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full cons-badge text-amber-300 text-xs font-semibold mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
            Ready to close more clients?
        </div>

        <h2 class="text-4xl md:text-5xl font-black text-white mb-5 tracking-tight">
            Your next client<br>could book this week.
        </h2>

        <p class="text-gray-400 text-lg mb-10 leading-relaxed">
            Book a 20-minute call and we'll configure your discovery call flow, intake brief,
            and client portal — ready to share with prospects the same day.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=consulting"
               class="px-8 py-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold transition-colors shadow-xl shadow-amber-900/30 text-sm">
                Book a 20-min demo &rarr;
            </a>
            <a href="{{ $contactHref }}?topic=consulting"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                Send a question first
            </a>
        </div>

        <p class="text-gray-600 text-xs mt-8">$79/month · Cancel anytime · Setup included</p>
    </div>
</section>

@endsection
