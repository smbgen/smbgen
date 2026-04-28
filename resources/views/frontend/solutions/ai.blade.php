@extends('layouts.frontend')

@php
    $bookHref = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
@endphp

@section('title', 'AI Solutions — Context-Aware Assistants for SMB Teams | smbgen')
@section('description', 'Dedicated context-aware AI assistants for HR, business development, and go-to-market execution. Custom assistants built around your business workflows, voice, and operating data.')

@push('head')
<style>
    .ai-hero-bg {
        background:
            radial-gradient(ellipse at 70% -10%, rgba(217,70,239,0.16) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(14,165,233,0.12) 0%, transparent 50%),
            #050712;
    }
    .ai-card-hover {
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }
    .ai-card-hover:hover {
        transform: translateY(-3px);
    }
</style>
@endpush

@section('content')

<section class="ai-hero-bg py-28 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-fuchsia-500/30 bg-fuchsia-500/10 text-fuchsia-300 text-xs font-bold uppercase tracking-widest mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-fuchsia-400 animate-pulse inline-block"></span>
                AI Solutions
            </div>

            <h1 class="text-4xl md:text-6xl font-black text-white leading-[1.05] tracking-tight mb-6">
                Dedicated context-aware AI assistants
                <span class="text-fuchsia-400">for your core teams.</span>
            </h1>

            <p class="text-lg md:text-xl text-gray-300 leading-relaxed mb-8 max-w-2xl">
                We build custom AI chat assistants trained on your business context: your processes, playbooks, messaging, offers, and internal standards. Your team gets faster answers with better decisions, without generic AI noise.
            </p>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ $bookHref }}?intent=ai-solutions" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-500 text-white font-bold text-sm transition-colors shadow-lg shadow-fuchsia-900/30">
                    Book AI planning call &rarr;
                </a>
                <a href="{{ route('contact') }}?topic=ai-assistants" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                    Ask about implementation
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#060a18] py-20 px-6 border-y border-white/5">
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-3 gap-6">
            <a href="{{ route('solutions.ai.hr') }}" class="ai-card-hover rounded-2xl border border-white/10 bg-white/5 p-7 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-500/15 border border-rose-500/25 flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-rose-300 mb-2">HR Assistant</p>
                    <h2 class="text-white font-black text-2xl tracking-tight mb-2">Policy and people operations copilot</h2>
                    <p class="text-gray-400 text-sm leading-relaxed">Support hiring, onboarding, policy Q and A, role definitions, and manager playbooks with assistant responses grounded in your actual HR documentation.</p>
                </div>
                <span class="text-rose-300 text-sm font-semibold mt-auto">View HR assistant &rarr;</span>
            </a>

            <a href="{{ route('solutions.ai.biz-dev') }}" class="ai-card-hover rounded-2xl border border-white/10 bg-white/5 p-7 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/15 border border-indigo-500/25 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-indigo-300 mb-2">Biz Dev Assistant</p>
                    <h2 class="text-white font-black text-2xl tracking-tight mb-2">Pipeline and outreach copilot</h2>
                    <p class="text-gray-400 text-sm leading-relaxed">Generate account briefs, personalize outreach, prioritize opportunities, and map next actions using your ICP, offer positioning, and deal-stage rules.</p>
                </div>
                <span class="text-indigo-300 text-sm font-semibold mt-auto">View Biz Dev assistant &rarr;</span>
            </a>

            <a href="{{ route('solutions.ai.gtm') }}" class="ai-card-hover rounded-2xl border border-white/10 bg-white/5 p-7 flex flex-col gap-4">
                <div class="w-12 h-12 rounded-xl bg-cyan-500/15 border border-cyan-500/25 flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-300 mb-2">GTM Assistant</p>
                    <h2 class="text-white font-black text-2xl tracking-tight mb-2">Campaign and launch copilot</h2>
                    <p class="text-gray-400 text-sm leading-relaxed">Plan launches, shape messaging, build channel plans, and generate tactical execution checklists aligned to your market, audience, and revenue goals.</p>
                </div>
                <span class="text-cyan-300 text-sm font-semibold mt-auto">View GTM assistant &rarr;</span>
            </a>
        </div>
    </div>
</section>

<section class="bg-[#050712] py-20 px-6">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-4">Context is the product advantage.</h2>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-8">These assistants are not generic chatbots. We tune them to your business language, internal playbooks, and workflows so they drive real execution quality.</p>
        <a href="{{ $bookHref }}?intent=context-aware-ai" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-500 text-white font-bold text-sm transition-colors shadow-lg shadow-fuchsia-900/30">
            Scope your AI assistant rollout &rarr;
        </a>
    </div>
</section>

@endsection
