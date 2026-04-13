@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
@endphp

@section('title', 'Streamline Bookings — Stop Playing Scheduling Tag | smbgen')
@section('description', 'Replace back-and-forth phone calls and texts with a self-serve booking experience connected to your Google Calendar. Automated confirmations, reminders, and no-show reduction built in.')

@push('head')
<style>
    .sb-hero-bg {
        background:
            radial-gradient(ellipse at 65% -10%, rgba(139,92,246,0.18) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(59,130,246,0.10) 0%, transparent 50%),
            #06101d;
    }
    .sb-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .sb-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(139,92,246,0.25), 0 8px 32px rgba(139,92,246,0.08);
        transform: translateY(-2px);
    }
    .sb-gradient-text {
        background: linear-gradient(135deg, #a78bfa, #60a5fa);
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
<section class="sb-hero-bg min-h-[85vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-pulse"></span>
                    Booking & Scheduling
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    You're losing an hour<br>
                    a day just<br>
                    <span class="sb-gradient-text">scheduling meetings.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    The back-and-forth to find a time is friction that costs you deals.
                    smbgen gives every client a self-serve booking page that lives in your calendar automatically.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=streamline-bookings"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold transition-colors shadow-xl shadow-violet-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $contactHref }}?topic=bookings"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Ask a specific question
                    </a>
                </div>

                <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">
                    @foreach(['Google Calendar sync', 'Auto-confirmations & reminders', 'Custom intake per booking type'] as $point)
                        <span class="flex items-center gap-1.5 text-violet-400 font-medium">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $point }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Right: booking flow visual --}}
            <div class="bg-white/4 border border-white/10 rounded-2xl p-6 space-y-3 shadow-2xl">
                <div class="flex items-center justify-between mb-2 pb-4 border-b border-white/8">
                    <p class="text-white font-bold text-sm">Book a Consultation</p>
                    <span class="px-2.5 py-1 rounded-lg bg-violet-600/20 border border-violet-600/30 text-violet-300 text-xs font-semibold">Live</span>
                </div>

                {{-- Day selector --}}
                <div class="grid grid-cols-5 gap-2">
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri'] as $i => $day)
                        <div class="{{ $i === 2 ? 'bg-violet-600 border-violet-500 text-white' : 'bg-white/5 border-white/10 text-gray-400' }} border rounded-xl p-2.5 text-center cursor-pointer">
                            <p class="text-[10px] font-semibold uppercase tracking-wide">{{ $day }}</p>
                            <p class="text-lg font-black mt-0.5">{{ 23 + $i }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Time slots --}}
                <div class="grid grid-cols-3 gap-2 pt-2">
                    @foreach(['9:00 AM', '10:00 AM', '11:00 AM', '1:00 PM', '2:00 PM', '3:00 PM'] as $i => $slot)
                        <div class="{{ $i === 1 ? 'bg-violet-600/20 border-violet-500/50 text-violet-300' : 'bg-white/5 border-white/10 text-gray-400' }} border rounded-lg px-3 py-2 text-center text-xs font-semibold cursor-pointer">
                            {{ $slot }}
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-white/8 space-y-2">
                    <div class="text-xs text-gray-500">10:00 AM · Wednesday, March 25 · 30 min</div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-green-500/20 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-green-400" viewBox="0 0 48 48" fill="none">
                                <rect x="4" y="4" width="40" height="40" rx="4" fill="currentColor" opacity="0.5"/>
                                <text x="24" y="30" font-size="18" font-weight="700" text-anchor="middle" fill="white">G</text>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-400">Syncs to Google Calendar instantly</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                        </div>
                        <span class="text-xs text-gray-400">Confirmation + reminder emails sent</span>
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
        <p class="text-center text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mb-12">The real cost of manual scheduling</p>
        <div class="grid md:grid-cols-3 gap-6">
            @php
                $pains = [
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'Time you don\'t have',
                        'body' => '3–5 messages to confirm a single appointment. Multiply that across your weekly schedule and you\'ve burned half a morning on logistics.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
                        'title' => 'No-shows with no warning',
                        'body' => 'When there\'s no confirmation email, no reminder, and no process — people forget. A 30-minute no-show wastes your prep time and prime calendar slots.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                        'title' => 'Zero intake before the call',
                        'body' => 'You start every discovery call cold. No context, no pre-qualification, no agenda. You spend the first 10 minutes figuring out if this is even the right conversation.',
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
            <p class="text-violet-400 text-xs font-bold uppercase tracking-[0.2em] mb-3">End-to-end booking flow</p>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">From "I'm interested" to confirmed on the calendar</h2>
            <p class="text-gray-400 mt-4 max-w-xl mx-auto">The whole flow — from first visit to confirmed appointment — runs itself.</p>
        </div>

        <div class="space-y-4">
            @php
                $steps = [
                    [
                        'num' => '01',
                        'label' => 'Book',
                        'title' => 'A self-serve booking page, always open',
                        'body' => 'Clients pick a service type, complete a brief intake form, and choose from your real-time availability. No phone call required, no back-and-forth.',
                        'color' => 'violet',
                    ],
                    [
                        'num' => '02',
                        'label' => 'Confirm',
                        'title' => 'Instant confirmation — for both of you',
                        'body' => 'The moment a booking is confirmed, both you and the client get professional confirmation emails. The appointment drops into your Google Calendar automatically.',
                        'color' => 'blue',
                    ],
                    [
                        'num' => '03',
                        'label' => 'Remind',
                        'title' => 'Reminder emails cut no-shows dramatically',
                        'body' => 'Automated reminders sent at the right times mean clients show up prepared. No manual follow-up, no awkward "just checking in" messages.',
                        'color' => 'emerald',
                    ],
                    [
                        'num' => '04',
                        'label' => 'Capture',
                        'title' => 'Every booking feeds your CRM',
                        'body' => 'Booking intake answers — service requested, budget, timeline, notes — are stored alongside the contact record. You walk into every call with context.',
                        'color' => 'orange',
                    ],
                ];
                $colorMap = [
                    'violet' => ['badge' => 'bg-violet-600/20 text-violet-300 border-violet-600/30', 'num' => 'text-violet-400'],
                    'blue'   => ['badge' => 'bg-blue-600/20 text-blue-300 border-blue-600/30', 'num' => 'text-blue-400'],
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
            <a href="{{ route('product.book') }}" class="sb-card-hover bg-violet-600/8 border border-violet-600/20 rounded-2xl p-6 group">
                <p class="text-violet-400 text-xs font-bold uppercase tracking-widest mb-2">Book</p>
                <p class="text-white font-bold mb-2">Online booking wizard</p>
                <p class="text-gray-400 text-sm leading-relaxed">Real-time availability, intake forms, Google Calendar sync, and automatic confirmation emails.</p>
                <span class="text-violet-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore Book <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
            <a href="{{ route('product.contact') }}" class="sb-card-hover bg-blue-600/8 border border-blue-600/20 rounded-2xl p-6 group">
                <p class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-2">Contact</p>
                <p class="text-white font-bold mb-2">Pre-booking intake</p>
                <p class="text-gray-400 text-sm leading-relaxed">Qualify leads before they even book with a structured contact form that feeds into the booking flow.</p>
                <span class="text-blue-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore Contact <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
            <a href="{{ route('product.crm') }}" class="sb-card-hover bg-indigo-600/8 border border-indigo-600/20 rounded-2xl p-6 group">
                <p class="text-indigo-400 text-xs font-bold uppercase tracking-widest mb-2">CRM</p>
                <p class="text-white font-bold mb-2">Every booking in context</p>
                <p class="text-gray-400 text-sm leading-relaxed">Booking data flows directly into contact records — you walk into every call already knowing the story.</p>
                <span class="text-indigo-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore CRM <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
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
            Your calendar should fill itself.
        </h2>
        <p class="text-gray-400 text-lg mb-8">
            Book a call and we'll show you how smbgen's booking system would work for your specific service and schedule.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=streamline-bookings"
               class="px-8 py-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm transition-colors shadow-xl shadow-violet-900/30">
                Book a demo &rarr;
            </a>
            <a href="{{ route('solutions.get-paid-faster') }}"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-semibold text-sm border border-white/10 transition-colors">
                Next: Get Paid Faster
            </a>
        </div>
    </div>
</section>

@endsection
