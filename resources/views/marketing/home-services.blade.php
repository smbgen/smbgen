@extends('layouts.marketing')

@section('title', 'smbgen — Expert Design, Development & Growth Marketing')
@section('description', 'Rapid application development, cloud delivery, content management, expert design, and growth marketing — all from one expert team.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     HERO — Light, professional, confident
═══════════════════════════════════════════════════════════ --}}
<section class="bg-white py-28 px-6 border-b border-gray-100">
    <div class="max-w-5xl mx-auto">

        <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs font-black px-3.5 py-1.5 rounded-full mb-10 border border-blue-200 tracking-widest uppercase">
            <span class="w-1.5 h-1.5 bg-blue-600 rounded-full inline-block"></span>
            Development &middot; Design &middot; Growth
        </div>

        <h1 class="text-6xl md:text-7xl font-black text-gray-900 leading-[1.05] tracking-tight mb-7">
            We build,<br>
            design, and<br>
            <span class="text-blue-600">grow your business.</span>
        </h1>

        <p class="text-xl text-gray-500 max-w-2xl mb-11 font-light leading-relaxed">
            smbgen is the expert team behind your entire digital stack — from rapid application
            development and cloud delivery to expert design, content management, and growth
            marketing that actually moves the needle.
        </p>

        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('register') }}" class="bg-blue-600 text-white font-bold px-7 py-3.5 rounded-xl hover:bg-blue-700 transition-all text-base shadow-sm">
                Start a project &rarr;
            </a>
            @if(Route::has('booking.wizard'))
                <a href="{{ route('booking.wizard') }}" class="border border-gray-200 text-gray-600 font-semibold px-7 py-3.5 rounded-xl hover:border-gray-400 hover:text-gray-900 transition-colors text-base">
                    Book a call
                </a>
            @endif
        </div>

        {{-- Trust strip --}}
        <div class="mt-16 pt-12 border-t border-gray-100 flex flex-wrap gap-8 items-center">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">What we deliver</p>
            @foreach(['Rapid App Dev', 'Cloud Delivery', 'Expert Design', 'CMS', 'Marketing Automation', 'Growth Marketing'] as $item)
                <span class="text-gray-500 text-sm font-semibold">{{ $item }}</span>
            @endforeach
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     01 — RAPID APPLICATION DEVELOPMENT
═══════════════════════════════════════════════════════════ --}}
<section id="platform" class="bg-indigo-700 py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        <div>
            <span class="text-indigo-300 text-xs font-black uppercase tracking-[0.2em] mb-5 block">01 &mdash; Development</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                We build your application. Fast.
            </h2>
            <p class="text-indigo-200 text-lg leading-relaxed mb-8">
                Whether you have a detailed spec or just an idea on a napkin — we take it
                from concept to fully deployed production software. Modern stack, clean code,
                built to scale from day one.
            </p>
            <div class="grid grid-cols-2 gap-4 mb-9">
                @foreach([
                    ['label' => 'Web Applications', 'icon' => '&#9633;'],
                    ['label' => 'API Development', 'icon' => '&#9672;'],
                    ['label' => 'Mobile-Ready', 'icon' => '&#9651;'],
                    ['label' => 'Integrations', 'icon' => '&#10022;'],
                ] as $item)
                    <div class="bg-indigo-800/50 border border-indigo-600/40 rounded-xl p-4">
                        <div class="text-indigo-300 text-lg mb-1">{!! $item['icon'] !!}</div>
                        <div class="text-white text-sm font-bold">{{ $item['label'] }}</div>
                    </div>
                @endforeach
            </div>
            <a href="/contact" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-bold px-5 py-3 rounded-xl hover:bg-indigo-50 transition-colors text-sm">
                Start a project &rarr;
            </a>
        </div>

        {{-- Visual --}}
        <div class="space-y-4">
            <div class="bg-indigo-900/60 rounded-2xl p-6 border border-indigo-600/40">
                <div class="text-indigo-400 text-xs font-black uppercase tracking-widest mb-3">Typical timeline</div>
                <div class="space-y-3">
                    @foreach([
                        ['phase' => 'Discovery & Spec', 'duration' => 'Week 1'],
                        ['phase' => 'Build & Iterate',  'duration' => 'Weeks 2–4'],
                        ['phase' => 'QA & Deploy',      'duration' => 'Week 5'],
                        ['phase' => 'Live &amp; Growing',    'duration' => 'Week 6+'],
                    ] as $i => $phase)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full {{ $i < 3 ? 'bg-green-400' : 'bg-indigo-500' }} flex items-center justify-center text-white text-xs font-black">
                                    {{ $i < 3 ? '✓' : $i + 1 }}
                                </div>
                                <span class="text-white text-sm font-medium">{!! $phase['phase'] !!}</span>
                            </div>
                            <span class="text-indigo-400 text-xs font-semibold">{{ $phase['duration'] }}</span>
                        </div>
                    @endforeach
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

        <div class="bg-slate-800 rounded-2xl p-8 border border-slate-700 order-2 md:order-1">
            <div class="text-slate-500 text-xs font-black uppercase tracking-widest mb-5">We handle all of this</div>
            <div class="space-y-3">
                @foreach([
                    ['label' => 'Server provisioning & configuration', 'done' => true],
                    ['label' => 'CI/CD pipeline setup', 'done' => true],
                    ['label' => 'SSL, domains & DNS', 'done' => true],
                    ['label' => 'Monitoring & uptime alerts', 'done' => true],
                    ['label' => 'Backups & disaster recovery', 'done' => true],
                    ['label' => 'Scaling as you grow', 'done' => true],
                ] as $item)
                    <div class="flex items-center gap-3 text-sm">
                        <span class="w-5 h-5 bg-emerald-500/20 border border-emerald-500/40 rounded flex items-center justify-center text-emerald-400 text-xs shrink-0">&#10003;</span>
                        <span class="text-slate-300 font-medium">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="order-1 md:order-2">
            <span class="text-slate-500 text-xs font-black uppercase tracking-[0.2em] mb-5 block">02 &mdash; Cloud Delivery</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                We deploy it, manage it, and keep it running.
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed mb-8">
                You focus on the business. We handle everything from containerized cloud
                deployments to global CDN delivery — fast, secure, and always on.
            </p>
            <a href="/contact" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-5 py-3 rounded-xl hover:bg-blue-500 transition-colors text-sm">
                Talk infrastructure &rarr;
            </a>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     03/04/05 — CMS · DESIGN · AUTOMATION
═══════════════════════════════════════════════════════════ --}}
<section id="services" class="bg-white py-24 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">The full suite</span>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight mb-4">Every capability. One team.</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto font-light">
                No coordinating between agencies. No gaps. We cover the entire stack.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">

            {{-- CMS --}}
            <div class="bg-violet-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-violet-600 rounded-xl flex items-center justify-center mb-6 text-xl text-violet-200 shrink-0 font-black">
                    &#9670;
                </div>
                <span class="text-violet-300 text-xs font-black uppercase tracking-[0.2em] mb-3 block">03 &mdash; CMS</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Content Management</h3>
                <p class="text-violet-200 text-sm leading-relaxed mb-6 flex-1">
                    We build and manage your content infrastructure — pages, blogs, landing pages,
                    and marketing content your team can actually edit and publish themselves.
                </p>
                <a href="#" class="text-white font-bold text-sm hover:text-violet-200 flex items-center gap-1 transition-colors">
                    Learn more &rarr;
                </a>
            </div>

            {{-- Design --}}
            <div class="bg-cyan-600 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-cyan-500 rounded-xl flex items-center justify-center mb-6 text-xl text-cyan-100 shrink-0 font-black">
                    &#9651;
                </div>
                <span class="text-cyan-200 text-xs font-black uppercase tracking-[0.2em] mb-3 block">04 &mdash; Design</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Expert Design</h3>
                <p class="text-cyan-100 text-sm leading-relaxed mb-6 flex-1">
                    Brand identity, UI/UX, and visual design that sets you apart. We create
                    design systems that scale — not templates you'll outgrow in six months.
                </p>
                <a href="#" class="text-white font-bold text-sm hover:text-cyan-100 flex items-center gap-1 transition-colors">
                    See our work &rarr;
                </a>
            </div>

            {{-- Automation --}}
            <div class="bg-emerald-700 rounded-2xl p-8 text-white flex flex-col">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center mb-6 text-xl text-emerald-200 shrink-0 font-black">
                    &#8635;
                </div>
                <span class="text-emerald-300 text-xs font-black uppercase tracking-[0.2em] mb-3 block">05 &mdash; Automation</span>
                <h3 class="text-2xl font-black mb-3 tracking-tight leading-tight">Marketing Automation</h3>
                <p class="text-emerald-100 text-sm leading-relaxed mb-6 flex-1">
                    Email sequences, lead nurture, CRM workflows — we set up and manage
                    automated marketing that keeps working long after you leave the office.
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
            We grow your business. Aggressively.
        </h2>
        <p class="text-orange-100 text-xl max-w-2xl mx-auto mb-10 font-light leading-relaxed">
            SEO, paid acquisition, content, partnerships, and guerrilla tactics &mdash; we build
            and execute the growth strategy that gets you customers and market share.
        </p>

        <div class="grid sm:grid-cols-3 gap-4 max-w-3xl mx-auto mb-12">
            @foreach([
                ['label' => 'SEO & Content', 'desc' => 'Dominate organic search in your niche'],
                ['label' => 'Paid Acquisition', 'desc' => 'Google, Meta, LinkedIn — profitable from day one'],
                ['label' => 'Guerrilla Tactics', 'desc' => 'Unconventional plays your competitors won\'t see coming'],
            ] as $item)
                <div class="bg-orange-500/60 border border-orange-400/40 rounded-xl p-5 text-left">
                    <div class="text-white font-black text-sm mb-1">{{ $item['label'] }}</div>
                    <div class="text-orange-200 text-xs leading-relaxed">{{ $item['desc'] }}</div>
                </div>
            @endforeach
        </div>

        <a href="/contact" class="inline-flex items-center gap-2 bg-white text-orange-700 font-black px-8 py-4 rounded-xl hover:bg-orange-50 transition-colors text-base shadow-xl shadow-orange-900/20">
            Let&rsquo;s talk growth &rarr;
        </a>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     PROCESS — brief, grounded
═══════════════════════════════════════════════════════════ --}}
<section class="bg-gray-50 py-24 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight mb-3">How we work</h2>
            <p class="text-gray-500 text-lg font-light">Simple process, fast results.</p>
        </div>

        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach([
                ['n' => '01', 'label' => 'Discover',  'desc' => 'We listen, ask the right questions, and define scope.'],
                ['n' => '02', 'label' => 'Build',     'desc' => 'Design, develop, and iterate in short sprints with you.'],
                ['n' => '03', 'label' => 'Launch',    'desc' => 'Deploy, test, and go live — fully managed.'],
                ['n' => '04', 'label' => 'Grow',      'desc' => 'Activate marketing, automate, and scale what works.'],
            ] as $step)
                <div class="bg-white border border-gray-200 rounded-2xl p-7">
                    <div class="text-3xl font-black text-blue-600 mb-3">{{ $step['n'] }}</div>
                    <h4 class="text-gray-900 font-black text-lg mb-2">{{ $step['label'] }}</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- Shared CTA --}}
@include('marketing.partials.section-cta')

@endsection
