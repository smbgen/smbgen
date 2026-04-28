@extends('layouts.frontend')

@php
    $bookHref = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
@endphp

@section('title', 'Biz Dev AI Assistant — Context-Aware Pipeline Support | smbgen')
@section('description', 'Custom business development AI assistant for account research, outreach planning, deal progression, and opportunity prioritization using your sales context.')

@section('content')
<section class="bg-[#070b15] py-24 px-6">
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('solutions.ai') }}" class="inline-flex items-center gap-2 text-indigo-300 text-sm font-semibold mb-8 hover:text-indigo-200 transition-colors">&larr; Back to AI Solutions</a>

        <div class="rounded-3xl border border-indigo-500/20 bg-gradient-to-br from-indigo-500/10 to-blue-500/10 p-10">
            <p class="text-indigo-300 text-xs font-black uppercase tracking-[0.2em] mb-3">Biz Dev Assistant</p>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-5">Turn scattered prospecting into a repeatable pipeline engine.</h1>
            <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-3xl">The Biz Dev assistant helps your team identify ideal accounts, draft better outreach, and execute stage-by-stage follow-up with context from your offer, ICP, and deal process.</p>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="rounded-2xl border border-white/10 bg-black/20 p-6">
                    <p class="text-white font-bold mb-3">What it handles</p>
                    <div class="flex flex-col gap-2 text-sm text-gray-300">
                        <span>Target account briefs and relevance notes</span>
                        <span>Outbound email and DM draft variations</span>
                        <span>Discovery prep and objection playbooks</span>
                        <span>Deal stage next-best-action recommendations</span>
                    </div>
                </div>
                <div class="rounded-2xl border border-white/10 bg-black/20 p-6">
                    <p class="text-white font-bold mb-3">Context we load</p>
                    <div class="flex flex-col gap-2 text-sm text-gray-300">
                        <span>ICP definitions and qualification criteria</span>
                        <span>Offer positioning and pricing narratives</span>
                        <span>Past win-loss notes and objection history</span>
                        <span>Pipeline stage definitions and handoffs</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ $bookHref }}?intent=ai-biz-dev" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition-colors">Plan Biz Dev assistant rollout &rarr;</a>
                <a href="{{ route('contact') }}?topic=ai-biz-dev" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">Ask Biz Dev use-case questions</a>
            </div>
        </div>
    </div>
</section>
@endsection
