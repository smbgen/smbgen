@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
@endphp

@section('title', 'Get Paid Faster — Eliminate Payment Friction | smbgen')
@section('description', 'Stop chasing invoices. smbgen gives your clients a clean, simple way to pay online — and gives you a real-time view of what\'s been collected and what\'s outstanding.')

@push('head')
<style>
    .gpf-hero-bg {
        background:
            radial-gradient(ellipse at 65% -10%, rgba(16,185,129,0.18) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(245,158,11,0.10) 0%, transparent 50%),
            #06101d;
    }
    .gpf-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .gpf-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(16,185,129,0.25), 0 8px 32px rgba(16,185,129,0.08);
        transform: translateY(-2px);
    }
    .gpf-gradient-text {
        background: linear-gradient(135deg, #34d399, #fbbf24);
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
<section class="gpf-hero-bg min-h-[85vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Payments & Invoicing
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    You did the work.<br>
                    Now you're waiting<br>
                    <span class="gpf-gradient-text">to get paid.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    Chasing invoices is unprofessional and exhausting.
                    smbgen gives clients a frictionless online payment experience
                    so money moves on delivery — not 30 days later.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=get-paid-faster"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition-colors shadow-xl shadow-emerald-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $contactHref }}?topic=payments"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Ask a specific question
                    </a>
                </div>

                <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">
                    @foreach(['Stripe-powered payments', 'Pay links via email', 'Real-time payment tracking'] as $point)
                        <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $point }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Right: payment flow visual --}}
            <div class="space-y-3">

                <div class="bg-white/4 border border-white/10 rounded-2xl p-5 shadow-xl">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-white font-bold text-sm">Invoice #1047</p>
                            <p class="text-gray-400 text-xs mt-0.5">Web Design Project — Final Deliverable</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-bold">Outstanding</span>
                    </div>
                    <div class="text-3xl font-black text-white mb-4">$3,200.00</div>
                    <div class="text-gray-500 text-xs mb-4">Due March 28, 2026</div>
                    <button class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm transition-colors">
                        Pay Now with Card &rarr;
                    </button>
                </div>

                <div class="bg-emerald-950/40 border border-emerald-800/30 rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-emerald-300 font-bold text-sm">Payment received — $3,200.00</p>
                        <p class="text-emerald-600 text-xs">Invoice #1047 settled · 2 minutes ago</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white/4 border border-white/8 rounded-xl p-4">
                        <p class="text-gray-500 text-xs mb-1">This Month</p>
                        <p class="text-white font-black text-xl">$14,800</p>
                        <p class="text-emerald-400 text-xs font-medium mt-0.5">collected</p>
                    </div>
                    <div class="bg-white/4 border border-white/8 rounded-xl p-4">
                        <p class="text-gray-500 text-xs mb-1">Outstanding</p>
                        <p class="text-white font-black text-xl">$3,200</p>
                        <p class="text-amber-400 text-xs font-medium mt-0.5">1 invoice pending</p>
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
        <p class="text-center text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mb-12">Why payment is still broken for most small businesses</p>
        <div class="grid md:grid-cols-3 gap-6">
            @php
                $pains = [
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                        'title' => 'Invoice sent. No response.',
                        'body' => 'You send a PDF, they say they\'ll "get to it." A week later you send a follow-up. Another week passes. The cash flow gap grows with every delay.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 3h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z"/>',
                        'title' => 'No easy way for clients to pay',
                        'body' => 'Checks. Bank transfers. Venmo. Zelle. Every client needs a different method. There\'s no clean, professional experience that just works for everyone.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'title' => 'No visibility into what\'s outstanding',
                        'body' => 'You don\'t know what\'s been paid, what\'s late, or who to follow up with — until you manually go through your email or bank statement.',
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
            <p class="text-emerald-400 text-xs font-bold uppercase tracking-[0.2em] mb-3">The payment flow</p>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Work complete. Invoice sent. Money collected.</h2>
            <p class="text-gray-400 mt-4 max-w-xl mx-auto">From project completion to payment in hand — no chasing required.</p>
        </div>

        <div class="space-y-4">
            @php
                $steps = [
                    [
                        'num' => '01',
                        'label' => 'Invoice',
                        'title' => 'Create an invoice in seconds',
                        'body' => 'Add line items, set the amount, attach a due date. No accounting software required — just a clean, professional invoice that goes out immediately.',
                        'color' => 'emerald',
                    ],
                    [
                        'num' => '02',
                        'label' => 'Send',
                        'title' => 'A pay link lands in their inbox',
                        'body' => 'The client receives a branded email with a single "Pay Now" button. One click, enter a card, done. No account creation, no friction, no excuses.',
                        'color' => 'blue',
                    ],
                    [
                        'num' => '03',
                        'label' => 'Collect',
                        'title' => 'Payment processes via Stripe',
                        'body' => 'Stripe handles the card processing — PCI-compliant, secure, and instant. Funds are in your account on Stripe\'s standard payout schedule.',
                        'color' => 'violet',
                    ],
                    [
                        'num' => '04',
                        'label' => 'Track',
                        'title' => 'Real-time dashboard of every dollar',
                        'body' => 'See what\'s collected, what\'s outstanding, and what\'s overdue at a glance. No spreadsheet reconciliation, no bank statement archaeology.',
                        'color' => 'orange',
                    ],
                ];
                $colorMap = [
                    'emerald'=> ['badge' => 'bg-emerald-600/20 text-emerald-300 border-emerald-600/30', 'num' => 'text-emerald-400'],
                    'blue'   => ['badge' => 'bg-blue-600/20 text-blue-300 border-blue-600/30', 'num' => 'text-blue-400'],
                    'violet' => ['badge' => 'bg-violet-600/20 text-violet-300 border-violet-600/30', 'num' => 'text-violet-400'],
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
        <div class="grid sm:grid-cols-2 gap-5 max-w-2xl mx-auto">
            <a href="{{ route('product.pay') }}" class="gpf-card-hover bg-emerald-600/8 border border-emerald-600/20 rounded-2xl p-6 group">
                <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-2">Pay</p>
                <p class="text-white font-bold mb-2">Online payment collection</p>
                <p class="text-gray-400 text-sm leading-relaxed">Stripe-powered pay links, invoice management, and real-time payment tracking in one place.</p>
                <span class="text-emerald-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore Pay <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
            <a href="{{ route('product.portal') }}" class="gpf-card-hover bg-orange-600/8 border border-orange-600/20 rounded-2xl p-6 group">
                <p class="text-orange-400 text-xs font-bold uppercase tracking-widest mb-2">Client Portal</p>
                <p class="text-white font-bold mb-2">Invoices & history in one place</p>
                <p class="text-gray-400 text-sm leading-relaxed">Clients can see all their invoices, payment history, and documents from one private portal login.</p>
                <span class="text-orange-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore Portal <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
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
            Stop counting days until payment clears.
        </h2>
        <p class="text-gray-400 text-lg mb-8">
            Book a call and we'll walk through how smbgen's payment flow would work for your business model and pricing structure.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=get-paid-faster"
               class="px-8 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm transition-colors shadow-xl shadow-emerald-900/30">
                Book a demo &rarr;
            </a>
            <a href="{{ route('solutions.retain-clients') }}"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-semibold text-sm border border-white/10 transition-colors">
                Next: Retain Clients
            </a>
        </div>
    </div>
</section>

@endsection
