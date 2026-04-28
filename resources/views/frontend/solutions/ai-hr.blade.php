@extends('layouts.frontend')

@php
    $bookHref = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
@endphp

@section('title', 'HR AI Assistant — Context-Aware Hiring and People Ops | smbgen')
@section('description', 'Custom HR AI assistant built with your policy docs, onboarding playbooks, role scorecards, and people-process standards.')

@section('content')
<section class="bg-[#0e0713] py-24 px-6">
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('solutions.ai') }}" class="inline-flex items-center gap-2 text-rose-300 text-sm font-semibold mb-8 hover:text-rose-200 transition-colors">&larr; Back to AI Solutions</a>

        <div class="rounded-3xl border border-rose-500/20 bg-gradient-to-br from-rose-500/10 to-purple-500/10 p-10">
            <p class="text-rose-300 text-xs font-black uppercase tracking-[0.2em] mb-3">HR Assistant</p>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-5">Your people-ops knowledge base, now conversational.</h1>
            <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-3xl">The HR assistant answers policy questions, supports manager workflows, and helps structure hiring and onboarding decisions using your own context, not generic internet answers.</p>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="rounded-2xl border border-white/10 bg-black/20 p-6">
                    <p class="text-white font-bold mb-3">What it handles</p>
                    <div class="flex flex-col gap-2 text-sm text-gray-300">
                        <span>Policy Q and A with source-aware responses</span>
                        <span>Role scorecard and interview question generation</span>
                        <span>Onboarding checklists by department</span>
                        <span>Manager response drafts for common scenarios</span>
                    </div>
                </div>
                <div class="rounded-2xl border border-white/10 bg-black/20 p-6">
                    <p class="text-white font-bold mb-3">Context we load</p>
                    <div class="flex flex-col gap-2 text-sm text-gray-300">
                        <span>Employee handbook and policy docs</span>
                        <span>Comp philosophy and leveling framework</span>
                        <span>Onboarding SOPs and compliance workflows</span>
                        <span>Company values and culture guardrails</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ $bookHref }}?intent=ai-hr" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-sm transition-colors">Plan HR assistant rollout &rarr;</a>
                <a href="{{ route('contact') }}?topic=ai-hr" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">Ask HR use-case questions</a>
            </div>
        </div>
    </div>
</section>
@endsection
