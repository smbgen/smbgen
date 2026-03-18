@extends('layouts.frontend')

@section('title', 'Portal7 — L7 Media Labs × smbgen Partner Spotlight')
@section('description', 'See how L7 Media Labs uses Portal7, their smbgen instance, to amplify brand DNA across their entire portfolio — social automation, email marketing, lead capture, and client delivery in one intelligent platform.')

@push('head')
<style>
    .l7-bg {
        background:
            radial-gradient(ellipse at 15% 10%,  rgba(251,191,36,0.08) 0%, transparent 50%),
            radial-gradient(ellipse at 85% 90%,  rgba(59,130,246,0.07) 0%, transparent 50%),
            radial-gradient(ellipse at 50% 50%,  rgba(251,191,36,0.03) 0%, transparent 70%),
            #04040a;
    }
    .l7-card {
        background: rgba(251,191,36,0.04);
        border: 1px solid rgba(251,191,36,0.14);
    }
    .l7-card:hover {
        background: rgba(251,191,36,0.07);
        border-color: rgba(251,191,36,0.25);
    }
    .portal7-badge {
        background: linear-gradient(135deg, rgba(251,191,36,0.15), rgba(59,130,246,0.15));
        border: 1px solid rgba(251,191,36,0.25);
    }
    @keyframes drift {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-8px); }
    }
    .drift { animation: drift 6s ease-in-out infinite; }
    .drift-slow { animation: drift 9s ease-in-out infinite; animation-delay: 2s; }
</style>
@endpush

@section('content')

{{-- ── PARTNER HERO ──────────────────────────────────────────────────── --}}
<section class="l7-bg min-h-[92vh] flex items-center px-6 py-20 relative overflow-hidden">

    {{-- Ambient particles --}}
    <div class="absolute top-20 right-20 w-2 h-2 rounded-full bg-amber-400/40 drift"></div>
    <div class="absolute top-1/3 right-1/4 w-1.5 h-1.5 rounded-full bg-amber-400/25 drift-slow"></div>
    <div class="absolute bottom-1/3 left-1/5 w-2 h-2 rounded-full bg-blue-400/30 drift"></div>
    <div class="absolute bottom-20 right-1/3 w-1 h-1 rounded-full bg-amber-300/40 drift-slow"></div>

    <div class="max-w-6xl mx-auto w-full relative">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- ── Left: copy ──────────────────────────────────────────── --}}
            <div>
                {{-- Partner badge --}}
                <div class="inline-flex items-center gap-2.5 portal7-badge px-4 py-2 rounded-full mb-10">
                    <span class="text-amber-400 text-[10px] font-black uppercase tracking-[0.25em]">Partner Spotlight</span>
                    <span class="w-px h-3 bg-amber-400/30"></span>
                    <span class="text-blue-400 text-[10px] font-black uppercase tracking-[0.25em]">Portal7 × smbgen</span>
                </div>

                {{-- L7 wordmark area --}}
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0"
                         style="background: linear-gradient(135deg, rgba(251,191,36,0.2), rgba(251,191,36,0.05)); border: 1px solid rgba(251,191,36,0.3);">
                        <span class="text-amber-400 font-black text-xl tracking-tighter">L7</span>
                    </div>
                    <div>
                        <div class="text-white font-black text-lg tracking-wide">L7 Media Labs</div>
                        <div class="text-amber-500/70 text-xs font-medium tracking-widest uppercase">Atlanta, GA &mdash; Synthesizing Brand DNA</div>
                    </div>
                </div>

                <h1 class="text-5xl md:text-6xl font-black text-white leading-[1.05] tracking-tight mb-7">
                    Where brand DNA<br>
                    meets<br>
                    <span style="background: linear-gradient(135deg, #fbbf24, #f59e0b, #60a5fa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        intelligent delivery.
                    </span>
                </h1>

                <p class="text-gray-400 text-lg leading-relaxed mb-8 max-w-lg font-light">
                    L7 Media Labs engineers bold brand identities for forward-thinking businesses.
                    Portal7 — their dedicated smbgen instance — is the platform that amplifies that work
                    across every channel, every client, every campaign.
                </p>

                <p class="text-gray-500 text-sm leading-relaxed mb-10 max-w-lg font-light">
                    From social automation to lead capture, email sequences to client portals —
                    Portal7 gives L7's entire portfolio a unified operating layer so the creative team
                    can stay focused on what they do best: synthesizing brand DNA.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="https://www.l7medialabs.com" target="_blank" rel="noopener"
                       class="px-6 py-3 rounded-xl font-bold text-sm text-black transition-colors"
                       style="background: linear-gradient(135deg, #fbbf24, #f59e0b);">
                        Visit L7 Media Labs &rarr;
                    </a>
                    <a href="/contact"
                       class="px-6 py-3 rounded-xl font-bold text-sm text-gray-300 hover:text-white transition-colors"
                       style="border: 1px solid rgba(255,255,255,0.1);">
                        Get your own instance
                    </a>
                </div>
            </div>

            {{-- ── Right: Portal7 system visual ────────────────────────── --}}
            <div class="hidden lg:block">
                <div class="relative">

                    {{-- Main card --}}
                    <div class="rounded-3xl p-8 relative overflow-hidden"
                         style="background: rgba(255,255,255,0.02); border: 1px solid rgba(251,191,36,0.15);">

                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-7">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                                     style="background: linear-gradient(135deg, rgba(251,191,36,0.25), rgba(251,191,36,0.08)); border: 1px solid rgba(251,191,36,0.3);">
                                    <span class="text-amber-400 font-black text-sm">P7</span>
                                </div>
                                <div>
                                    <div class="text-white font-black text-sm">Portal7</div>
                                    <div class="text-amber-600 text-[10px] font-medium">powered by smbgen</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-400 uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                                Live
                            </div>
                        </div>

                        {{-- Module status rows --}}
                        <div class="space-y-2.5 mb-7">
                            @foreach([
                                ['SIGNAL',  'Social Automation',   '12 posts scheduled', '#c4b5fd', 'rgba(139,92,246,0.12)', 'rgba(139,92,246,0.25)'],
                                ['RELAY',   'Email Marketing',     '3 sequences active', '#67e8f9', 'rgba(6,182,212,0.12)',  'rgba(6,182,212,0.25)'],
                                ['SURGE',   'Lead Generation',     '48 new leads / mo',  '#fdba74', 'rgba(249,115,22,0.12)', 'rgba(249,115,22,0.25)'],
                                ['CAST',    'Web Design & Delivery','6 sites managed',   '#6ee7b7', 'rgba(16,185,129,0.12)', 'rgba(16,185,129,0.25)'],
                                ['VAULT',   'CRM & Client Portal',  '24 active clients', '#a5b4fc', 'rgba(99,102,241,0.12)', 'rgba(99,102,241,0.25)'],
                            ] as [$product, $label, $stat, $textColor, $bg, $border])
                                <div class="flex items-center justify-between rounded-xl px-4 py-3"
                                     style="background: {{ $bg }}; border: 1px solid {{ $border }};">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-black uppercase tracking-widest w-14" style="color: {{ $textColor }}">{{ $product }}</span>
                                        <span class="text-gray-400 text-xs">{{ $label }}</span>
                                    </div>
                                    <span class="text-xs font-semibold" style="color: {{ $textColor }}">{{ $stat }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Portfolio brands --}}
                        <div class="pt-5" style="border-top: 1px solid rgba(251,191,36,0.1);">
                            <div class="text-amber-700 text-[10px] font-black uppercase tracking-[0.2em] mb-3">Portfolio brands on Portal7</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Spatio', 'The Illustrated Live', 'Military Talent Pipeline', '+ more'] as $brand)
                                    <span class="text-[11px] font-semibold text-amber-300/70 px-3 py-1 rounded-lg"
                                          style="background: rgba(251,191,36,0.07); border: 1px solid rgba(251,191,36,0.15);">
                                        {{ $brand }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Floating AI tag --}}
                    <div class="absolute -top-4 -right-4 drift-slow px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest text-blue-300"
                         style="background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.25);">
                        AI-Native
                    </div>
                    <div class="absolute -bottom-4 -left-4 drift px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest text-amber-400"
                         style="background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.25);">
                        Atlanta, GA
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── HOW IT WORKS ──────────────────────────────────────────────────── --}}
<section class="bg-black py-24 px-6" style="border-top: 1px solid rgba(251,191,36,0.1);">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-amber-600 text-xs font-black uppercase tracking-[0.25em] mb-4 block">The Model</span>
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-5">
                L7 creates. Portal7 amplifies.
            </h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto font-light leading-relaxed">
                L7 Media Labs engineers the brand strategy, identity, and creative direction.
                Portal7 is the intelligent operating system that distributes, captures, and manages
                everything downstream — so the creative output actually drives growth.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">

            {{-- Step 1 --}}
            <div class="relative">
                <div class="l7-card rounded-2xl p-8 h-full hover:border-amber-400/25 transition-all">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-6 text-amber-400 font-black text-lg"
                         style="background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.2);">01</div>
                    <h3 class="text-white font-black text-xl mb-3 tracking-tight">Brand DNA is engineered</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        L7's creative team synthesizes the identity — logo systems, brand voice, visual language,
                        campaign strategy. The hard creative work that makes a brand unforgettable.
                    </p>
                    <div class="mt-6 pt-5 flex flex-wrap gap-2" style="border-top: 1px solid rgba(251,191,36,0.1);">
                        @foreach(['Brand Identity', 'Visual System', 'Campaign Strategy', 'Storytelling'] as $tag)
                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest px-2.5 py-1 rounded-lg"
                                  style="background: rgba(251,191,36,0.06);">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="hidden md:flex absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full items-center justify-center z-10 text-amber-400 text-xs"
                     style="background: rgba(251,191,36,0.15); border: 1px solid rgba(251,191,36,0.3);">&rarr;</div>
            </div>

            {{-- Step 2 --}}
            <div class="relative">
                <div class="l7-card rounded-2xl p-8 h-full hover:border-amber-400/25 transition-all">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-6 text-blue-400 font-black text-lg"
                         style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2);">02</div>
                    <h3 class="text-white font-black text-xl mb-3 tracking-tight">Portal7 distributes it</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        AI-generated social content, scheduled email sequences, SEO-optimised landing pages —
                        Portal7 takes the brand and pushes it out across every channel, consistently and at scale.
                    </p>
                    <div class="mt-6 pt-5 flex flex-wrap gap-2" style="border-top: 1px solid rgba(59,130,246,0.1);">
                        @foreach(['Social Auto', 'Email Sequences', 'SEO Content', 'Paid Campaigns'] as $tag)
                            <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest px-2.5 py-1 rounded-lg"
                                  style="background: rgba(59,130,246,0.08);">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="hidden md:flex absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full items-center justify-center z-10 text-amber-400 text-xs"
                     style="background: rgba(251,191,36,0.15); border: 1px solid rgba(251,191,36,0.3);">&rarr;</div>
            </div>

            {{-- Step 3 --}}
            <div>
                <div class="l7-card rounded-2xl p-8 h-full hover:border-amber-400/25 transition-all">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-6 text-emerald-400 font-black text-lg"
                         style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">03</div>
                    <h3 class="text-white font-black text-xl mb-3 tracking-tight">Leads become clients</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Capture forms, CRM pipelines, automated follow-ups, and secure client portals — Portal7
                        closes the loop from first impression to signed engagement, organised and on time.
                    </p>
                    <div class="mt-6 pt-5 flex flex-wrap gap-2" style="border-top: 1px solid rgba(16,185,129,0.1);">
                        @foreach(['Lead Capture', 'CRM Pipeline', 'Client Portals', 'File Delivery'] as $tag)
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest px-2.5 py-1 rounded-lg"
                                  style="background: rgba(16,185,129,0.08);">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── PORTFOLIO SECTION ─────────────────────────────────────────────── --}}
<section class="py-24 px-6" style="background: #06060f; border-top: 1px solid rgba(255,255,255,0.04);">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="text-amber-600 text-xs font-black uppercase tracking-[0.25em] mb-4 block">L7 Portfolio</span>
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-5">
                One platform. Every brand.
            </h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto font-light">
                Every brand L7 creates gets the full Portal7 stack — its own presence, pipeline, and performance layer.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">

            {{-- Spatio --}}
            <div class="rounded-2xl overflow-hidden transition-all hover:-translate-y-1 duration-200"
                 style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.07);">
                <div class="h-2" style="background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>
                <div class="p-7">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-amber-400 font-black text-sm"
                             style="background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.2);">SP</div>
                        <div>
                            <div class="text-white font-black text-base">Spatio</div>
                            <a href="https://www.l7medialabs.com/thevault-spatio" target="_blank" rel="noopener"
                               class="text-amber-600 text-[10px] hover:text-amber-400 transition-colors">l7medialabs.com &rarr;</a>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">
                        A spotlight brand in the L7 portfolio. Portal7 drives its social presence,
                        email reach, and prospective client journey end-to-end.
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['SIGNAL', 'RELAY', 'VAULT'] as $product)
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-400/70 px-2 py-1 rounded"
                                  style="background: rgba(251,191,36,0.07);">{{ $product }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- The Illustrated Live --}}
            <div class="rounded-2xl overflow-hidden transition-all hover:-translate-y-1 duration-200"
                 style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.07);">
                <div class="h-2" style="background: linear-gradient(90deg, #8b5cf6, #c084fc);"></div>
                <div class="p-7">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-violet-400 font-black text-sm"
                             style="background: rgba(139,92,246,0.1); border: 1px solid rgba(139,92,246,0.2);">TIL</div>
                        <div>
                            <div class="text-white font-black text-base">The Illustrated Live</div>
                            <a href="https://www.l7medialabs.com/thevault-theillustratedlive" target="_blank" rel="noopener"
                               class="text-violet-600 text-[10px] hover:text-violet-400 transition-colors">l7medialabs.com &rarr;</a>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">
                        Live illustration events — Portal7 manages social scheduling,
                        event lead capture, and post-event client follow-up sequences.
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['SIGNAL', 'SURGE', 'RELAY'] as $product)
                            <span class="text-[10px] font-black uppercase tracking-widest text-violet-400/70 px-2 py-1 rounded"
                                  style="background: rgba(139,92,246,0.07);">{{ $product }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Military Talent Pipeline --}}
            <div class="rounded-2xl overflow-hidden transition-all hover:-translate-y-1 duration-200"
                 style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.07);">
                <div class="h-2" style="background: linear-gradient(90deg, #0ea5e9, #38bdf8);"></div>
                <div class="p-7">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sky-400 font-black text-sm"
                             style="background: rgba(14,165,233,0.1); border: 1px solid rgba(14,165,233,0.2);">MTP</div>
                        <div>
                            <div class="text-white font-black text-base">Military Talent Pipeline</div>
                            <a href="https://www.l7medialabs.com/thevault-mtp" target="_blank" rel="noopener"
                               class="text-sky-600 text-[10px] hover:text-sky-400 transition-colors">l7medialabs.com &rarr;</a>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">
                        Connecting military talent to opportunity. Portal7 powers the lead funnel,
                        CRM, document management, and automated candidate outreach.
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['VAULT', 'SURGE', 'RELAY'] as $product)
                            <span class="text-[10px] font-black uppercase tracking-widest text-sky-400/70 px-2 py-1 rounded"
                                  style="background: rgba(14,165,233,0.07);">{{ $product }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── WHAT PORTAL7 GIVES L7 ─────────────────────────────────────────── --}}
<section class="bg-black py-24 px-6" style="border-top: 1px solid rgba(255,255,255,0.04);">
    <div class="max-w-6xl mx-auto">

        <div class="grid md:grid-cols-2 gap-16 items-center">

            <div>
                <span class="text-amber-600 text-xs font-black uppercase tracking-[0.25em] mb-5 block">What Portal7 unlocks</span>
                <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                    The operating layer<br>
                    <span class="text-amber-400">creative agencies need.</span>
                </h2>
                <p class="text-gray-400 text-lg leading-relaxed mb-10 font-light">
                    L7's creative output is world-class. Portal7 ensures that output doesn't stop at delivery —
                    it continues to generate signal, capture attention, and convert interest into long-term clients.
                </p>
                <div class="flex flex-col gap-4">
                    @foreach([
                        ['text-amber-400', 'bg-amber-600/10 border-amber-600/25', 'One dashboard for every brand', 'Manage Spatio, The Illustrated Live, MTP, and all future L7 brands from a single Portal7 instance.'],
                        ['text-blue-400',  'bg-blue-600/10 border-blue-600/25',   'AI that speaks every brand\'s voice', 'SIGNAL and RELAY generate content tuned to each brand\'s identity — not generic output.'],
                        ['text-violet-400','bg-violet-600/10 border-violet-600/25','Leads don\'t fall through cracks', 'VAULT\'s CRM and automated sequences keep every prospect warm from first touch to signed contract.'],
                        ['text-emerald-400','bg-emerald-600/10 border-emerald-600/25','Client delivery that impresses', 'Secure portals, document storage, and approval workflows make every engagement look polished.'],
                    ] as [$textColor, $bg, $title, $body])
                        <div class="flex items-start gap-4 rounded-xl p-4 border {{ $bg }}">
                            <span class="w-5 h-5 rounded flex items-center justify-center {{ $bg }} {{ $textColor }} text-xs shrink-0 mt-0.5 font-black border">&#10003;</span>
                            <div>
                                <div class="text-white font-bold text-sm mb-0.5">{{ $title }}</div>
                                <div class="text-gray-500 text-xs leading-relaxed">{{ $body }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Metrics panel --}}
            <div class="rounded-2xl p-8" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(251,191,36,0.12);">
                <div class="text-amber-700 text-[10px] font-black uppercase tracking-[0.2em] mb-6">Portal7 at a glance</div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    @foreach([
                        ['3', 'Portfolio Brands', 'text-amber-400', 'rgba(251,191,36,0.1)', 'rgba(251,191,36,0.2)'],
                        ['5', 'Active Modules', 'text-blue-400', 'rgba(59,130,246,0.1)', 'rgba(59,130,246,0.2)'],
                        ['100%', 'AI-Powered', 'text-violet-400', 'rgba(139,92,246,0.1)', 'rgba(139,92,246,0.2)'],
                        ['1', 'Unified Platform', 'text-emerald-400', 'rgba(16,185,129,0.1)', 'rgba(16,185,129,0.2)'],
                    ] as [$num, $label, $textColor, $bg, $border])
                        <div class="rounded-xl p-5 text-center"
                             style="background: {{ $bg }}; border: 1px solid {{ $border }};">
                            <div class="text-3xl font-black {{ $textColor }} mb-1">{{ $num }}</div>
                            <div class="text-gray-600 text-xs font-medium">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="rounded-xl p-5" style="background: rgba(251,191,36,0.04); border: 1px solid rgba(251,191,36,0.12);">
                    <div class="text-amber-700 text-[10px] font-black uppercase tracking-widest mb-3">Modules running on Portal7</div>
                    <div class="flex flex-col gap-2">
                        @foreach([
                            ['SIGNAL', 'Social Automation', '#c4b5fd'],
                            ['RELAY',  'Email Marketing',   '#67e8f9'],
                            ['SURGE',  'Lead Generation',   '#fdba74'],
                            ['CAST',   'Web Design',        '#6ee7b7'],
                            ['VAULT',  'CRM & Docs',        '#a5b4fc'],
                        ] as [$product, $label, $color])
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: {{ $color }};"></span>
                                    <span class="text-[10px] font-black uppercase tracking-widest" style="color: {{ $color }};">{{ $product }}</span>
                                </div>
                                <span class="text-gray-600 text-xs">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── QUOTE / BRAND STATEMENT ───────────────────────────────────────── --}}
<section class="py-24 px-6 relative overflow-hidden" style="background: #04040a; border-top: 1px solid rgba(251,191,36,0.08);">
    <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(circle at 1px 1px, #fbbf24 1px, transparent 0); background-size: 40px 40px;"></div>
    <div class="max-w-4xl mx-auto text-center relative">
        <div class="text-6xl text-amber-800/50 font-black leading-none mb-6">&ldquo;</div>
        <blockquote class="text-2xl md:text-3xl font-black text-white leading-tight tracking-tight mb-8 max-w-3xl mx-auto">
            We don&rsquo;t just build brands — we synthesize their DNA, creating identities that evolve,
            adapt, and lead.
        </blockquote>
        <div class="flex items-center justify-center gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-amber-400 font-black text-sm"
                 style="background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.25);">JD</div>
            <div class="text-left">
                <div class="text-white font-bold text-sm">James Du Rell</div>
                <div class="text-amber-700 text-xs">Founder &amp; Creative Director, L7 Media Labs</div>
            </div>
        </div>
        <p class="text-gray-600 text-sm mt-8 max-w-xl mx-auto leading-relaxed">
            Portal7 is the platform layer that makes that vision executable — connecting brand creation
            directly to distribution, capture, and client management.
        </p>
    </div>
</section>

{{-- ── CTA ────────────────────────────────────────────────────────────── --}}
<section class="py-28 px-6" style="background: #03030a;">
    <div class="max-w-5xl mx-auto">
        <div class="grid md:grid-cols-2 gap-6">

            {{-- CTA: Visit L7 --}}
            <div class="rounded-3xl p-10 relative overflow-hidden"
                 style="background: radial-gradient(ellipse at 0% 0%, rgba(251,191,36,0.12) 0%, transparent 60%), rgba(255,255,255,0.02); border: 1px solid rgba(251,191,36,0.2);">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 text-amber-400 font-black text-xl"
                     style="background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.25);">L7</div>
                <h3 class="text-2xl font-black text-white mb-3 tracking-tight">Work with L7 Media Labs</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-7">
                    Need a brand built from the ground up? L7&rsquo;s creative team is based in Atlanta
                    and works with founders and businesses at every stage.
                </p>
                <a href="https://www.l7medialabs.com/contact-1" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm text-black transition-all"
                   style="background: linear-gradient(135deg, #fbbf24, #f59e0b);">
                    Get a quote from L7 &rarr;
                </a>
            </div>

            {{-- CTA: Your own Portal7-style instance --}}
            <div class="rounded-3xl p-10 relative overflow-hidden"
                 style="background: radial-gradient(ellipse at 100% 0%, rgba(59,130,246,0.12) 0%, transparent 60%), rgba(255,255,255,0.02); border: 1px solid rgba(59,130,246,0.2);">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6"
                     style="background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.25);">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-white mb-3 tracking-tight">Get your own smbgen instance</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-7">
                    Like what you see? Every agency and brand can have their own Portal7-style
                    instance — named, configured, and AI-powered for their specific operation.
                </p>
                <a href="/contact"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm text-white bg-blue-600 hover:bg-blue-500 transition-colors">
                    Talk to us &rarr;
                </a>
            </div>

        </div>
    </div>
</section>

@endsection
