@extends('layouts.frontend')

@php
    $bookHref = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
@endphp

@section('title', 'GTM AI Assistant — Context-Aware Go-to-Market Execution | smbgen')
@section('description', 'Custom GTM AI assistant for campaign strategy, launch planning, messaging architecture, and channel execution aligned to your market context.')

@section('content')
<section class="bg-[#061016] py-24 px-6">
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('solutions.ai') }}" class="inline-flex items-center gap-2 text-cyan-300 text-sm font-semibold mb-8 hover:text-cyan-200 transition-colors">&larr; Back to AI Solutions</a>

        <div class="rounded-3xl border border-cyan-500/20 bg-gradient-to-br from-cyan-500/10 to-teal-500/10 p-10">
            <p class="text-cyan-300 text-xs font-black uppercase tracking-[0.2em] mb-3">GTM Assistant</p>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-5">Ship better campaigns with tighter go-to-market execution.</h1>
            <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-3xl">The GTM assistant helps your team move from ideas to launch-ready plans with messaging frameworks, channel sequencing, campaign assets, and execution checklists grounded in your strategy.</p>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="rounded-2xl border border-white/10 bg-black/20 p-6">
                    <p class="text-white font-bold mb-3">What it handles</p>
                    <div class="flex flex-col gap-2 text-sm text-gray-300">
                        <span>Campaign brief and launch plan generation</span>
                        <span>Audience-specific messaging frameworks</span>
                        <span>Channel plan sequencing and timing</span>
                        <span>Asset checklist and execution timelines</span>
                    </div>
                </div>
                <div class="rounded-2xl border border-white/10 bg-black/20 p-6">
                    <p class="text-white font-bold mb-3">Context we load</p>
                    <div class="flex flex-col gap-2 text-sm text-gray-300">
                        <span>Positioning docs and value propositions</span>
                        <span>Target personas and segment priorities</span>
                        <span>Channel performance and historical learnings</span>
                        <span>Revenue goals and launch milestones</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ $bookHref }}?intent=ai-gtm" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-sm transition-colors">Plan GTM assistant rollout &rarr;</a>
                <a href="{{ route('contact') }}?topic=ai-gtm" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">Ask GTM use-case questions</a>
            </div>
        </div>
    </div>
</section>
@endsection
