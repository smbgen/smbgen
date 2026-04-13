@extends('layouts.marketing')

@section('title', 'smbgen — One platform. Every channel. AI-native.')
@section('description', 'smbgen is the AI-native platform that gives growing businesses the capabilities of an enterprise marketing and operations team — without the headcount.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     HERO — Dark, bold, confident
═══════════════════════════════════════════════════════════ --}}
<section class="bg-slate-950 py-28 px-6">
    <div class="max-w-5xl mx-auto">

        <div class="inline-flex items-center gap-2 bg-blue-600/15 text-blue-400 text-xs font-black px-3.5 py-1.5 rounded-full mb-10 border border-blue-500/25 tracking-widest uppercase">
            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full inline-block"></span>
            AI-Native &middot; Distributed &middot; Full-Stack
        </div>

        <h1 class="text-6xl md:text-7xl font-black text-white leading-[1.05] tracking-tight mb-7">
            One platform.<br>
            Every channel.<br>
            <span class="text-blue-400">AI working for you.</span>
        </h1>

        <p class="text-xl text-slate-400 max-w-2xl mb-11 font-light leading-relaxed">
            smbgen gives growing businesses the capabilities of an enterprise marketing and
            operations team — rapid app development, cloud delivery, content, design, lead
            generation, and AI-powered growth automation. No headcount required.
        </p>

        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('register') }}" class="bg-blue-600 text-white font-bold px-7 py-3.5 rounded-xl hover:bg-blue-500 transition-all text-base shadow-lg shadow-blue-900/30">
                Start for free &rarr;
            </a>
            <a href="#platform" class="text-slate-400 font-semibold hover:text-white transition-colors text-base flex items-center gap-2">
                See the platform <span class="text-slate-600">&darr;</span>
            </a>
        </div>

    </div>
</section>

{{-- Capability strip --}}
@include('marketing.partials.capability-strip')

{{-- ═══════════════════════════════════════════════════════════
     01 — RAPID APPLICATION DEVELOPMENT
═══════════════════════════════════════════════════════════ --}}
<section id="platform" class="bg-indigo-700 py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        <div>
            <span class="text-indigo-300 text-xs font-black uppercase tracking-[0.2em] mb-5 block">01 &mdash; Application Platform</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                Rapid application development that actually ships.
            </h2>
            <p class="text-indigo-200 text-lg leading-relaxed mb-8">
                Full-stack web applications built at pace without compromising quality.
                We turn concepts into production-ready software — weeks, not quarters.
                Your idea deserves to exist sooner than you think.
            </p>
            <div class="flex flex-col gap-3 mb-9">
                @foreach([
                    'Laravel · React · Vue · Alpine.js · Livewire',
                    'API design, integrations, event-driven architecture',
                    'From prototype to production — fully tested, fully deployed',
                ] as $point)
                    <div class="flex items-center gap-3 text-white text-sm font-medium">
                        <span class="w-5 h-5 bg-indigo-500/60 rounded flex items-center justify-center text-indigo-200 text-xs shrink-0">&#10003;</span>
                        {{ $point }}
                    </div>
                @endforeach
            </div>
            <a href="#" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-bold px-5 py-3 rounded-xl hover:bg-indigo-50 transition-colors text-sm">
                Explore the platform &rarr;
            </a>
        </div>

        {{-- Code panel --}}
        <div class="bg-indigo-900/60 rounded-2xl p-8 border border-indigo-600/40 font-mono text-xs leading-relaxed">
            <div class="flex items-center gap-2 mb-5">
                <span class="w-3 h-3 rounded-full bg-red-400/70"></span>
                <span class="w-3 h-3 rounded-full bg-yellow-400/70"></span>
                <span class="w-3 h-3 rounded-full bg-green-400/70"></span>
                <span class="text-indigo-400 ml-2 text-[10px] tracking-widest uppercase">smbgen.platform</span>
            </div>
            <div class="space-y-1.5 text-indigo-300">
                <div><span class="text-blue-300">const</span> <span class="text-white">app</span> = <span class="text-green-300">build</span>({</div>
                <div class="pl-5"><span class="text-indigo-200">stack:</span>    <span class="text-yellow-300">'laravel + react'</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">deploy:</span>   <span class="text-yellow-300">'cloud-native'</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">ai:</span>       <span class="text-yellow-300">true</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">timeline:</span> <span class="text-yellow-300">'weeks'</span>,</div>
                <div>});</div>
                <div class="mt-5 pt-4 border-t border-indigo-700/50">
                    <div class="text-green-400 font-semibold">&#10003; Build complete &mdash; deployed to production</div>
                    <div class="text-indigo-400 mt-1">&#9654; 3 services &middot; 0 errors &middot; 99.9% uptime</div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     02 — CLOUD DELIVERY
═══════════════════════════════════════════════════════════ --}}
<section id="cloud" class="bg-slate-900 py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        {{-- Stats panel --}}
        <div class="bg-slate-800 rounded-2xl p-8 border border-slate-700 order-2 md:order-1">
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-blue-600/15 border border-blue-500/25 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-blue-400">99.9%</div>
                    <div class="text-slate-500 text-xs mt-1 font-medium">Uptime SLA</div>
                </div>
                <div class="bg-emerald-600/15 border border-emerald-500/25 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-emerald-400">&lt;50ms</div>
                    <div class="text-slate-500 text-xs mt-1 font-medium">Response</div>
                </div>
                <div class="bg-violet-600/15 border border-violet-500/25 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-violet-400">&#8734;</div>
                    <div class="text-slate-500 text-xs mt-1 font-medium">Scale</div>
                </div>
            </div>
            <div class="bg-slate-700/40 rounded-xl p-5">
                <div class="text-slate-500 text-xs font-black uppercase tracking-widest mb-3">Infrastructure</div>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['AWS', 'Cloudflare', 'Docker', 'CI/CD', 'Auto-scale', 'Redis', 'Queues'] as $tech)
                        <span class="bg-slate-700 text-slate-300 text-xs font-semibold px-3 py-1.5 rounded-lg">{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="order-1 md:order-2">
            <span class="text-slate-500 text-xs font-black uppercase tracking-[0.2em] mb-5 block">02 &mdash; Cloud Delivery</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                Cloud-native delivery at any scale.
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed mb-8">
                From containerized deployments to global edge networks — we architect and
                operate cloud infrastructure that's fast, resilient, and built for wherever
                your business is going.
            </p>
            <a href="#" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-5 py-3 rounded-xl hover:bg-blue-500 transition-colors text-sm">
                Explore infrastructure &rarr;
            </a>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     03/04/05 — CMS · DESIGN · AUTOMATION
═══════════════════════════════════════════════════════════ --}}
<section id="services" class="bg-gray-50 py-24 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Full-stack capabilities</span>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight mb-4">Every layer. One platform.</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto font-light">
                Content, design, and marketing automation — end-to-end, no vendor stitching.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">

            {{-- CMS --}}
            <div class="bg-violet-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-violet-600 rounded-xl flex items-center justify-center mb-6 text-xl font-black text-violet-200 shrink-0">
                    &#9670;
                </div>
                <span class="text-violet-300 text-xs font-black uppercase tracking-[0.2em] mb-3 block">03 &mdash; CMS</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Content Management</h3>
                <p class="text-violet-200 text-sm leading-relaxed mb-6 flex-1">
                    A CMS built for teams that ship. Structured content, dynamic pages,
                    AI-assisted copy, and a publishing workflow that keeps everyone moving.
                </p>
                <a href="#" class="text-white font-bold text-sm hover:text-violet-200 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

            {{-- Design --}}
            <div class="bg-cyan-600 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-cyan-500 rounded-xl flex items-center justify-center mb-6 text-xl font-black text-cyan-100 shrink-0">
                    &#9651;
                </div>
                <span class="text-cyan-200 text-xs font-black uppercase tracking-[0.2em] mb-3 block">04 &mdash; Design</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Expert Design</h3>
                <p class="text-cyan-100 text-sm leading-relaxed mb-6 flex-1">
                    Brand identity to UI systems. Design that stands out, scales, and converts.
                    No templates. No generic. Everything intentional.
                </p>
                <a href="#" class="text-white font-bold text-sm hover:text-cyan-100 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

            {{-- Automation --}}
            <div class="bg-emerald-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center mb-6 text-xl font-black text-emerald-200 shrink-0">
                    &#8635;
                </div>
                <span class="text-emerald-300 text-xs font-black uppercase tracking-[0.2em] mb-3 block">05 &mdash; Automation</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Marketing Automation</h3>
                <p class="text-emerald-100 text-sm leading-relaxed mb-6 flex-1">
                    Email sequences, social scheduling, CRM workflows — AI-powered automation
                    that warms leads and drives revenue while you sleep.
                </p>
                <a href="#" class="text-white font-bold text-sm hover:text-emerald-100 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     06 — GROWTH MARKETING
═══════════════════════════════════════════════════════════ --}}
<section id="growth" class="bg-orange-600 py-24 px-6">
    <div class="max-w-5xl mx-auto text-center">

        <span class="text-orange-200 text-xs font-black uppercase tracking-[0.2em] mb-6 block">06 &mdash; Growth Marketing</span>
        <h2 class="text-5xl md:text-6xl font-black text-white leading-tight tracking-tight mb-6 max-w-3xl mx-auto">
            Guerrilla growth for ambitious brands.
        </h2>
        <p class="text-orange-100 text-xl max-w-2xl mx-auto mb-10 font-light leading-relaxed">
            Unconventional strategy. Aggressive execution. SEO, paid, content, partnerships &mdash;
            whatever it takes to acquire customers and own your market segment.
        </p>

        <div class="flex flex-wrap justify-center gap-3 mb-12">
            @foreach(['SEO Domination', 'Paid Acquisition', 'Content Strategy', 'Lead Generation', 'Viral Loops', 'Guerrilla Tactics'] as $tactic)
                <span class="bg-orange-500/70 border border-orange-400/50 text-white text-sm font-bold px-4 py-2 rounded-full">
                    {{ $tactic }}
                </span>
            @endforeach
        </div>

        <a href="/contact" class="inline-flex items-center gap-2 bg-white text-orange-700 font-black px-8 py-4 rounded-xl hover:bg-orange-50 transition-colors text-base shadow-xl shadow-orange-900/20">
            Let&rsquo;s talk growth &rarr;
        </a>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     LEAD CAPTURE — AI qualifier strip
═══════════════════════════════════════════════════════════ --}}
<section class="bg-slate-800 py-16 px-6">
    <div class="max-w-4xl mx-auto text-center">
        <h3 class="text-2xl md:text-3xl font-black text-white tracking-tight mb-3">
            Stop subscribing to 9 tools.
        </h3>
        <p class="text-slate-400 mb-8 font-light">
            smbgen replaces your social scheduler, email platform, CRM, website builder, lead capture tool, and project manager &mdash; and adds AI on top of all of it.
        </p>
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            @foreach(['Email Marketing', 'Social Media', 'Web Design & Delivery', 'Lead Capture', 'Lead Management', 'File & Doc Management'] as $feature)
                <span class="border border-slate-600 text-slate-300 text-xs font-semibold px-3.5 py-1.5 rounded-full">{{ $feature }}</span>
            @endforeach
        </div>
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-7 py-3.5 rounded-xl hover:bg-blue-500 transition-colors text-sm">
            Try the platform free &rarr;
        </a>
    </div>
</section>

{{-- Shared CTA --}}
@include('marketing.partials.section-cta')

@endsection
