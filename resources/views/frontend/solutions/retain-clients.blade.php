@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
@endphp

@section('title', 'Retain Clients After Delivery — smbgen Client Retention Solutions')
@section('description', 'The project is done, the invoice is paid — and then you never hear from them again. smbgen gives you the portal, the communication channel, and the CRM follow-up system to turn one-time clients into long-term relationships.')

@push('head')
<style>
    .rc-hero-bg {
        background:
            radial-gradient(ellipse at 65% -10%, rgba(249,115,22,0.16) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(139,92,246,0.10) 0%, transparent 50%),
            #06101d;
    }
    .rc-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .rc-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(249,115,22,0.25), 0 8px 32px rgba(249,115,22,0.08);
        transform: translateY(-2px);
    }
    .rc-gradient-text {
        background: linear-gradient(135deg, #fb923c, #a78bfa);
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
<section class="rc-hero-bg min-h-[85vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    Client Retention
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    You delivered great work.<br>
                    Then they<br>
                    <span class="rc-gradient-text">disappeared.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    A great first project isn't enough if there's no system to stay in the relationship.
                    smbgen gives every client a private portal, a communication channel, and keeps you top-of-mind long after delivery.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=retain-clients"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold transition-colors shadow-xl shadow-orange-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $contactHref }}?topic=retention"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Ask a specific question
                    </a>
                </div>

                <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">
                    @foreach(['Private client portal login', 'Document & file sharing', 'CRM follow-up tracking'] as $point)
                        <span class="flex items-center gap-1.5 text-orange-400 font-medium">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $point }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Right: client portal preview --}}
            <div class="bg-white/4 border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
                {{-- Portal header --}}
                <div class="bg-white/4 border-b border-white/8 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-orange-500/30 flex items-center justify-center text-orange-300 text-xs font-black">JD</div>
                        <div>
                            <p class="text-white text-sm font-bold">Jane Doe</p>
                            <p class="text-gray-500 text-xs">Client since Jan 2026</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-green-500/20 text-green-300 text-[10px] font-bold uppercase tracking-wide">Active</span>
                </div>
                {{-- Portal content --}}
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide mb-3">Recent Documents</p>
                        <div class="space-y-2">
                            @foreach([
                                ['name' => 'Q1 Strategy Report.pdf', 'date' => 'Mar 20', 'icon' => 'pdf'],
                                ['name' => 'Brand Assets v2.zip', 'date' => 'Mar 15', 'icon' => 'zip'],
                                ['name' => 'Project Proposal.pdf', 'date' => 'Feb 28', 'icon' => 'pdf'],
                            ] as $file)
                                <div class="flex items-center gap-3 bg-white/4 rounded-xl px-4 py-3">
                                    <div class="w-7 h-7 rounded-lg bg-orange-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <span class="text-gray-300 text-xs flex-1 truncate">{{ $file['name'] }}</span>
                                    <span class="text-gray-600 text-xs shrink-0">{{ $file['date'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="pt-2 border-t border-white/8">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide mb-3">Messages</p>
                        <div class="bg-white/4 rounded-xl px-4 py-3 flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-blue-500/30 flex items-center justify-center text-blue-300 text-[10px] font-black shrink-0 mt-0.5">Y</div>
                            <div>
                                <p class="text-gray-300 text-xs leading-relaxed">"Quick question about the Q2 project scope — when's a good time to chat?"</p>
                                <p class="text-gray-600 text-xs mt-1">2 hours ago</p>
                            </div>
                        </div>
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
        <p class="text-center text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mb-12">Why clients don't come back (even when they loved you)</p>
        <div class="grid md:grid-cols-3 gap-6">
            @php
                $pains = [
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
                        'title' => 'No reason to stay in contact',
                        'body' => 'Once the project ends, the relationship goes cold. There\'s no structured reason for them to think of you when the next need comes up.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
                        'title' => 'Deliverables scattered across email',
                        'body' => 'Reports, files, contracts, receipts — all buried in a long email thread. Clients can\'t find what they need without digging, which creates friction.',
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                        'title' => 'No follow-up system',
                        'body' => 'You know you should check in at 30, 60, 90 days — but without a system it doesn\'t happen. By the time you remember, they\'ve hired someone else.',
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
            <p class="text-orange-400 text-xs font-bold uppercase tracking-[0.2em] mb-3">The retention system</p>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Turn every delivery into a long-term relationship</h2>
            <p class="text-gray-400 mt-4 max-w-xl mx-auto">Keeping a client is five times cheaper than finding a new one. Build the system that makes staying easy.</p>
        </div>

        <div class="space-y-4">
            @php
                $steps = [
                    [
                        'num' => '01',
                        'label' => 'Deliver',
                        'title' => 'Share deliverables through the portal — not email',
                        'body' => 'Upload reports, designs, contracts, and any document directly to the client\'s private portal. They get a notification, log in, and find everything in one organized place.',
                        'color' => 'orange',
                    ],
                    [
                        'num' => '02',
                        'label' => 'Communicate',
                        'title' => 'A direct message channel, not a buried inbox',
                        'body' => 'The portal includes an integrated messaging system. Questions and updates live in the client\'s account — not scattered across five different email threads.',
                        'color' => 'violet',
                    ],
                    [
                        'num' => '03',
                        'label' => 'Follow up',
                        'title' => 'CRM-scheduled check-ins keep you present',
                        'body' => 'Set a follow-up date in the CRM the day you close a project. Get a reminder at 30 days. Reach out with context — not a cold "just checking in" message.',
                        'color' => 'blue',
                    ],
                    [
                        'num' => '04',
                        'label' => 'Grow',
                        'title' => 'Satisfied clients become repeat buyers and referrers',
                        'body' => 'A client with portal access, responsive communication, and timely follow-up doesn\'t shop around. They come back. And they send people they know.',
                        'color' => 'emerald',
                    ],
                ];
                $colorMap = [
                    'orange' => ['badge' => 'bg-orange-600/20 text-orange-300 border-orange-600/30', 'num' => 'text-orange-400'],
                    'violet' => ['badge' => 'bg-violet-600/20 text-violet-300 border-violet-600/30', 'num' => 'text-violet-400'],
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
        <div class="grid sm:grid-cols-3 gap-5">
            <a href="{{ route('product.portal') }}" class="rc-card-hover bg-orange-600/8 border border-orange-600/20 rounded-2xl p-6 group">
                <p class="text-orange-400 text-xs font-bold uppercase tracking-widest mb-2">Client Portal</p>
                <p class="text-white font-bold mb-2">Private access for every client</p>
                <p class="text-gray-400 text-sm leading-relaxed">Files, messages, invoices, and history — all in one private, branded client area.</p>
                <span class="text-orange-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore Portal <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
            <a href="{{ route('product.crm') }}" class="rc-card-hover bg-indigo-600/8 border border-indigo-600/20 rounded-2xl p-6 group">
                <p class="text-indigo-400 text-xs font-bold uppercase tracking-widest mb-2">CRM</p>
                <p class="text-white font-bold mb-2">Follow-up scheduling & history</p>
                <p class="text-gray-400 text-sm leading-relaxed">Set next-action dates, keep notes, and never let a good relationship go cold by accident.</p>
                <span class="text-indigo-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore CRM <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
            <a href="{{ route('product.pay') }}" class="rc-card-hover bg-emerald-600/8 border border-emerald-600/20 rounded-2xl p-6 group">
                <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-2">Pay</p>
                <p class="text-white font-bold mb-2">Invoicing inside the portal</p>
                <p class="text-gray-400 text-sm leading-relaxed">Clients pay invoices directly from their portal — no separate login, no friction.</p>
                <span class="text-emerald-400 text-xs font-semibold mt-4 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Explore Pay <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
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
            Every client should feel like a priority after delivery.
        </h2>
        <p class="text-gray-400 text-lg mb-8">
            Book a call and we'll show you how smbgen's client portal and CRM would work for your business.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=retain-clients"
               class="px-8 py-4 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm transition-colors shadow-xl shadow-orange-900/30">
                Book a demo &rarr;
            </a>
            <a href="{{ route('solutions.grow-referrals') }}"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-semibold text-sm border border-white/10 transition-colors">
                Next: Grow Through Referrals
            </a>
        </div>
    </div>
</section>

@endsection
