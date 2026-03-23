@extends('layouts.frontend')

@section('title', 'smbgen-core — Contact, Book, Pay, Client Portal, CRM, CMS')
@section('description', 'smbgen-core is the simple operating layer for growing businesses: contact capture, booking, payments, client portal access, CRM, and CMS in one connected product.')

@section('content')

{{-- ── PLATFORM OVERVIEW ─────────────────────────────────────────────── --}}
<section id="platform" class="bg-indigo-700 py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        <div>
            <span class="text-indigo-300 text-xs font-black uppercase tracking-[0.2em] mb-5 block">How smbgen-core works</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                One connected core instead of six disconnected tools.
            </h2>
            <p class="text-indigo-200 text-lg leading-relaxed mb-8">
                Contact, booking, payments, portal access, CRM, and CMS all live in the same operating layer.
                That means less friction for customers and less cleanup work for your team.
            </p>
            <div class="flex flex-col gap-3 mb-9">
                @foreach([
                    'Laravel &middot; Livewire &middot; Alpine.js &middot; Tailwind CSS',
                    'API design, integrations & event-driven architecture',
                    'AI-augmented workflows built-in, not bolted on',
                    'From idea to live product in 3&ndash;6 weeks',
                ] as $item)
                    <div class="flex items-start gap-3 text-white text-sm font-medium">
                        <span class="w-5 h-5 bg-indigo-500/60 rounded flex items-center justify-center text-indigo-200 text-xs shrink-0 mt-0.5">&#10003;</span>
                        <span>{!! $item !!}</span>
                    </div>
                @endforeach
            </div>
            <a href="/contact" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-bold px-5 py-3 rounded-xl hover:bg-indigo-50 transition-colors text-sm">
                Start a project &rarr;
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
                <div><span class="text-blue-300">const</span> <span class="text-white">platform</span> = <span class="text-green-300">smbgen</span>.build({</div>
                <div class="pl-5"><span class="text-indigo-200">stack:</span>    <span class="text-yellow-300">'laravel + ai'</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">deploy:</span>   <span class="text-yellow-300">'cloud-native'</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">channels:</span> <span class="text-yellow-300">['web', 'social', 'email', 'leads']</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">ai:</span>       <span class="text-yellow-300">true</span>,</div>
                <div class="pl-5"><span class="text-indigo-200">timeline:</span> <span class="text-yellow-300">'weeks, not months'</span>,</div>
                <div>});</div>
                <div class="mt-5 pt-4 border-t border-indigo-700/50 space-y-1.5">
                    <div class="text-green-400 font-semibold">&#10003; Build complete &mdash; deploying to prod</div>
                    <div class="text-indigo-400">&#9654; 7 modules &middot; 0 errors &middot; AI active</div>
                    <div class="text-blue-400">&#9654; Leads pipeline: active</div>
                    <div class="text-violet-400">&#9654; Social scheduler: running</div>
                </div>
            </div>
        </div>

    </div>
</section>

<section id="start-here" class="bg-white px-6 py-12 md:py-16">
    <div class="max-w-6xl mx-auto">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @php
                $startHereItems = [
                    [
                        'step' => '01',
                        'title' => 'Contact',
                        'body' => 'A superior contact experience with structured intake, clearer qualification, and smarter routing than a generic form page.',
                        'href' => route('solutions') . '#contact-core',
                        'cta' => 'See contact page',
                        'pillClass' => 'bg-blue-100 text-blue-700',
                        'ctaClass' => 'group-hover:text-blue-700',
                        'demo' => 'contact',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Book',
                        'body' => 'Scheduling that removes friction with availability, confirmations, reminders, and a smoother path from interest to appointment.',
                        'href' => route('solutions') . '#book-core',
                        'cta' => 'See booking page',
                        'pillClass' => 'bg-violet-100 text-violet-700',
                        'ctaClass' => 'group-hover:text-violet-700',
                        'demo' => 'book',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Pay',
                        'body' => 'A simpler payment experience that feels trustworthy and fast, with a cleaner handoff from approval to invoice to paid.',
                        'href' => route('solutions') . '#pay-core',
                        'cta' => 'See payments page',
                        'pillClass' => 'bg-emerald-100 text-emerald-700',
                        'ctaClass' => 'group-hover:text-emerald-700',
                        'demo' => 'pay',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Client Portal',
                        'body' => 'One clear place for clients to log in, view files, track progress, manage billing, and stay aligned without extra back-and-forth.',
                        'href' => route('solutions') . '#portal-core',
                        'cta' => 'See portal page',
                        'pillClass' => 'bg-orange-100 text-orange-700',
                        'ctaClass' => 'group-hover:text-orange-700',
                        'demo' => 'portal',
                    ],
                    [
                        'step' => '05',
                        'title' => 'CRM',
                        'body' => 'Track leads, conversations, deals, follow-ups, and customer history in one place so nothing falls through the cracks.',
                        'href' => route('solutions') . '#crm-core',
                        'cta' => 'See CRM',
                        'pillClass' => 'bg-indigo-100 text-indigo-700',
                        'ctaClass' => 'group-hover:text-indigo-700',
                        'demo' => 'crm',
                    ],
                    [
                        'step' => '06',
                        'title' => 'CMS',
                        'body' => 'Update pages, publish offers, and manage content without turning every site change into a development ticket.',
                        'href' => route('solutions') . '#cms-core',
                        'cta' => 'See CMS',
                        'pillClass' => 'bg-cyan-100 text-cyan-700',
                        'ctaClass' => 'group-hover:text-cyan-700',
                        'demo' => 'cms',
                    ],
                ];
            @endphp

            @foreach($startHereItems as $item)
                <a href="{{ $item['href'] }}" class="group rounded-3xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-7 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-xl hover:border-gray-300">
                    <div class="mb-5 flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-[0.25em] text-gray-400">{{ $item['step'] }}</span>
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.2em] {{ $item['pillClass'] }}">{{ $item['title'] }}</span>
                    </div>
                    <h3 class="mb-3 text-2xl font-black tracking-tight text-gray-900">{{ $item['title'] }}</h3>
                    <p class="mb-6 text-sm leading-relaxed text-gray-600">{{ $item['body'] }}</p>

                    <div class="mb-6 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        @if($item['demo'] === 'contact')
                            <div class="space-y-2">
                                <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                    <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-blue-50 border border-blue-100 px-3 py-2 text-[11px] text-blue-700">
                                    <span>Qualified routing</span>
                                    <span class="inline-block h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                                </div>
                            </div>
                        @elseif($item['demo'] === 'book')
                            <div class="space-y-2">
                                <div class="grid grid-cols-3 gap-2 text-[10px] font-bold text-violet-700">
                                    <div class="rounded-lg bg-violet-100 py-1.5 text-center">Mon</div>
                                    <div class="rounded-lg bg-violet-100 py-1.5 text-center">Tue</div>
                                    <div class="rounded-lg bg-violet-100 py-1.5 text-center">Wed</div>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-white border border-gray-200 px-3 py-2 text-[11px]">
                                    <span>11:30 AM</span>
                                    <span class="text-violet-700 font-semibold">Available</span>
                                </div>
                            </div>
                        @elseif($item['demo'] === 'pay')
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                                    <span>Invoice #1048</span>
                                    <span>$1,250.00</span>
                                </div>
                                <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                <div class="rounded-lg bg-emerald-600 text-white text-xs font-bold py-2 text-center animate-pulse">Pay now</div>
                            </div>
                        @elseif($item['demo'] === 'portal')
                            <div class="space-y-2 text-[11px]">
                                @foreach(['Files', 'Messages', 'Billing'] as $portalItem)
                                    <div class="flex items-center justify-between rounded-lg bg-white border border-gray-200 px-3 py-2">
                                        <span>{{ $portalItem }}</span>
                                        <span class="text-orange-700 font-semibold">Open</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($item['demo'] === 'crm')
                            <div class="space-y-2 text-[11px]">
                                <div class="flex items-center justify-between rounded-lg bg-indigo-50 border border-indigo-100 px-3 py-2">
                                    <span>New lead</span>
                                    <span class="font-semibold text-indigo-700">High intent</span>
                                </div>
                                <div class="h-2 rounded-full bg-indigo-100 overflow-hidden">
                                    <div class="h-2 w-2/3 bg-indigo-500 animate-pulse"></div>
                                </div>
                            </div>
                        @else
                            <div class="space-y-2">
                                <div class="h-8 rounded-lg bg-white border border-gray-200"></div>
                                <div class="h-16 rounded-lg bg-white border border-gray-200"></div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="h-10 rounded-lg bg-cyan-100"></div>
                                    <div class="h-10 rounded-lg bg-cyan-100"></div>
                                    <div class="h-10 rounded-lg bg-cyan-100"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 transition-colors {{ $item['ctaClass'] }}">
                        {{ $item['cta'] }}
                        <span>&rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ── CLOUD DELIVERY ────────────────────────────────────────────────── --}}
<section class="bg-slate-900 py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">

        {{-- Stats panel --}}
        <div class="bg-slate-800 rounded-2xl p-8 border border-slate-700 order-2 md:order-1">
            <div class="grid grid-cols-3 gap-4 mb-5">
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
                <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-3">Infrastructure Stack</div>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['AWS', 'Cloudflare', 'Docker', 'CI/CD', 'Auto-scale', 'Redis', 'Horizon', 'Queues'] as $tag)
                        <span class="bg-slate-700 text-slate-300 text-xs font-semibold px-3 py-1.5 rounded-lg">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="order-1 md:order-2">
            <span class="text-slate-500 text-xs font-black uppercase tracking-[0.2em] mb-5 block">02 &mdash; Cloud Delivery</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                Cloud-native. Globally distributed.
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed mb-8">
                Deploy anywhere on the planet. Auto-scaling infrastructure, blazing-fast edge delivery,
                and zero-downtime deployments — built for wherever your customers are.
            </p>
            <a href="/contact" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-5 py-3 rounded-xl hover:bg-blue-500 transition-colors text-sm">
                Explore infrastructure &rarr;
            </a>
        </div>

    </div>
</section>

{{-- ── PLATFORM MODULES GRID ─────────────────────────────────────────── --}}
<section id="modules" class="bg-gray-50 py-24 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Platform Modules</span>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight mb-4">One platform. All the tools.</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto font-light">
                Every module is deeply integrated. Data flows freely. AI works across everything.
                Nothing is siloed, nothing is bolted on.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">

            @foreach([
                [
                    'color'   => 'violet',
                    'icon'    => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',
                    'num'     => '03',
                    'label'   => 'Social Automation',
                    'title'   => 'Social Media at Scale',
                    'body'    => 'AI-generated posts, scheduled across platforms, with engagement analytics feeding back into your lead pipeline.',
                ],
                [
                    'color'   => 'cyan',
                    'icon'    => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    'num'     => '04',
                    'label'   => 'Email Marketing',
                    'title'   => 'Email That Converts',
                    'body'    => 'Broadcast campaigns, drip sequences, and AI-written copy. Deliverability monitoring included. Tight CRM integration out of the box.',
                ],
                [
                    'color'   => 'emerald',
                    'icon'    => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                    'num'     => '05',
                    'label'   => 'Lead Generation',
                    'title'   => 'Full-Funnel Lead Gen',
                    'body'    => 'SEO, paid, content strategy, and referral loops working in concert. Capture forms that feed directly into your CRM.',
                ],
                [
                    'color'   => 'orange',
                    'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    'num'     => '06',
                    'label'   => 'Lead Management',
                    'title'   => 'Built-In CRM',
                    'body'    => 'Contact management, deal tracking, automated follow-ups, and AI-powered lead scoring. Know exactly who to call next.',
                ],
                [
                    'color'   => 'blue',
                    'icon'    => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                    'num'     => '07',
                    'label'   => 'AI Content Engine',
                    'title'   => 'Content at Machine Speed',
                    'body'    => 'Blog posts, landing copy, email sequences, social captions — all generated, reviewed, and published from one AI-powered content hub.',
                ],
                [
                    'color'   => 'slate',
                    'icon'    => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                    'num'     => '08',
                    'label'   => 'Document Management',
                    'title'   => 'Project Files. Organised.',
                    'body'    => 'Secure client portals, document delivery, project tracking, and approval workflows. Every engagement, documented end-to-end.',
                ],
            ] as $module)
                @php $c = $module['color']; @endphp
                <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all flex flex-col">
                    <div class="w-10 h-10 bg-{{ $c }}-100 rounded-xl flex items-center justify-center mb-5 shrink-0">
                        <svg class="w-5 h-5 text-{{ $c }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-{{ $c }}-600 text-xs font-black uppercase tracking-[0.2em] mb-2 block">{{ $module['num'] }} &mdash; {{ $module['label'] }}</span>
                    <h3 class="text-lg font-black text-gray-900 mb-2 tracking-tight">{{ $module['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-1">{{ $module['body'] }}</p>
                </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ── AI DIFFERENTIATOR ─────────────────────────────────────────────── --}}
<section id="ai" class="bg-slate-950 py-24 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-slate-600 text-xs font-black uppercase tracking-[0.2em] mb-4 block">AI-Derived Intelligence</span>
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">
                Not AI features. An AI-native platform.
            </h2>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto font-light">
                smbgen&rsquo;s AI isn&rsquo;t a chatbot widget bolted to a legacy system.
                It&rsquo;s woven into every module — generating, optimising, scoring, and automating
                across your entire operation, 24/7.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['Generate', 'Blog posts, email copy, social captions, landing page content — produced at scale, tuned to your voice.', 'text-blue-400', 'bg-blue-600/10 border-blue-500/20'],
                ['Optimise', 'Every piece of content scored and improved for SEO, readability, and conversion before it goes out.', 'text-violet-400', 'bg-violet-600/10 border-violet-500/20'],
                ['Automate', 'Workflows that run themselves. Social posting, email sequences, lead follow-up — handled without manual intervention.', 'text-emerald-400', 'bg-emerald-600/10 border-emerald-500/20'],
                ['Score', 'AI ranks your leads by conversion probability so your team focuses energy where it matters most.', 'text-orange-400', 'bg-orange-600/10 border-orange-500/20'],
            ] as [$title, $body, $textColor, $bg])
                <div class="rounded-2xl p-7 border {{ $bg }}">
                    <div class="text-2xl font-black {{ $textColor }} mb-3">{{ $title }}</div>
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $body }}</p>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ── GROWTH SECTION ────────────────────────────────────────────────── --}}
<section id="growth" class="bg-orange-600 py-24 px-6">
    <div class="max-w-5xl mx-auto text-center">
        <span class="text-orange-200 text-xs font-black uppercase tracking-[0.2em] mb-6 block">Growth Engine</span>
        <h2 class="text-5xl md:text-6xl font-black text-white leading-tight tracking-tight mb-6 max-w-3xl mx-auto">
            Guerrilla growth for ambitious businesses.
        </h2>
        <p class="text-orange-100 text-xl max-w-2xl mx-auto mb-10 font-light leading-relaxed">
            Unconventional strategy. Aggressive execution. SEO, paid, content, partnerships, and viral loops —
            the full growth playbook deployed on one AI-powered platform.
        </p>
        <div class="flex flex-wrap justify-center gap-3 mb-12">
            @foreach(['SEO Domination', 'Paid Acquisition', 'Content Strategy', 'Partnership Growth', 'Viral Loops', 'Guerrilla Tactics', 'Social Automation', 'Email Nurturing'] as $tag)
                <span class="bg-orange-500/70 border border-orange-400/50 text-white text-sm font-bold px-4 py-2 rounded-full">{{ $tag }}</span>
            @endforeach
        </div>
        <a href="/contact" class="inline-flex items-center gap-2 bg-white text-orange-700 font-black px-8 py-4 rounded-xl hover:bg-orange-50 transition-colors text-base shadow-xl shadow-orange-900/20">
            Let&rsquo;s talk growth &rarr;
        </a>
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
            Ready to build<br>something great?
        </h2>
        <p class="text-slate-400 text-xl mb-11 max-w-xl mx-auto font-light leading-relaxed">
            Start on the platform, engage the services, or just get in touch. We move fast.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}" class="bg-blue-600 text-white font-bold px-8 py-4 rounded-xl hover:bg-blue-500 transition-colors text-base shadow-lg shadow-blue-900/40">
                Start for free &rarr;
            </a>
            <a href="{{ route('home.services') }}" class="border border-slate-700 text-slate-300 font-bold px-8 py-4 rounded-xl hover:border-slate-500 hover:text-white transition-colors text-base">
                View services
            </a>
        </div>
    </div>
</section>

@endsection
