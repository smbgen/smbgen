@extends('layouts.frontend')

@section('title', 'smbgen-core — Contact, Book, Pay, Client Portal, CRM, CMS')
@section('description', 'Simple explanation pages for the smbgen-core product offering: Contact, Book, Pay, Client Portal, CRM, and CMS.')

@push('head')
<style>
    .solutions-bg {
        background:
            radial-gradient(ellipse at 20% 0%, rgba(59, 130, 246, 0.08) 0%, transparent 55%),
            radial-gradient(ellipse at 80% 100%, rgba(16, 185, 129, 0.06) 0%, transparent 55%),
            #03040d;
    }

    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-3px);
    }
</style>
@endpush

@section('content')

<section class="solutions-bg py-28 px-6">
    <div class="max-w-5xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-white/10 bg-white/5 text-gray-400 text-xs font-bold uppercase tracking-widest mb-10">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse inline-block"></span>
            smbgen-core product offering
        </div>

        <h1 class="text-6xl md:text-7xl font-black text-white leading-[1.05] tracking-tight mb-7">
            Clear product pages.<br>
            <span class="text-blue-400">No internal names.</span>
            Just the capabilities customers need.
        </h1>

        <p class="text-gray-400 text-xl max-w-3xl mx-auto mb-12 font-light leading-relaxed">
            smbgen-core is explained through the six things people immediately understand:
            Contact, Book, Pay, Client Portal, CRM, and CMS. Each page explains the job, the outcome, and the next step.
        </p>

        <div class="flex flex-wrap justify-center gap-3 mb-4">
            <a href="#contact-core" class="px-4 py-2 rounded-lg bg-blue-600/15 border border-blue-600/30 text-blue-400 text-sm font-black uppercase tracking-widest hover:bg-blue-600/25 transition-colors">Contact</a>
            <a href="#book-core" class="px-4 py-2 rounded-lg bg-violet-600/15 border border-violet-600/30 text-violet-400 text-sm font-black uppercase tracking-widest hover:bg-violet-600/25 transition-colors">Book</a>
            <a href="#pay-core" class="px-4 py-2 rounded-lg bg-emerald-600/15 border border-emerald-600/30 text-emerald-400 text-sm font-black uppercase tracking-widest hover:bg-emerald-600/25 transition-colors">Pay</a>
            <a href="#portal-core" class="px-4 py-2 rounded-lg bg-orange-600/15 border border-orange-600/30 text-orange-400 text-sm font-black uppercase tracking-widest hover:bg-orange-600/25 transition-colors">Client Portal</a>
            <a href="#crm-core" class="px-4 py-2 rounded-lg bg-indigo-600/15 border border-indigo-600/30 text-indigo-400 text-sm font-black uppercase tracking-widest hover:bg-indigo-600/25 transition-colors">CRM</a>
            <a href="#cms-core" class="px-4 py-2 rounded-lg bg-cyan-600/15 border border-cyan-600/30 text-cyan-400 text-sm font-black uppercase tracking-widest hover:bg-cyan-600/25 transition-colors">CMS</a>
        </div>
    </div>
</section>

@php
    $portalHref = auth()->check()
        ? (auth()->user()->isAdministrator() ? route('admin.dashboard') : route('dashboard'))
        : route('login');

    $corePages = [
        [
            'id' => 'contact-core',
            'number' => '01',
            'title' => 'Contact',
            'headline' => 'A superior contact form.',
            'body' => 'The contact page should do more than collect a name and email. smbgen-core gives you a structured intake experience that qualifies leads, routes the right requests, and makes the next step obvious.',
            'points' => [
                'Structured intake instead of a generic form',
                'Smarter qualification before the sales conversation',
                'Cleaner handoff into booking, CRM, and follow-up',
            ],
            'primaryHref' => route('contact'),
            'primaryLabel' => 'Open contact flow',
            'secondaryHref' => route('home.services'),
            'secondaryLabel' => 'See implementation services',
            'surface' => 'background: radial-gradient(ellipse at 70% 0%, rgba(37,99,235,0.18) 0%, transparent 60%), #06101d; border: 1px solid rgba(37,99,235,0.22);',
            'badgeClass' => 'bg-blue-600/20 border-blue-500/30 text-blue-300',
            'accentClass' => 'text-blue-400',
            'buttonClass' => 'bg-blue-700 hover:bg-blue-600 border-blue-600/40',
            'checkClass' => 'bg-blue-600/20 border-blue-600/40 text-blue-400',
            'visual' => 'contact',
        ],
        [
            'id' => 'book-core',
            'number' => '02',
            'title' => 'Book',
            'headline' => 'A booking flow people actually complete.',
            'body' => 'Booking should feel fast, clear, and trustworthy. smbgen-core handles availability, appointment selection, confirmations, and follow-through so customers move from interest to action without friction.',
            'points' => [
                'Availability and scheduling in one clear path',
                'Confirmations and reminders built in',
                'Booking tied directly to the client record',
            ],
            'primaryHref' => Route::has('booking.wizard') ? route('booking.wizard') : route('contact'),
            'primaryLabel' => Route::has('booking.wizard') ? 'Open booking flow' : 'Ask about booking',
            'secondaryHref' => route('home.services'),
            'secondaryLabel' => 'See booking services',
            'surface' => 'background: radial-gradient(ellipse at 30% 0%, rgba(139,92,246,0.18) 0%, transparent 60%), #0a0616; border: 1px solid rgba(139,92,246,0.2);',
            'badgeClass' => 'bg-violet-600/20 border-violet-500/30 text-violet-300',
            'accentClass' => 'text-violet-400',
            'buttonClass' => 'bg-violet-700 hover:bg-violet-600 border-violet-600/40',
            'checkClass' => 'bg-violet-600/20 border-violet-600/40 text-violet-400',
            'visual' => 'book',
        ],
        [
            'id' => 'pay-core',
            'number' => '03',
            'title' => 'Pay',
            'headline' => 'A payment experience that gets money in faster.',
            'body' => 'The payment step should remove hesitation, not create it. smbgen-core gives you a clearer payment path tied to the actual customer workflow, from invoice or request to confirmation.',
            'points' => [
                'Simple payment collection customers understand instantly',
                'Cleaner handoff from booking or approval into payment',
                'A better trust signal than scattered third-party links',
            ],
            'primaryHref' => Route::has('payment.collect') ? route('payment.collect') : route('contact'),
            'primaryLabel' => Route::has('payment.collect') ? 'Open payment flow' : 'Ask about payments',
            'secondaryHref' => route('home.services'),
            'secondaryLabel' => 'See payment services',
            'surface' => 'background: radial-gradient(ellipse at 70% 0%, rgba(16,185,129,0.15) 0%, transparent 60%), #04150f; border: 1px solid rgba(16,185,129,0.22);',
            'badgeClass' => 'bg-emerald-600/20 border-emerald-500/30 text-emerald-300',
            'accentClass' => 'text-emerald-400',
            'buttonClass' => 'bg-emerald-700 hover:bg-emerald-600 border-emerald-600/40',
            'checkClass' => 'bg-emerald-600/20 border-emerald-600/40 text-emerald-400',
            'visual' => 'pay',
        ],
        [
            'id' => 'portal-core',
            'number' => '04',
            'title' => 'Client Portal',
            'headline' => 'A better post-sale customer experience.',
            'body' => 'Clients need one place to go. smbgen-core gives them a real portal where they can log in, review files, check progress, handle billing, and stay aligned without extra email back-and-forth.',
            'points' => [
                'One login for updates, files, and next steps',
                'A cleaner client experience after the sale',
                'Less manual status communication for your team',
            ],
            'primaryHref' => $portalHref,
            'primaryLabel' => auth()->check() ? 'Open portal' : 'View portal access',
            'secondaryHref' => route('home.services'),
            'secondaryLabel' => 'See portal services',
            'surface' => 'background: radial-gradient(ellipse at 30% 100%, rgba(249,115,22,0.18) 0%, transparent 55%), #140900; border: 1px solid rgba(249,115,22,0.22);',
            'badgeClass' => 'bg-orange-600/20 border-orange-500/30 text-orange-300',
            'accentClass' => 'text-orange-400',
            'buttonClass' => 'bg-orange-700 hover:bg-orange-600 border-orange-600/40',
            'checkClass' => 'bg-orange-600/20 border-orange-600/40 text-orange-400',
            'visual' => 'portal',
        ],
        [
            'id' => 'crm-core',
            'number' => '05',
            'title' => 'CRM',
            'headline' => 'A CRM your team will actually use.',
            'body' => 'Contact records, opportunities, notes, follow-ups, and deal visibility all live in one operating layer. smbgen-core keeps the commercial side of the business visible and actionable.',
            'points' => [
                'Lead and contact history in one place',
                'Follow-up visibility across the whole team',
                'Connected to contact, booking, portal, and payment activity',
            ],
            'primaryHref' => route('contact'),
            'primaryLabel' => 'Talk to us about CRM',
            'secondaryHref' => route('home.services'),
            'secondaryLabel' => 'See CRM services',
            'surface' => 'background: radial-gradient(ellipse at 65% 0%, rgba(99,102,241,0.16) 0%, transparent 55%), #06081a; border: 1px solid rgba(99,102,241,0.2);',
            'badgeClass' => 'bg-indigo-600/20 border-indigo-500/30 text-indigo-300',
            'accentClass' => 'text-indigo-400',
            'buttonClass' => 'bg-indigo-700 hover:bg-indigo-600 border-indigo-600/40',
            'checkClass' => 'bg-indigo-600/20 border-indigo-600/40 text-indigo-400',
            'visual' => 'crm',
        ],
        [
            'id' => 'cms-core',
            'number' => '06',
            'title' => 'CMS',
            'headline' => 'A CMS that makes publishing easier, not harder.',
            'body' => 'Your team should be able to update pages, publish offers, manage media, and keep the site current without waiting on dev time for every change. smbgen-core makes content operations manageable.',
            'points' => [
                'Page editing without bottlenecking on developers',
                'Faster turnaround on updates, offers, and landing pages',
                'Content tied to the same core system as the rest of the business',
            ],
            'primaryHref' => route('contact'),
            'primaryLabel' => 'Talk to us about CMS',
            'secondaryHref' => route('home.services'),
            'secondaryLabel' => 'See CMS services',
            'surface' => 'background: radial-gradient(ellipse at 60% 0%, rgba(6,182,212,0.14) 0%, transparent 55%), #021118; border: 1px solid rgba(6,182,212,0.2);',
            'badgeClass' => 'bg-cyan-600/20 border-cyan-500/30 text-cyan-300',
            'accentClass' => 'text-cyan-400',
            'buttonClass' => 'bg-cyan-700 hover:bg-cyan-600 border-cyan-600/40',
            'checkClass' => 'bg-cyan-600/20 border-cyan-600/40 text-cyan-400',
            'visual' => 'cms',
        ],
    ];
@endphp

@foreach($corePages as $page)
    <section id="{{ $page['id'] }}" class="px-6 {{ $loop->first ? 'py-1' : 'pt-5' }} {{ $loop->last ? 'pb-24' : '' }}">
        <div class="max-w-6xl mx-auto">
            <div class="product-card rounded-3xl overflow-hidden" style="{{ $page['surface'] }}">
                <div class="grid md:grid-cols-2 gap-0">
                    @if($loop->iteration % 2 === 0)
                        <div class="p-10 md:p-14 flex items-center order-2 md:order-1">
                            <div class="w-full rounded-2xl p-6" style="background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.06);">
                                @if($page['visual'] === 'book')
                                    <div class="text-violet-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Booking flow preview</div>
                                    <div class="grid grid-cols-3 gap-2 mb-4">
                                        @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                            <div class="rounded-xl border border-violet-500/20 bg-violet-500/10 p-3 text-center text-xs text-violet-200">{{ $day }}</div>
                                        @endforeach
                                    </div>
                                    <div class="space-y-2">
                                        @foreach(['9:00 AM', '11:30 AM', '2:00 PM'] as $slot)
                                            <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 text-xs text-gray-300"><span>{{ $slot }}</span><span class="text-violet-400">Available</span></div>
                                        @endforeach
                                    </div>
                                @elseif($page['visual'] === 'portal')
                                    <div class="text-orange-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Portal preview</div>
                                    <div class="space-y-3">
                                        @foreach(['Project status', 'Files and approvals', 'Messages', 'Billing'] as $item)
                                            <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 text-sm text-gray-200"><span>{{ $item }}</span><span class="text-orange-400">Open</span></div>
                                        @endforeach
                                    </div>
                                @elseif($page['visual'] === 'cms')
                                    <div class="text-cyan-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">CMS preview</div>
                                    <div class="space-y-3">
                                        <div class="h-10 rounded-xl bg-white/5"></div>
                                        <div class="h-24 rounded-xl bg-white/5"></div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <div class="h-14 rounded-xl bg-white/5"></div>
                                            <div class="h-14 rounded-xl bg-white/5"></div>
                                            <div class="h-14 rounded-xl bg-white/5"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="p-10 md:p-14 flex flex-col justify-center {{ $loop->iteration % 2 === 0 ? 'order-1 md:order-2' : '' }}">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl border flex items-center justify-center shadow-lg {{ $page['badgeClass'] }}">
                                <span class="text-xs font-black">{{ $page['number'] }}</span>
                            </div>
                            <div>
                                <span class="{{ $page['accentClass'] }} text-[10px] font-bold uppercase tracking-[0.25em]">smbgen-core</span>
                                <div class="text-white font-black text-2xl uppercase tracking-widest">{{ $page['title'] }}</div>
                            </div>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-black text-white leading-tight tracking-tight mb-4">
                            {{ $page['headline'] }}
                        </h2>
                        <p class="text-gray-400 text-base leading-relaxed mb-8">{{ $page['body'] }}</p>

                        <div class="flex flex-col gap-2 mb-9">
                            @foreach($page['points'] as $point)
                                <div class="flex items-center gap-2.5 text-gray-300 text-sm">
                                    <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] shrink-0 {{ $page['checkClass'] }}">&#10003;</span>
                                    {{ $point }}
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="{{ $page['primaryHref'] }}" class="px-6 py-3 rounded-xl text-white font-black uppercase tracking-wider text-sm transition-colors border {{ $page['buttonClass'] }}">
                                {{ $page['primaryLabel'] }} &rarr;
                            </a>
                            <a href="{{ $page['secondaryHref'] }}" class="{{ $page['accentClass'] }} text-sm font-semibold hover:text-white transition-colors">{{ $page['secondaryLabel'] }}</a>
                        </div>
                    </div>

                    @if($loop->iteration % 2 === 1)
                        <div class="p-10 md:p-14 flex items-center">
                            <div class="w-full rounded-2xl p-6" style="background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.06);">
                                @if($page['visual'] === 'contact')
                                    <div class="text-blue-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Lead intake preview</div>
                                    <div class="space-y-3">
                                        <div class="h-10 rounded-xl bg-white/5"></div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="h-10 rounded-xl bg-white/5"></div>
                                            <div class="h-10 rounded-xl bg-white/5"></div>
                                        </div>
                                        <div class="h-24 rounded-xl bg-white/5"></div>
                                        <div class="flex items-center justify-between rounded-xl border border-blue-500/20 bg-blue-500/10 px-4 py-3 text-xs text-blue-300">
                                            <span>Qualified lead routing active</span>
                                            <span>Ready</span>
                                        </div>
                                    </div>
                                @elseif($page['visual'] === 'pay')
                                    <div class="text-emerald-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Payment preview</div>
                                    <div class="rounded-2xl border border-emerald-500/15 bg-emerald-500/10 p-5">
                                        <div class="mb-2 flex items-center justify-between text-sm font-semibold text-white"><span>Invoice #1048</span><span>$1,250.00</span></div>
                                        <div class="mb-4 text-xs text-emerald-200">Deposit for onboarding and setup</div>
                                        <div class="space-y-2">
                                            <div class="h-10 rounded-xl bg-white/5"></div>
                                            <div class="h-10 rounded-xl bg-white/5"></div>
                                        </div>
                                        <div class="mt-4 rounded-xl bg-emerald-600 px-4 py-3 text-center text-sm font-bold text-white">Pay now</div>
                                    </div>
                                @elseif($page['visual'] === 'crm')
                                    <div class="text-indigo-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">CRM preview</div>
                                    <div class="space-y-3">
                                        @foreach([
                                            ['New lead', 'High intent'],
                                            ['Proposal sent', 'Follow up today'],
                                            ['Client onboarded', 'Portal active'],
                                        ] as [$rowTitle, $rowStatus])
                                            <div class="flex items-center justify-between rounded-xl border border-indigo-500/10 bg-indigo-500/10 px-4 py-3 text-xs text-gray-200">
                                                <span>{{ $rowTitle }}</span>
                                                <span class="font-bold text-indigo-300">{{ $rowStatus }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endforeach

<section class="px-6 pb-28" style="background: #03040d;">
    <div class="max-w-4xl mx-auto text-center">
        <div class="rounded-3xl p-14" style="background: radial-gradient(ellipse at 50% 0%, rgba(59,130,246,0.12) 0%, transparent 70%), rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.07);">
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-5 leading-tight">
                Start with the page customers need most.<br>Expand from there.
            </h2>
            <p class="text-gray-500 text-lg mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                smbgen-core is strongest when all six capabilities work together, but you can start with the biggest bottleneck first and grow the system over time.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('contact') }}" class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-base transition-colors shadow-lg shadow-blue-900/30">
                    Talk to us &rarr;
                </a>
                <a href="{{ route('home') }}#start-here" class="px-8 py-4 rounded-xl text-gray-400 font-bold text-base transition-colors hover:text-white" style="border: 1px solid rgba(255,255,255,0.1);">
                    Back to overview
                </a>
            </div>
        </div>
    </div>
</section>

@endsection