@extends('layouts.extreme')

@section('title', 'Plans & Pricing')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-16">

    <div class="text-center mb-12">
        <div class="flex items-center justify-center gap-2 mb-3">
            <a href="{{ route('extreme') }}" class="text-red-900 hover:text-red-600 text-xs font-mono transition-colors">← extreme</a>
            <span class="text-red-900/50 text-xs">/</span>
            <span class="text-red-700/70 text-xs font-mono">plans</span>
        </div>
        <p class="text-red-500 text-sm font-medium uppercase tracking-widest mb-3">Pricing</p>
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Simple, honest pricing</h1>
        <p class="text-gray-500 max-w-xl mx-auto text-sm">Cancel anytime. Every plan includes full source code, no watermarks, no code expiry.</p>
    </div>

    <form action="{{ route('cleanslate.billing.checkout') }}" method="POST" x-data="{ selected: 'professional' }">
        @csrf

        @error('tier')
            <p class="text-red-400 text-sm mb-6 text-center">{{ $message }}</p>
        @enderror

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            @foreach ($tiers as $tier)
            <label class="relative cursor-pointer" @click="selected = '{{ $tier->value }}'">

                <input type="radio" name="tier" value="{{ $tier->value }}"
                    x-bind:checked="selected === '{{ $tier->value }}'"
                    class="sr-only">

                <div class="h-full p-8 rounded-2xl border transition-all relative"
                    :class="selected === '{{ $tier->value }}'
                        ? 'border-red-600/60 bg-red-600/5'
                        : 'border-white/[0.08] bg-white/[0.03] hover:border-white/20'">

                    @if($tier->value === 'professional')
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-700 border border-red-600/40 text-white">Most Popular</span>
                        </div>
                    @endif

                    <div class="mb-5">
                        <p class="text-gray-400 text-sm font-medium mb-2">{{ $tier->label() }}</p>
                        <div class="flex items-end gap-2 mb-1">
                            <span class="text-5xl font-bold text-white">${{ number_format($tier->priceMonthly() / 100, 0) }}</span>
                            <span class="text-gray-500 mb-2">/mo</span>
                        </div>
                    </div>

                    <ul class="space-y-2.5 text-sm text-gray-400 mb-6">
                        @if($tier->value === 'basic')
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Up to 3 generations/mo</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Full source code download</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Standard full-stack</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Basic integrations (auth, DB)</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Community support</li>
                        @elseif($tier->value === 'professional')
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Unlimited app generations</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Repo push on generation</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Full integration library</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Multi-tenancy scaffolding</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Billing, OAuth, queues</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Priority support</li>
                        @else
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Everything in Pro</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> 5 team member seats</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> White-label output</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Custom tech stack presets</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> One-click deploy integration</li>
                            <li class="flex items-center gap-2"><span class="text-red-400 text-xs">✓</span> Dedicated support channel</li>
                        @endif
                    </ul>

                    {{-- Selection indicator --}}
                    <div class="w-5 h-5 rounded-full border-2 transition-colors flex items-center justify-center mx-auto"
                        :class="selected === '{{ $tier->value }}' ? 'border-red-500' : 'border-gray-600'">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500 transition-opacity"
                            :class="selected === '{{ $tier->value }}' ? 'opacity-100' : 'opacity-0'"></div>
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        <div class="text-center">
            <button type="submit"
                class="inline-flex items-center gap-3 px-10 py-4 rounded-xl bg-red-700 hover:bg-red-600 text-white font-black uppercase tracking-widest text-sm transition-all shadow-xl shadow-red-900/50 border border-red-600/40">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                Get Started
            </button>
            <p class="text-gray-600 text-xs mt-4">Cancel anytime &nbsp;·&nbsp; Billed monthly &nbsp;·&nbsp; All code is yours, no royalties</p>
        </div>
    </form>

</div>
@endsection
