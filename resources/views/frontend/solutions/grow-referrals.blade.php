@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
@endphp

@section('title', 'Grow Through Referrals — Turn Happy Clients Into New Business | smbgen')
@section('description', 'Your best clients know other great clients. smbgen gives you the CRM tools and communication system to consistently ask, track, and reward referrals — so word-of-mouth actually scales.')

@push('head')
<style>
    .gr-hero-bg {
        background:
            radial-gradient(ellipse at 65% -10%, rgba(234,179,8,0.14) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(16,185,129,0.10) 0%, transparent 50%),
            #06101d;
    }
    .gr-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .gr-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(234,179,8,0.25), 0 8px 32px rgba(234,179,8,0.08);
        transform: translateY(-2px);
    }
    .gr-gradient-text {
        background: linear-gradient(135deg, #fbbf24, #34d399);
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
<section class="gr-hero-bg min-h-[85vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-yellow-500/10 border border-yellow-500/20 text-yellow-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    Referrals & Growth
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    Word-of-mouth is<br>
                    your best channel.<br>
                    <span class="gr-gradient-text">You have no system for it.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    The clients who love you the most are sitting on referrals they've never sent because you never asked.
                    smbgen gives you the CRM infrastructure to make referrals a repeatable, trackable growth lever.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=grow-referrals"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-yellow-600 hover:bg-yellow-500 text-white font-bold transition-colors shadow-xl shadow-yellow-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $contactHref }}?topic=referrals"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Ask a specific question
                    </a>
                </div>

                <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">
                    @foreach(['CRM-tracked referral sources', 'Post-delivery follow-up system', 'Warm intro workflows'] as $point)
                        <span class="flex items-center gap-1.5 text-yellow-400 font-medium">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $point }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Right: referral flywheel visual --}}
            <div class="space-y-4">

                {{-- Flywheel steps --}}
                @php
                    $flywheel = [
                        ['step' => 'Deliver', 'desc' => 'Exceptional work + portal experience', 'color' => 'emerald'],
                        ['step' => 'Retain', 'desc' => 'Stay connected via portal & follow-ups', 'color' => 'blue'],
                        ['step' => 'Ask', 'desc' => 'CRM-scheduled referral request at 30 days', 'color' => 'violet'],
                        ['step' => 'Track', 'desc' => 'New lead tagged with referrer source in CRM', 'color' => 'orange'],
                        ['step' => 'Thank', 'desc' => 'Close the loop — acknowledge & reward the referrer', 'color' => 'yellow'],
                    ];
                    $flywheelColors = [
                        'emerald' => 'bg-emerald-600/20 border-emerald-600/30 text-emerald-300',
                        'blue'    => 'bg-blue-600/20 border-blue-600/30 text-blue-300',
                        'violet'  => 'bg-violet-600/20 border-violet-600/30 text-violet-300',
                        'orange'  => 'bg-orange-600/20 border-orange-600/30 text-orange-300',
                        'yellow'  => 'bg-yellow-600/20 border-yellow-600/30 text-yellow-300',
                    ];
                @endphp
                <div class="bg-white/4 border border-white/10 rounded-2xl p-6 shadow-xl">
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-5">The Referral Flywheel</p>
                    <div class="space-y-3">
                        @foreach($flywheel as $i => $item)
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 flex flex-col items-center">
                                    <div class="w-7 h-7 rounded-full {{ $flywheelColors[$item['color']] }} border flex items-center justify-center text-xs font-black">
                                        {{ $i + 1 }}
                                    </div>
                                    @if($i < count($flywheel) - 1)
                                        <div class="w-px h-4 bg-white/10 mt-1"></div>
                                    @endif
                                </div>
                                <div class="pt-0.5">
                                    <span class="text-white font-bold text-sm">{{ $item['step'] }}</span>
                                    <span class="text-gray-400 text-xs ml-2">{{ $item['desc'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 pt-4 border-t border-white/8 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></div>
                        <span class="text-yellow-300 text-xs font-semibold">Flywheel compounds over time — every referral brings more referrals.</span>
                    </div>
                </div>

                {{-- Stat callout --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white/4 border border-white/8 rounded-xl p-4 text-center">
                        <p class="text-3xl font-black text-white">5×</p>
                        <p class="text-gray-400 text-xs mt-1">cheaper to retain than acquire</p>
                    </div>
                    <div class="bg-white/4 border border-white/8 rounded-xl p-4 text-center">
                        <p class="text-3xl font-black text-white">83%</p>
                        <p class="text-gray-400 text-xs mt-1">of satisfied clients would refer — if asked</p>
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
        <p class="text-center text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mb-12">Why referrals don't happen even when clients love you</p>
        <div class="grid md:grid-cols-3 gap-6">
            @php
                $pains = [
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'Nobody ever asked',
                        'body' => 'Referrals require a prompt. Most businesses do exceptional work and then… nothing. The happy client moves on and the moment passes.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
                        'title' => 'No way to track where leads come from',
                        'body' => 'A new lead calls in. You have no idea who sent them or what context they came in with. You can\'t thank the referrer or double down on what\'s working.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                        'title' => 'No way to reward the relationship',
                        'body' => 'You mean to send a thank-you but there\'s no trigger, no reminder, no process. Referrers who aren\'t acknowledged don\'t stay motivated to keep sending people your way.',
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
            <p class="text-yellow-400 text-xs font-bold uppercase tracking-[0.2em] mb-3">The referral system</p>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Make referrals a process, not an accident</h2>
            <p class="text-gray-400 mt-4 max-w-xl mx-auto">Referrals compound. Every client you retain well and ask properly becomes a source of future revenue.</p>
        </div>

        <div class="space-y-4">
            @php
                $steps = [
                    [
                        'num' => '01',
                        'label' => 'Close the Loop',
                        'title' => 'Log the referral source the moment a lead comes in',
                        'body' => 'When a new contact submits an intake form or gets created in the CRM, record who referred them. Every referral source is tracked, searchable, and attributed.',
                        'color' => 'yellow',
                    ],
                    [
                        'num' => '02',
                        'label' => 'Deliver & Retain',
                        'title' => 'Do great work and stay connected post-delivery',
                        'body' => 'Clients who have a portal, get responsive communication, and feel well-served are primed to refer. The experience itself is the referral engine.',
                        'color' => 'orange',
                    ],
                    [
                        'num' => '03',
                        'label' => 'Ask',
                        'title' => 'CRM-scheduled referral request at the right moment',
                        'body' => 'Set a follow-up task in the CRM at project close — "Ask for referral at 30 days." When the reminder fires, you have the full history to personalize your outreach.',
                        'color' => 'blue',
                    ],
                    [
                        'num' => '04',
                        'label' => 'Thank',
                        'title' => 'Acknowledge every referral explicitly and fast',
                        'body' => 'When the referred lead comes in, the CRM links them back to the source. Reach out the same day to thank the referrer. This is what keeps the flywheel spinning.',
                        'color' => 'emerald',
                    ],
                ];
                $colorMap = [
                    'yellow' => ['badge' => 'bg-yellow-600/20 text-yellow-300 border-yellow-600/30', 'num' => 'text-yellow-400'],
                    'orange' => ['badge' => 'bg-orange-600/20 text-orange-300 border-orange-600/30', 'num' => 'text-orange-400'],
                    'blue'   => ['badge' => 'bg-blue-600/20 text-blue-300 border-blue-600/30', 'num' => 'text-blue-400'],
                    'emerald'=> ['badge' => 'bg-emerald-600/20 text-emerald-300 border-emerald-600/30', 'num' => 'text-emerald-400'],
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
        <div class="grid sm:grid-cols-2 gap-5 max-w-2xl mx-auto">
            <a href="{{ route('product.crm') }}" class="gr-card-hover bg-indigo-600/8 border border-indigo-600/20 rounded-2xl p-6 group">
                <p class="text-indigo-400 text-xs font-bold uppercase tracking-widest mb-2">CRM</p>
                <p class="text-white font-bold mb-2">Referral source tracking</p>
                <p class="text-gray-400 text-sm leading-relaxed">Track where every lead came from, schedule follow-ups, and never miss a thank-you opportunity.</p>
                <span class="text-indigo-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore CRM <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
            <a href="{{ route('product.portal') }}" class="gr-card-hover bg-orange-600/8 border border-orange-600/20 rounded-2xl p-6 group">
                <p class="text-orange-400 text-xs font-bold uppercase tracking-widest mb-2">Client Portal</p>
                <p class="text-white font-bold mb-2">The experience that earns referrals</p>
                <p class="text-gray-400 text-sm leading-relaxed">Clients who feel taken care of refer people. The portal is the ongoing proof that you're worth recommending.</p>
                <span class="text-orange-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore Portal <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- THE FULL JOURNEY CALLOUT                                          --}}
{{-- ================================================================ --}}
<section class="bg-[#060e1a] py-16 px-6 border-t border-white/5">
    <div class="max-w-5xl mx-auto">
        <p class="text-center text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mb-8">The complete business journey — smbgen covers every stage</p>
        <div class="flex flex-wrap justify-center gap-2">
            @php
                $journey = [
                    ['label' => 'Lead', 'href' => route('solutions.more-leads'), 'color' => 'bg-blue-600/20 border-blue-600/30 text-blue-300'],
                    ['label' => 'Nurture', 'href' => route('solutions.more-leads'), 'color' => 'bg-violet-600/20 border-violet-600/30 text-violet-300'],
                    ['label' => 'Propose', 'href' => route('solutions.streamline-bookings'), 'color' => 'bg-indigo-600/20 border-indigo-600/30 text-indigo-300'],
                    ['label' => 'Close', 'href' => route('solutions.streamline-bookings'), 'color' => 'bg-cyan-600/20 border-cyan-600/30 text-cyan-300'],
                    ['label' => 'Pay', 'href' => route('solutions.get-paid-faster'), 'color' => 'bg-emerald-600/20 border-emerald-600/30 text-emerald-300'],
                    ['label' => 'Deliver', 'href' => route('solutions.retain-clients'), 'color' => 'bg-orange-600/20 border-orange-600/30 text-orange-300'],
                    ['label' => 'Retain', 'href' => route('solutions.retain-clients'), 'color' => 'bg-amber-600/20 border-amber-600/30 text-amber-300'],
                    ['label' => 'Refer', 'href' => route('solutions.grow-referrals'), 'color' => 'bg-yellow-600/20 border-yellow-600/30 text-yellow-300'],
                ];
            @endphp
            @foreach($journey as $i => $stage)
                <a href="{{ $stage['href'] }}" class="flex items-center gap-1.5">
                    <span class="px-4 py-2 rounded-xl border {{ $stage['color'] }} text-sm font-bold transition-opacity hover:opacity-80">
                        {{ $stage['label'] }}
                    </span>
                    @if($i < count($journey) - 1)
                        <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- CTA                                                               --}}
{{-- ================================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-4">
            Your next best client is already in your network.
        </h2>
        <p class="text-gray-400 text-lg mb-8">
            Book a call and we'll map out how smbgen would build your referral flywheel from the ground up.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=grow-referrals"
               class="px-8 py-4 rounded-xl bg-yellow-600 hover:bg-yellow-500 text-white font-bold text-sm transition-colors shadow-xl shadow-yellow-900/30">
                Book a demo &rarr;
            </a>
            <a href="{{ route('solutions') }}"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-semibold text-sm border border-white/10 transition-colors">
                View all smbgen-core tools
            </a>
        </div>
    </div>
</section>

@endsection
