@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
@endphp

@section('title', 'Get More Leads — smbgen Solutions for Small Business Growth')
@section('description', 'Stop losing potential clients to a bad first impression. smbgen replaces generic contact forms with structured intake that qualifies leads, routes them intelligently, and feeds your CRM automatically.')

@push('head')
<style>
    .ml-hero-bg {
        background:
            radial-gradient(ellipse at 65% -10%, rgba(59,130,246,0.18) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(16,185,129,0.10) 0%, transparent 50%),
            #06101d;
    }
    .ml-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .ml-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(59,130,246,0.25), 0 8px 32px rgba(59,130,246,0.08);
        transform: translateY(-2px);
    }
    .ml-gradient-text {
        background: linear-gradient(135deg, #60a5fa, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
@endpush

@section('content')

{{-- ================================================================ --}}
{{-- HERO                                                              --}}
{{-- ================================================================ --}}
<section class="ml-hero-bg min-h-[85vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                    Lead Generation
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    Your website has<br>
                    traffic. You're not<br>
                    <span class="ml-gradient-text">capturing any of it.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    A generic "Name / Email / Message" form isn't a lead capture strategy — it's a dead end.
                    smbgen gives your contact flow structure, qualification, and an automatic path into your CRM.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=more-leads"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-colors shadow-xl shadow-blue-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $contactHref }}?topic=lead-gen"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Ask a specific question
                    </a>
                </div>

                <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">
                    @foreach(['Structured intake forms', 'Auto-routes into CRM', 'No-code setup'] as $point)
                        <span class="flex items-center gap-1.5 text-blue-400 font-medium">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $point }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Right: before / after comparison --}}
            <div class="space-y-4">
                {{-- Before --}}
                <div class="bg-red-950/30 border border-red-800/30 rounded-2xl p-6">
                    <p class="text-red-400 text-xs font-bold uppercase tracking-widest mb-4">Before smbgen</p>
                    <div class="space-y-3">
                        @foreach([
                            'Generic "contact us" form sits ignored',
                            'Visitor leaves — you never know why',
                            'No qualification, no routing, no follow-up',
                            'Leads manually copied into a spreadsheet',
                            'Response time: hours or days',
                        ] as $item)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-red-900/60 border border-red-700/40 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                                <span class="text-gray-400 text-sm">{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- After --}}
                <div class="bg-emerald-950/30 border border-emerald-800/30 rounded-2xl p-6">
                    <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-4">With smbgen</p>
                    <div class="space-y-3">
                        @foreach([
                            'Structured intake qualifies every visitor',
                            'Hot leads auto-flagged and routed to CRM',
                            'Instant notification + automated follow-up',
                            'Every submission tracked with full history',
                            'Response time: minutes, automatically',
                        ] as $item)
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-emerald-900/60 border border-emerald-700/40 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                                <span class="text-gray-200 text-sm">{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- PAIN POINTS                                                       --}}
{{-- ================================================================ --}}
<section class="bg-[#060e1a] py-20 px-6 border-y border-white/5">
    <div class="max-w-5xl mx-auto">
        <p class="text-center text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mb-12">The bottlenecks killing your lead flow</p>
        <div class="grid md:grid-cols-3 gap-6">
            @php
                $pains = [
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'title' => 'Your form asks the wrong questions',
                        'body' => '"What\'s your message?" doesn\'t tell you anything useful. You need to know what they want, when they need it, and whether it\'s a real opportunity.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'No system after the form submits',
                        'body' => 'The lead hits an inbox. Maybe it\'s seen in an hour, maybe a day. No CRM entry. No follow-up sequence. No record that it even happened.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
                        'title' => 'Traffic without conversion is noise',
                        'body' => 'You might be ranking on Google, running ads, or posting on social — but if the intake experience doesn\'t convert, all that effort is wasted.',
                    ],
                ];
            @endphp
            @foreach($pains as $pain)
                <div class="bg-white/3 border border-white/8 rounded-2xl p-6">
                    <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $pain['icon'] !!}</svg>
                    </div>
                    <p class="text-white font-bold text-sm mb-2">{{ $pain['title'] }}</p>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $pain['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- HOW IT WORKS                                                      --}}
{{-- ================================================================ --}}
<section class="bg-[#06101d] py-24 px-6">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <p class="text-blue-400 text-xs font-bold uppercase tracking-[0.2em] mb-3">The smbgen difference</p>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">From "contact us" to closed deal</h2>
            <p class="text-gray-400 mt-4 max-w-xl mx-auto">Every step between a stranger's first click and a signed client is covered by one connected system.</p>
        </div>

        <div class="space-y-4">
            @php
                $steps = [
                    [
                        'num' => '01',
                        'label' => 'Capture',
                        'title' => 'A contact page that actually qualifies',
                        'body' => 'Ask the right questions upfront. Service type, budget range, timeline, how they heard about you. Structure the intake so the first real conversation is already informed.',
                        'color' => 'blue',
                    ],
                    [
                        'num' => '02',
                        'label' => 'Route',
                        'title' => 'Every lead lands in your CRM instantly',
                        'body' => 'No more copying from email. Form submissions create CRM contacts automatically with all intake data attached. You know exactly what each lead needs before you pick up the phone.',
                        'color' => 'violet',
                    ],
                    [
                        'num' => '03',
                        'label' => 'Notify',
                        'title' => 'You\'re alerted the moment it happens',
                        'body' => 'Email notification, admin dashboard flag, and a lead record — the moment someone submits. Speed matters more than almost anything else in the first response.',
                        'color' => 'emerald',
                    ],
                    [
                        'num' => '04',
                        'label' => 'Nurture',
                        'title' => 'Not ready yet? They stay in your orbit',
                        'body' => 'Leads that aren\'t ready to buy today get stored in your CRM with notes, next-action dates, and history. Follow up at the right time with context.',
                        'color' => 'orange',
                    ],
                ];
                $colorMap = [
                    'blue'   => ['badge' => 'bg-blue-600/20 text-blue-300 border-blue-600/30', 'num' => 'text-blue-400'],
                    'violet' => ['badge' => 'bg-violet-600/20 text-violet-300 border-violet-600/30', 'num' => 'text-violet-400'],
                    'emerald'=> ['badge' => 'bg-emerald-600/20 text-emerald-300 border-emerald-600/30', 'num' => 'text-emerald-400'],
                    'orange' => ['badge' => 'bg-orange-600/20 text-orange-300 border-orange-600/30', 'num' => 'text-orange-400'],
                ];
            @endphp

            @foreach($steps as $step)
                @php $c = $colorMap[$step['color']]; @endphp
                <div class="bg-white/3 border border-white/8 rounded-2xl p-7 flex gap-6 items-start">
                    <div class="shrink-0 w-12 h-12 rounded-xl {{ $c['badge'] }} border flex items-center justify-center font-black text-sm">
                        {{ $step['num'] }}
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest {{ $c['num'] }} mb-1">{{ $step['label'] }}</p>
                        <p class="text-white font-bold text-base mb-2">{{ $step['title'] }}</p>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $step['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- RELEVANT TOOLS                                                    --}}
{{-- ================================================================ --}}
<section class="bg-[#060e1a] py-20 px-6 border-t border-white/5">
    <div class="max-w-5xl mx-auto">
        <p class="text-center text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mb-10">The smbgen-core tools behind this</p>
        <div class="grid sm:grid-cols-3 gap-5">
            <a href="{{ route('product.contact') }}" class="ml-card-hover bg-blue-600/8 border border-blue-600/20 rounded-2xl p-6 group">
                <p class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-2">Contact</p>
                <p class="text-white font-bold mb-2">Superior intake forms</p>
                <p class="text-gray-400 text-sm leading-relaxed">Structured, qualifying contact experiences that do more than collect a name.</p>
                <span class="text-blue-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore Contact <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
            <a href="{{ route('product.crm') }}" class="ml-card-hover bg-indigo-600/8 border border-indigo-600/20 rounded-2xl p-6 group">
                <p class="text-indigo-400 text-xs font-bold uppercase tracking-widest mb-2">CRM</p>
                <p class="text-white font-bold mb-2">Pipeline & contact tracking</p>
                <p class="text-gray-400 text-sm leading-relaxed">Every lead stored, tracked, and ready for follow-up with full interaction history.</p>
                <span class="text-indigo-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore CRM <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
            <a href="{{ route('product.cms') }}" class="ml-card-hover bg-cyan-600/8 border border-cyan-600/20 rounded-2xl p-6 group">
                <p class="text-cyan-400 text-xs font-bold uppercase tracking-widest mb-2">CMS</p>
                <p class="text-white font-bold mb-2">Landing pages that convert</p>
                <p class="text-gray-400 text-sm leading-relaxed">Build service pages, campaign landing pages, and lead magnets — no dev required.</p>
                <span class="text-cyan-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore CMS <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- CTA                                                               --}}
{{-- ================================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-4">
            Ready to stop leaving leads on the table?
        </h2>
        <p class="text-gray-400 text-lg mb-8">
            Book a 20-minute call and we'll walk through exactly how smbgen would change your lead capture and follow-up.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=more-leads"
               class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm transition-colors shadow-xl shadow-blue-900/30">
                Book a demo &rarr;
            </a>
            <a href="{{ route('solutions.streamline-bookings') }}"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-semibold text-sm border border-white/10 transition-colors">
                Next: Streamline Bookings
            </a>
        </div>
    </div>
</section>

@endsection
