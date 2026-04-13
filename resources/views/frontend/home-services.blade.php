@extends('layouts.frontend')

@section('title', 'smbgen Services — Implementation and growth on top of smbgen-core')
@section('description', 'Services that design, implement, and grow smbgen-core for your business: contact flow, booking, payments, client portal, CRM, CMS, and the campaigns that drive demand into them.')

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────────── --}}
<section class="bg-slate-950 py-28 px-6 relative overflow-hidden">

    {{-- Background grid accent --}}
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>

    <div class="max-w-5xl mx-auto relative">

        <div class="inline-flex items-center gap-2 bg-blue-600/15 text-blue-400 text-xs font-bold px-3.5 py-1.5 rounded-full mb-10 border border-blue-500/25 tracking-widest uppercase">
            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full inline-block animate-pulse"></span>
            Services layer for smbgen-core
        </div>

        <h1 class="text-6xl md:text-7xl font-black text-white leading-[1.05] tracking-tight mb-7">
            We build and run<br>
            <span class="text-blue-400">smbgen-core</span><br>
            for your business.
        </h1>

        <p class="text-xl text-slate-400 max-w-2xl mb-11 font-light leading-relaxed">
            The product is smbgen-core. The services are how we tailor it, launch it, improve it,
            and drive demand into it so your contact flow, booking flow, payments, portal, CRM, and CMS actually perform.
        </p>

        <div class="flex flex-wrap items-center gap-4">
            <a href="/contact" class="bg-blue-600 text-white font-bold px-7 py-3.5 rounded-xl hover:bg-blue-500 transition-all text-base shadow-lg shadow-blue-900/30">
                Talk to us &rarr;
            </a>
            <a href="#services" class="text-slate-400 font-semibold hover:text-white transition-colors text-base flex items-center gap-2">
                See all services <span>&darr;</span>
            </a>
        </div>

    </div>
</section>

{{-- ── CAPABILITY STRIP ─────────────────────────────────────────────── --}}
<div class="bg-blue-600 py-4 px-6">
    <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-center gap-x-8 gap-y-2">
        @foreach([
            ['Contact', true],
            ['Book', false],
            ['Pay', false],
            ['Client Portal', false],
            ['CRM', false],
            ['CMS', false],
            ['Growth Services', false],
        ] as [$cap, $active])
            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-widest whitespace-nowrap {{ $active ? 'text-white' : 'text-white/65' }}">
                <span class="text-blue-200">&#10022;</span>
                {{ $cap }}
            </div>
        @endforeach
    </div>
</div>

{{-- ── WHY SMBGEN ────────────────────────────────────────────────────── --}}
<section class="bg-white py-20 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="max-w-2xl mb-16">
            <span class="text-blue-600 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Why services still matter</span>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight mb-5">
                smbgen-core is the product. We are the team behind the rollout.
            </h2>
            <p class="text-gray-500 text-lg font-light leading-relaxed">
                Most businesses do not need more software. They need the right operating layer configured correctly,
                launched quickly, and improved constantly. That is where smbgen services come in.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-black text-gray-900 mb-2 tracking-tight">Implementation</h3>
                <p class="text-gray-500 text-sm leading-relaxed">We configure contact capture, booking, payments, portal access, CRM workflows, and CMS structure around your actual business process.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100">
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-black text-gray-900 mb-2 tracking-tight">Optimisation</h3>
                <p class="text-gray-500 text-sm leading-relaxed">We improve the conversion path after launch so the product becomes a revenue system, not just a nice-looking interface.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h3 class="text-lg font-black text-gray-900 mb-2 tracking-tight">Growth</h3>
                <p class="text-gray-500 text-sm leading-relaxed">We drive traffic and demand into smbgen-core through content, SEO, email, social, and lead generation work tied to real pipeline outcomes.</p>
            </div>
        </div>
    </div>
</section>

{{-- ── SERVICES ──────────────────────────────────────────────────────── --}}
<section id="services" class="bg-gray-50 py-24 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Services</span>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight mb-4">Everything you need. Nothing you don&rsquo;t.</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto font-light">
                Pick one service or engage the full stack. Every offering runs on the same platform, so everything talks to everything.
            </p>
        </div>

        {{-- Row 1 --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

            {{-- Web Design & Delivery --}}
            <div class="bg-blue-700 rounded-2xl p-8 text-white flex flex-col lg:col-span-1">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center mb-6 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-blue-300 text-xs font-black uppercase tracking-[0.2em] mb-3 block">01 &mdash; Web Design &amp; Delivery</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Websites that convert.</h3>
                <p class="text-blue-100 text-sm leading-relaxed mb-6 flex-1">
                    Beautifully crafted, conversion-optimised websites delivered fast.
                    From landing pages to full product sites — designed to win attention and turn visitors into leads.
                </p>
                <div class="flex flex-col gap-1.5 mb-6">
                    @foreach(['Brand identity & design systems', 'Custom landing pages & full sites', 'CMS-powered content management', 'Mobile-first, performance-tuned'] as $item)
                        <div class="flex items-center gap-2 text-blue-200 text-xs font-medium">
                            <span class="w-4 h-4 bg-blue-600/60 rounded flex items-center justify-center text-blue-200 shrink-0">&#10003;</span>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
                <a href="/contact" class="text-white font-bold text-sm hover:text-blue-200 flex items-center gap-1 transition-colors">
                    Start a project &rarr;
                </a>
            </div>

            {{-- Social Media Automation --}}
            <div class="bg-violet-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-violet-600 rounded-xl flex items-center justify-center mb-6 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                <span class="text-violet-300 text-xs font-black uppercase tracking-[0.2em] mb-3 block">02 &mdash; Social Media Automation</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Always-on social presence.</h3>
                <p class="text-violet-200 text-sm leading-relaxed mb-6 flex-1">
                    AI-generated content, scheduled publishing, and engagement tracking across every major platform.
                    Show up consistently without consuming your day.
                </p>
                <a href="/contact" class="text-white font-bold text-sm hover:text-violet-200 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

            {{-- Email Marketing --}}
            <div class="bg-cyan-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-cyan-600 rounded-xl flex items-center justify-center mb-6 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-cyan-200 text-xs font-black uppercase tracking-[0.2em] mb-3 block">03 &mdash; Email Marketing</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Email that actually lands.</h3>
                <p class="text-cyan-100 text-sm leading-relaxed mb-6 flex-1">
                    Automated nurture sequences, broadcast campaigns, and deliverability monitoring.
                    AI-written copy tuned to your voice and your audience.
                </p>
                <a href="/contact" class="text-white font-bold text-sm hover:text-cyan-100 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

        </div>

        {{-- Row 2 --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Lead Generation --}}
            <div class="bg-emerald-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center mb-6 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <span class="text-emerald-200 text-xs font-black uppercase tracking-[0.2em] mb-3 block">04 &mdash; Lead Generation</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Fill your pipeline.</h3>
                <p class="text-emerald-100 text-sm leading-relaxed mb-6 flex-1">
                    Multi-channel outbound and inbound strategies that bring qualified prospects to your door.
                    SEO, paid, content, and referral working in concert.
                </p>
                <a href="/contact" class="text-white font-bold text-sm hover:text-emerald-100 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

            {{-- Lead Capture & Management --}}
            <div class="bg-orange-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center mb-6 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-orange-200 text-xs font-black uppercase tracking-[0.2em] mb-3 block">05 &mdash; Lead Capture &amp; CRM</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Never lose a lead again.</h3>
                <p class="text-orange-100 text-sm leading-relaxed mb-6 flex-1">
                    Smart capture forms, automated follow-up sequences, and a built-in CRM to track every contact
                    from first touch to closed deal.
                </p>
                <a href="/contact" class="text-white font-bold text-sm hover:text-orange-100 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

            {{-- Project & File Management --}}
            <div class="bg-slate-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-slate-600 rounded-xl flex items-center justify-center mb-6 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <span class="text-slate-300 text-xs font-black uppercase tracking-[0.2em] mb-3 block">06 &mdash; Project &amp; File Management</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Organised. Delivered.</h3>
                <p class="text-slate-300 text-sm leading-relaxed mb-6 flex-1">
                    Secure document storage, client portals, project tracking, and file delivery.
                    Keep every engagement documented and every deliverable on time.
                </p>
                <a href="/contact" class="text-white font-bold text-sm hover:text-slate-200 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

        </div>
    </div>
</section>

{{-- ── GROWTH / AI SECTION ───────────────────────────────────────────── --}}
<section id="growth" class="bg-slate-950 py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        <div>
            <span class="text-slate-500 text-xs font-black uppercase tracking-[0.2em] mb-5 block">AI-Powered Growth</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                Grow aggressively with AI on your side.
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed mb-8">
                Our AI engine powers content generation, SEO optimisation, email copywriting,
                lead scoring, and social automation — dramatically increasing output
                while lowering cost per acquisition.
            </p>
            <div class="flex flex-col gap-3 mb-9">
                @foreach([
                    'AI-written SEO content at scale',
                    'Automated lead nurture sequences',
                    'Social content calendar, generated & scheduled',
                    'Intelligent lead scoring & prioritisation',
                    'Conversion-optimised email subject lines',
                ] as $point)
                    <div class="flex items-center gap-3 text-slate-300 text-sm">
                        <span class="w-5 h-5 bg-blue-600/20 border border-blue-500/30 rounded flex items-center justify-center text-blue-400 text-xs shrink-0">&#10003;</span>
                        {{ $point }}
                    </div>
                @endforeach
            </div>
            <a href="/contact" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-blue-500 transition-colors text-sm">
                See AI capabilities &rarr;
            </a>
        </div>

        {{-- AI stats panel --}}
        <div id="ai" class="bg-slate-900 rounded-2xl p-8 border border-slate-700">
            <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-6">AI Performance Metrics</div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-600/10 border border-blue-500/20 rounded-xl p-5 text-center">
                    <div class="text-3xl font-black text-blue-400 mb-1">10x</div>
                    <div class="text-slate-500 text-xs font-medium">Content Output</div>
                </div>
                <div class="bg-emerald-600/10 border border-emerald-500/20 rounded-xl p-5 text-center">
                    <div class="text-3xl font-black text-emerald-400 mb-1">-60%</div>
                    <div class="text-slate-500 text-xs font-medium">Cost Per Lead</div>
                </div>
                <div class="bg-violet-600/10 border border-violet-500/20 rounded-xl p-5 text-center">
                    <div class="text-3xl font-black text-violet-400 mb-1">24/7</div>
                    <div class="text-slate-500 text-xs font-medium">Lead Nurturing</div>
                </div>
                <div class="bg-orange-600/10 border border-orange-500/20 rounded-xl p-5 text-center">
                    <div class="text-3xl font-black text-orange-400 mb-1">3 wks</div>
                    <div class="text-slate-500 text-xs font-medium">Time to Live</div>
                </div>
            </div>
            <div class="bg-slate-800/50 rounded-xl p-4">
                <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-3">AI Capabilities</div>
                <div class="flex flex-wrap gap-2">
                    @foreach(['SEO Content', 'Email Copy', 'Social Posts', 'Lead Scoring', 'Image Generation', 'A/B Testing'] as $cap)
                        <span class="bg-slate-700 text-slate-300 text-xs font-semibold px-3 py-1.5 rounded-lg">{{ $cap }}</span>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ── PLATFORM TEASER ───────────────────────────────────────────────── --}}
<section id="platform" class="bg-blue-700 py-20 px-6">
    <div class="max-w-5xl mx-auto text-center">
        <span class="text-blue-300 text-xs font-black uppercase tracking-[0.2em] mb-6 block">Powered By</span>
        <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
            Built on a next-generation<br>distributed platform.
        </h2>
        <p class="text-blue-100 text-xl max-w-2xl mx-auto mb-10 font-light leading-relaxed">
            Every service runs on the smbgen platform — a modern, AI-native infrastructure
            designed for the speed and scale that growing businesses demand.
            <a href="{{ route('home.platform') }}" class="text-white font-bold underline underline-offset-2 hover:no-underline">
                Explore the platform &rarr;
            </a>
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach(['Laravel', 'Cloud-Native', 'AI Engine', 'Real-Time', 'API-First', 'Scalable', 'Secure'] as $tag)
                <span class="bg-blue-600/70 border border-blue-500/50 text-white text-sm font-bold px-4 py-2 rounded-full">{{ $tag }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ── FINAL CTA ─────────────────────────────────────────────────────── --}}
<section class="bg-slate-950 py-28 px-6">
    <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 bg-blue-600/15 text-blue-400 text-xs font-bold px-3.5 py-1.5 rounded-full mb-8 border border-blue-500/25 tracking-widest uppercase">
            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full inline-block"></span>
            Ready when you are
        </div>
        <h2 class="text-5xl md:text-6xl font-black text-white tracking-tight mb-6 leading-tight">
            Ready to launch<br>smbgen-core properly?
        </h2>
        <p class="text-slate-400 text-xl mb-11 max-w-xl mx-auto font-light leading-relaxed">
            Start with the product page, then let us scope the implementation, optimisation, and growth work around it.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('solutions') }}" class="bg-blue-600 text-white font-bold px-8 py-4 rounded-xl hover:bg-blue-500 transition-colors text-base shadow-lg shadow-blue-900/40">
                See smbgen-core &rarr;
            </a>
            <a href="/contact" class="border border-slate-700 text-slate-300 font-bold px-8 py-4 rounded-xl hover:border-slate-500 hover:text-white transition-colors text-base">
                Talk to us
            </a>
        </div>
    </div>
</section>

@endsection
