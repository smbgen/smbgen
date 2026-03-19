@extends('layouts.frontend')

@section('title', 'Solutions — smbgen Product Suite')
@section('description', 'Six purpose-built products. One platform. AI-native. Built for small and mid-market businesses that refuse to stay small.')

@push('head')
<style>
    .solutions-bg {
        background:
            radial-gradient(ellipse at 20% 0%,   rgba(139, 92, 246, 0.07) 0%, transparent 55%),
            radial-gradient(ellipse at 80% 100%, rgba(220, 38,  38,  0.06) 0%, transparent 55%),
            #03040d;
    }
    .product-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .product-card:hover { transform: translateY(-3px); }
</style>
@endpush

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────────── --}}
<section class="solutions-bg py-28 px-6">
    <div class="max-w-5xl mx-auto text-center">

        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-white/10 bg-white/5 text-gray-400 text-xs font-bold uppercase tracking-widest mb-10">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse inline-block"></span>
            smbgen Product Suite
        </div>

        <h1 class="text-6xl md:text-7xl font-black text-white leading-[1.05] tracking-tight mb-7">
            Six products.<br>
            <span style="background: linear-gradient(135deg, #f87171 0%, #c084fc 35%, #22d3ee 60%, #fb923c 80%, #34d399 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                One platform.
            </span>
        </h1>

        <p class="text-gray-400 text-xl max-w-2xl mx-auto mb-12 font-light leading-relaxed">
            Purpose-built products for every layer of your business —
            each one AI-native, deeply integrated, and engineered to move the needle.
        </p>

        {{-- Product name strip --}}
        <div class="flex flex-wrap justify-center gap-3 mb-16">
            <a href="#extreme" class="px-4 py-2 rounded-lg bg-red-600/15 border border-red-600/30 text-red-400 text-sm font-black uppercase tracking-widest hover:bg-red-600/25 transition-colors">EXTREME</a>
            <a href="#signal"  class="px-4 py-2 rounded-lg bg-violet-600/15 border border-violet-600/30 text-violet-400 text-sm font-black uppercase tracking-widest hover:bg-violet-600/25 transition-colors">SIGNAL</a>
            <a href="#relay"   class="px-4 py-2 rounded-lg bg-cyan-600/15 border border-cyan-600/30 text-cyan-400 text-sm font-black uppercase tracking-widest hover:bg-cyan-600/25 transition-colors">RELAY</a>
            <a href="#surge"   class="px-4 py-2 rounded-lg bg-orange-600/15 border border-orange-600/30 text-orange-400 text-sm font-black uppercase tracking-widest hover:bg-orange-600/25 transition-colors">SURGE</a>
            <a href="#cast"    class="px-4 py-2 rounded-lg bg-emerald-600/15 border border-emerald-600/30 text-emerald-400 text-sm font-black uppercase tracking-widest hover:bg-emerald-600/25 transition-colors">CAST</a>
            <a href="#vault"   class="px-4 py-2 rounded-lg bg-indigo-600/15 border border-indigo-600/30 text-indigo-400 text-sm font-black uppercase tracking-widest hover:bg-indigo-600/25 transition-colors">VAULT</a>
        </div>

    </div>
</section>

{{-- ── EXTREME ───────────────────────────────────────────────────────── --}}
<section id="extreme" class="px-6 py-1">
    <div class="max-w-6xl mx-auto">
        <div class="product-card rounded-3xl overflow-hidden"
             style="background: radial-gradient(ellipse at 70% 0%, rgba(220,38,38,0.18) 0%, transparent 60%), #060d1a; border: 1px solid rgba(220,38,38,0.2);">
            <div class="grid md:grid-cols-2 gap-0">

                {{-- Content --}}
                <div class="p-10 md:p-14 flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-red-900 border border-red-500/40 flex items-center justify-center shadow-lg shadow-red-900/50">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-red-500 text-[10px] font-bold uppercase tracking-[0.25em]">smbgen &mdash; 01</span>
                            <div class="text-white font-black text-2xl uppercase tracking-widest" style="text-shadow: 0 0 20px rgba(220,38,38,0.4);">EXTREME</div>
                        </div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-white leading-tight tracking-tight mb-4">
                        Describe your app.<br>
                        <span style="background: linear-gradient(135deg, #f87171, #fbbf24); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Ship production code.</span>
                    </h2>
                    <p class="text-gray-400 text-base leading-relaxed mb-8">
                        AI-powered full-stack application generation.
                        Plain-English prompt goes in — production-ready Laravel codebase comes out.
                        Auth, database, reactive UI, tests. Everything wired. Ready to ship.
                    </p>
                    <div class="flex flex-col gap-2 mb-9">
                        @foreach(['Full-stack code generation from plain English', 'Auth, database, UI components — all scaffolded', 'Automated tests included. Deployable immediately.'] as $point)
                            <div class="flex items-center gap-2.5 text-gray-300 text-sm">
                                <span class="w-4 h-4 rounded bg-red-600/20 border border-red-600/40 flex items-center justify-center text-red-400 text-[10px] shrink-0">&#10003;</span>
                                {{ $point }}
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('extreme') }}" class="px-6 py-3 rounded-xl bg-red-700 hover:bg-red-600 text-white font-black uppercase tracking-wider text-sm transition-colors border border-red-600/40 shadow-lg shadow-red-900/30">
                            Launch EXTREME &rarr;
                        </a>
                        <a href="{{ route('extreme.demo') }}" class="text-red-400 text-sm font-semibold hover:text-red-300 transition-colors">Try the demo</a>
                    </div>
                </div>

                {{-- Code panel --}}
                <div class="p-10 md:p-14 flex items-center">
                    <div class="w-full rounded-2xl p-6 text-xs font-mono leading-relaxed" style="background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.06);">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500/60"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/60"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500/60"></span>
                            <span class="text-gray-600 ml-2 text-[10px] tracking-widest">extreme — generate</span>
                        </div>
                        <p class="text-gray-600 mb-2">$ extreme generate</p>
                        <p class="text-red-300 mb-1"><span class="text-gray-600">›</span> Build a multi-tenant SaaS for personal trainers…</p>
                        <div class="border-t mt-4 pt-4 space-y-1" style="border-color: rgba(255,255,255,0.05)">
                            <p class="text-green-400">✓ Scaffolding 12 models…</p>
                            <p class="text-green-400">✓ Generating migrations…</p>
                            <p class="text-green-400">✓ Wiring auth &amp; billing…</p>
                            <p class="text-green-400">✓ Writing 38 tests…</p>
                            <p class="text-emerald-300 font-semibold mt-2">→ Ready to deploy.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ── SIGNAL ────────────────────────────────────────────────────────── --}}
<section id="signal" class="px-6 pt-5">
    <div class="max-w-6xl mx-auto">
        <div class="product-card rounded-3xl overflow-hidden"
             style="background: radial-gradient(ellipse at 30% 0%, rgba(139,92,246,0.18) 0%, transparent 60%), #0a0616; border: 1px solid rgba(139,92,246,0.2);">
            <div class="grid md:grid-cols-2 gap-0">

                {{-- Visual panel --}}
                <div class="p-10 md:p-14 flex items-center order-2 md:order-1">
                    <div class="w-full space-y-3">
                        @foreach([
                            ['LinkedIn', 'New case study live — how we helped a logistics firm cut costs by 40% using AI workflows.', '2m ago', '847 impressions'],
                            ['Instagram', 'Behind the scenes: our team building something big 🔥 #smbgen #buildinpublic', '1h ago', '1.2k reach'],
                            ['X / Twitter', 'Thread dropping tomorrow. Follow to catch it first.', 'Scheduled 9 AM', '&mdash;'],
                        ] as [$platform, $copy, $time, $stat])
                            <div class="rounded-xl p-4" style="background: rgba(139,92,246,0.08); border: 1px solid rgba(139,92,246,0.18);">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-violet-400 text-xs font-black uppercase tracking-widest">{{ $platform }}</span>
                                    <span class="text-gray-600 text-xs">{{ $time }}</span>
                                </div>
                                <p class="text-gray-300 text-xs leading-relaxed mb-1">{{ $copy }}</p>
                                @if($stat !== '&mdash;')
                                    <p class="text-violet-600 text-[10px]">&#9650; {!! $stat !!}</p>
                                @else
                                    <p class="text-gray-700 text-[10px]">Pending</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-10 md:p-14 flex flex-col justify-center order-1 md:order-2">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-violet-900 border border-violet-500/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-violet-500 text-[10px] font-bold uppercase tracking-[0.25em]">smbgen &mdash; 02</span>
                            <div class="text-white font-black text-2xl uppercase tracking-widest">SIGNAL</div>
                        </div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-white leading-tight tracking-tight mb-4">
                        Your brand.<br>
                        <span class="text-violet-400">Everywhere. Always on.</span>
                    </h2>
                    <p class="text-gray-400 text-base leading-relaxed mb-8">
                        AI-generated social content, scheduled across every platform, with engagement analytics feeding back into your lead pipeline.
                        Show up consistently without consuming your day.
                    </p>
                    <div class="flex flex-col gap-2 mb-9">
                        @foreach(['AI content generation tuned to your brand voice', 'Multi-platform scheduling: LinkedIn, Instagram, X, Facebook', 'Engagement analytics feeding your CRM'] as $point)
                            <div class="flex items-center gap-2.5 text-gray-300 text-sm">
                                <span class="w-4 h-4 rounded bg-violet-600/20 border border-violet-600/40 flex items-center justify-center text-violet-400 text-[10px] shrink-0">&#10003;</span>
                                {{ $point }}
                            </div>
                        @endforeach
                    </div>
                    <a href="/contact" class="self-start px-6 py-3 rounded-xl bg-violet-700 hover:bg-violet-600 text-white font-black uppercase tracking-wider text-sm transition-colors border border-violet-600/40">
                        Explore SIGNAL &rarr;
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ── RELAY ─────────────────────────────────────────────────────────── --}}
<section id="relay" class="px-6 pt-5">
    <div class="max-w-6xl mx-auto">
        <div class="product-card rounded-3xl overflow-hidden"
             style="background: radial-gradient(ellipse at 70% 0%, rgba(6,182,212,0.15) 0%, transparent 60%), #020e12; border: 1px solid rgba(6,182,212,0.2);">
            <div class="grid md:grid-cols-2 gap-0">

                {{-- Content --}}
                <div class="p-10 md:p-14 flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-800 border border-cyan-500/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-cyan-500 text-[10px] font-bold uppercase tracking-[0.25em]">smbgen &mdash; 03</span>
                            <div class="text-white font-black text-2xl uppercase tracking-widest">RELAY</div>
                        </div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-white leading-tight tracking-tight mb-4">
                        Sequences that<br>
                        <span class="text-cyan-400">close deals.</span>
                    </h2>
                    <p class="text-gray-400 text-base leading-relaxed mb-8">
                        Email marketing and automation wired directly into your pipeline.
                        AI-written copy, deliverability monitoring, broadcast campaigns, and drip sequences
                        — built to warm leads and convert them on autopilot.
                    </p>
                    <div class="flex flex-col gap-2 mb-9">
                        @foreach(['AI-written email copy tuned to conversion', 'Drip sequences, broadcasts & trigger campaigns', 'Deliverability monitoring & open/click analytics', 'Native CRM sync — no Zapier required'] as $point)
                            <div class="flex items-center gap-2.5 text-gray-300 text-sm">
                                <span class="w-4 h-4 rounded bg-cyan-600/20 border border-cyan-600/40 flex items-center justify-center text-cyan-400 text-[10px] shrink-0">&#10003;</span>
                                {{ $point }}
                            </div>
                        @endforeach
                    </div>
                    <a href="/contact" class="self-start px-6 py-3 rounded-xl bg-cyan-700 hover:bg-cyan-600 text-white font-black uppercase tracking-wider text-sm transition-colors border border-cyan-600/40">
                        Explore RELAY &rarr;
                    </a>
                </div>

                {{-- Email sequence visual --}}
                <div class="p-10 md:p-14 flex items-center">
                    <div class="w-full space-y-2">
                        <p class="text-cyan-600 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Active sequence — New Lead Nurture</p>
                        @foreach([
                            ['Day 0',  'Welcome + quick win',    '62% open',  'Sent',      'text-green-400'],
                            ['Day 2',  'Case study delivery',    '48% open',  'Sent',      'text-green-400'],
                            ['Day 5',  'Objection handler',      '41% open',  'Sent',      'text-green-400'],
                            ['Day 9',  'Social proof + offer',   '&mdash;',   'Scheduled', 'text-cyan-500'],
                            ['Day 14', 'Last-chance close',      '&mdash;',   'Scheduled', 'text-gray-600'],
                        ] as [$day, $subject, $stat, $status, $statusColor])
                            <div class="flex items-center gap-3 rounded-xl p-3" style="background: rgba(6,182,212,0.06); border: 1px solid rgba(6,182,212,0.12);">
                                <span class="text-cyan-700 text-[10px] font-mono w-10 shrink-0">{{ $day }}</span>
                                <span class="text-gray-300 text-xs flex-1">{{ $subject }}</span>
                                <span class="text-gray-600 text-[10px] w-16 text-right">{!! $stat !!}</span>
                                <span class="text-[10px] font-bold {{ $statusColor }} w-16 text-right">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ── SURGE ─────────────────────────────────────────────────────────── --}}
<section id="surge" class="px-6 pt-5">
    <div class="max-w-6xl mx-auto">
        <div class="product-card rounded-3xl overflow-hidden"
             style="background: radial-gradient(ellipse at 30% 100%, rgba(234,88,12,0.18) 0%, transparent 55%), radial-gradient(ellipse at 80% 0%, rgba(251,146,60,0.10) 0%, transparent 50%), #0e0600; border: 1px solid rgba(234,88,12,0.22);">
            <div class="grid md:grid-cols-2 gap-0">

                {{-- Pipeline visual --}}
                <div class="p-10 md:p-14 flex items-center order-2 md:order-1">
                    <div class="w-full">
                        <p class="text-orange-700 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Pipeline — This Month</p>
                        <div class="space-y-3">
                            @foreach([
                                ['New Leads',      '84',  'bg-orange-500', '100%'],
                                ['Contacted',      '61',  'bg-orange-600', '73%'],
                                ['Qualified',      '29',  'bg-amber-500',  '35%'],
                                ['Proposal Sent',  '14',  'bg-amber-600',  '17%'],
                                ['Closed Won',     '6',   'bg-green-500',  '7%'],
                            ] as [$stage, $count, $barColor, $width])
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-400">{{ $stage }}</span>
                                        <span class="text-orange-400 font-bold">{{ $count }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-white/5">
                                        <div class="h-2 rounded-full {{ $barColor }}" style="width: {{ $width }}; opacity: 0.8;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-5 pt-4 grid grid-cols-3 gap-3" style="border-top: 1px solid rgba(234,88,12,0.15);">
                            <div class="text-center">
                                <div class="text-2xl font-black text-orange-400">$48k</div>
                                <div class="text-gray-600 text-[10px] mt-0.5">Pipeline Value</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-black text-amber-400">7.1%</div>
                                <div class="text-gray-600 text-[10px] mt-0.5">Close Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-black text-green-400">+34%</div>
                                <div class="text-gray-600 text-[10px] mt-0.5">MoM Growth</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-10 md:p-14 flex flex-col justify-center order-1 md:order-2">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-900 border border-orange-500/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-orange-500 text-[10px] font-bold uppercase tracking-[0.25em]">smbgen &mdash; 04</span>
                            <div class="text-white font-black text-2xl uppercase tracking-widest">SURGE</div>
                        </div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-white leading-tight tracking-tight mb-4">
                        Fill the pipeline.<br>
                        <span class="text-orange-400">Dominate the market.</span>
                    </h2>
                    <p class="text-gray-400 text-base leading-relaxed mb-8">
                        Multi-channel lead generation and growth marketing on one platform.
                        SEO, paid campaigns, content strategy, lead scoring, and referral loops
                        — all measured against real revenue, not vanity metrics.
                    </p>
                    <div class="flex flex-col gap-2 mb-9">
                        @foreach(['SEO content at AI scale', 'Paid campaign management with AI bid optimisation', 'Lead scoring & prioritisation', 'Referral loops & partnership channels'] as $point)
                            <div class="flex items-center gap-2.5 text-gray-300 text-sm">
                                <span class="w-4 h-4 rounded bg-orange-600/20 border border-orange-600/40 flex items-center justify-center text-orange-400 text-[10px] shrink-0">&#10003;</span>
                                {{ $point }}
                            </div>
                        @endforeach
                    </div>
                    <a href="/contact" class="self-start px-6 py-3 rounded-xl bg-orange-700 hover:bg-orange-600 text-white font-black uppercase tracking-wider text-sm transition-colors border border-orange-600/40">
                        Explore SURGE &rarr;
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ── CAST ──────────────────────────────────────────────────────────── --}}
<section id="cast" class="px-6 pt-5">
    <div class="max-w-6xl mx-auto">
        <div class="product-card rounded-3xl overflow-hidden"
             style="background: radial-gradient(ellipse at 60% 0%, rgba(16,185,129,0.14) 0%, transparent 55%), #020e08; border: 1px solid rgba(16,185,129,0.2);">
            <div class="grid md:grid-cols-2 gap-0">

                {{-- Content --}}
                <div class="p-10 md:p-14 flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-900 border border-emerald-500/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-emerald-500 text-[10px] font-bold uppercase tracking-[0.25em]">smbgen &mdash; 05</span>
                            <div class="text-white font-black text-2xl uppercase tracking-widest">CAST</div>
                        </div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-white leading-tight tracking-tight mb-4">
                        Sites that convert.<br>
                        <span class="text-emerald-400">Delivered in weeks.</span>
                    </h2>
                    <p class="text-gray-400 text-base leading-relaxed mb-8">
                        Beautifully crafted, conversion-optimised websites built on the smbgen platform
                        and delivered fast. Brand identity, design systems, landing pages, full product sites —
                        every pixel intentional, every CTA wired.
                    </p>
                    <div class="flex flex-col gap-2 mb-9">
                        @foreach(['Brand identity & full design systems', 'Conversion-optimised landing pages & sites', 'CMS-powered — edit anything, no developer needed', 'Performance-tuned, mobile-first, SEO-ready from day one', 'Integrated payment page — accept client payments online'] as $point)
                            <div class="flex items-center gap-2.5 text-gray-300 text-sm">
                                <span class="w-4 h-4 rounded bg-emerald-600/20 border border-emerald-600/40 flex items-center justify-center text-emerald-400 text-[10px] shrink-0">&#10003;</span>
                                {{ $point }}
                            </div>
                        @endforeach
                    </div>
                    <a href="/contact" class="self-start px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white font-black uppercase tracking-wider text-sm transition-colors border border-emerald-600/40">
                        Explore CAST &rarr;
                    </a>
                </div>

                {{-- Browser mockup --}}
                <div class="p-10 md:p-14 flex items-center">
                    <div class="w-full rounded-2xl overflow-hidden" style="border: 1px solid rgba(16,185,129,0.2);">
                        {{-- Browser chrome --}}
                        <div class="flex items-center gap-2 px-4 py-3" style="background: rgba(16,185,129,0.08); border-bottom: 1px solid rgba(16,185,129,0.15);">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500/50"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/50"></span>
                            <div class="flex-1 mx-3 px-3 py-1 rounded text-[10px] text-gray-600" style="background: rgba(255,255,255,0.04);">yoursite.com</div>
                            <span class="text-emerald-600 text-[10px] font-bold">LIVE</span>
                        </div>
                        {{-- Site package routes --}}
                        <div class="p-5" style="background: rgba(0,0,0,0.4);">
                            <p class="text-emerald-700 text-[9px] font-black uppercase tracking-[0.2em] mb-4">Your site package</p>
                            @foreach([
                                ['/', 'Home', 'Your homepage &amp; hero'],
                                ['/blog', 'SEO Blog', 'AI-generated, search-optimised'],
                                ['/contact', 'Contact', 'Lead capture form'],
                                ['/pay', 'Payments', 'Accept client payments online'],
                            ] as [$route, $label, $desc])
                                <div class="flex items-center gap-3 rounded-lg px-3 py-2.5 mb-2 last:mb-0" style="background: rgba(16,185,129,0.07); border: 1px solid rgba(16,185,129,0.15);">
                                    <span class="text-emerald-500 font-mono text-[10px] w-14 shrink-0">{{ $route }}</span>
                                    <span class="text-white text-[10px] font-bold">{{ $label }}</span>
                                    <span class="text-gray-600 text-[9px] ml-auto text-right">{!! $desc !!}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="px-6 py-3 flex justify-between items-center" style="background: rgba(16,185,129,0.06); border-top: 1px solid rgba(16,185,129,0.1);">
                            <span class="text-emerald-600 text-[10px] font-mono">PageSpeed: 98</span>
                            <span class="text-emerald-600 text-[10px] font-mono">SEO: A+</span>
                            <span class="text-emerald-600 text-[10px] font-mono">Mobile: ✓</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ── VAULT ─────────────────────────────────────────────────────────── --}}
<section id="vault" class="px-6 pt-5 pb-24">
    <div class="max-w-6xl mx-auto">
        <div class="product-card rounded-3xl overflow-hidden"
             style="background: radial-gradient(ellipse at 35% 0%, rgba(99,102,241,0.15) 0%, transparent 55%), #05060f; border: 1px solid rgba(99,102,241,0.2);">
            <div class="grid md:grid-cols-2 gap-0">

                {{-- File/CRM visual --}}
                <div class="p-10 md:p-14 flex items-center order-2 md:order-1">
                    <div class="w-full space-y-2.5">
                        <p class="text-indigo-700 text-[10px] font-black uppercase tracking-[0.2em] mb-4">Client: Meridian Group</p>
                        @foreach([
                            ['📄', 'Project Proposal v3.pdf', '2.4 MB', 'Delivered', 'text-green-400'],
                            ['📋', 'Signed Contract.pdf', '1.1 MB', 'Signed', 'text-green-400'],
                            ['📊', 'Q1 Report.xlsx', '890 KB', 'Under Review', 'text-yellow-400'],
                            ['🖼️', 'Brand Assets.zip', '48 MB', 'Uploaded', 'text-indigo-400'],
                            ['📝', 'Meeting Notes — Mar 18', '&mdash;', 'Draft', 'text-gray-500'],
                        ] as [$icon, $name, $size, $status, $statusColor])
                            <div class="flex items-center gap-3 rounded-xl p-3" style="background: rgba(99,102,241,0.07); border: 1px solid rgba(99,102,241,0.14);">
                                <span class="text-base shrink-0">{{ $icon }}</span>
                                <span class="text-gray-300 text-xs flex-1 truncate">{{ $name }}</span>
                                <span class="text-gray-600 text-[10px] w-12 text-right">{!! $size !!}</span>
                                <span class="text-[10px] font-bold {{ $statusColor }} w-20 text-right">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-10 md:p-14 flex flex-col justify-center order-1 md:order-2">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-900 border border-indigo-500/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-indigo-400 text-[10px] font-bold uppercase tracking-[0.25em]">smbgen &mdash; 06</span>
                            <div class="text-white font-black text-2xl uppercase tracking-widest">VAULT</div>
                        </div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-white leading-tight tracking-tight mb-4">
                        Your operation.<br>
                        <span class="text-indigo-400">Organised. Delivered.</span>
                    </h2>
                    <p class="text-gray-400 text-base leading-relaxed mb-8">
                        CRM, lead management, secure document storage, and client portals in one system.
                        Track every contact from first touch to closed deal.
                        Deliver every file, report, and contract without the chaos.
                    </p>
                    <div class="flex flex-col gap-2 mb-9">
                        @foreach(['Contact & deal management with AI lead scoring', 'Secure client portals — share files, track approvals', 'Project document storage with version history', 'Automated follow-up sequences from inside the CRM'] as $point)
                            <div class="flex items-center gap-2.5 text-gray-300 text-sm">
                                <span class="w-4 h-4 rounded bg-indigo-600/20 border border-indigo-600/40 flex items-center justify-center text-indigo-400 text-[10px] shrink-0">&#10003;</span>
                                {{ $point }}
                            </div>
                        @endforeach
                    </div>
                    <a href="/contact" class="self-start px-6 py-3 rounded-xl bg-indigo-700 hover:bg-indigo-600 text-white font-black uppercase tracking-wider text-sm transition-colors border border-indigo-600/40">
                        Explore VAULT &rarr;
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ── CLOSING CTA ───────────────────────────────────────────────────── --}}
<section class="px-6 pb-28" style="background: #03040d;">
    <div class="max-w-4xl mx-auto text-center">
        <div class="rounded-3xl p-14" style="background: radial-gradient(ellipse at 50% 0%, rgba(59,130,246,0.12) 0%, transparent 70%), rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.07);">
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-5 leading-tight">
                Start with one.<br>Scale to the full suite.
            </h2>
            <p class="text-gray-500 text-lg mb-10 max-w-lg mx-auto font-light leading-relaxed">
                Every product runs on the same platform. Data flows freely between them.
                AI works across all of them. Add more as you grow.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/contact" class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-base transition-colors shadow-lg shadow-blue-900/30">
                    Talk to us &rarr;
                </a>
                <a href="{{ route('home') }}" class="px-8 py-4 rounded-xl text-gray-400 font-bold text-base transition-colors hover:text-white" style="border: 1px solid rgba(255,255,255,0.1);">
                    Back to platform
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
