@extends('layouts.clean-slate')

@section('title', 'Choose a Plan')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-16" x-data="{ selected: 'professional' }">

    <div class="text-center mb-12">
        <h1 class="text-3xl font-extrabold text-white mb-3">Choose Your Plan</h1>
        <p class="text-gray-400 text-sm max-w-md mx-auto">Select the coverage level that's right for you. All plans include a personal opt-out specialist.</p>
    </div>

    <form action="{{ route('cleanslate.billing.checkout') }}" method="POST">
        @csrf

        @error('tier')
            <p class="text-red-400 text-sm mb-6 text-center">{{ $message }}</p>
        @enderror

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            @foreach ($tiers as $tier)
            <label
                class="relative cursor-pointer"
                @click="selected = '{{ $tier->value }}'">

                <input type="radio" name="tier" value="{{ $tier->value }}"
                    x-bind:checked="selected === '{{ $tier->value }}'"
                    class="sr-only">

                <div class="h-full p-6 rounded-2xl border transition-all relative"
                    :class="selected === '{{ $tier->value }}'
                        ? 'border-cyan-500 bg-cyan-500/5'
                        : 'border-white/10 bg-white/5 hover:border-white/20'">

                    @if($tier->value === 'professional')
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-cyan-500 to-violet-500 text-white">Most Popular</span>
                        </div>
                    @endif

                    <div class="mb-5">
                        <h3 class="text-lg font-bold text-white mb-1">{{ $tier->label() }}</h3>
                        <div>
                            <span class="text-4xl font-extrabold text-white">${{ number_format($tier->priceMonthly() / 100, 0) }}</span>
                            <span class="text-gray-500 text-sm">/mo</span>
                        </div>
                    </div>

                    <ul class="space-y-2.5 text-sm text-gray-400 mb-6">
                        @if($tier->value === 'basic')
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> 18 top data brokers</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Monthly scans</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Web form opt-outs</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Email support</li>
                        @elseif($tier->value === 'professional')
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> 24 data brokers</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Weekly scans</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Email opt-outs included</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Priority support</li>
                        @else
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> All 25 brokers</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Continuous monitoring</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Manual removals</li>
                            <li class="flex items-center gap-2"><span class="text-cyan-400 text-xs">✓</span> Dedicated specialist</li>
                        @endif
                    </ul>

                    {{-- Selection indicator --}}
                    <div class="w-5 h-5 rounded-full border-2 transition-colors flex items-center justify-center mx-auto"
                        :class="selected === '{{ $tier->value }}' ? 'border-cyan-500' : 'border-gray-600'">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-500 transition-opacity"
                            :class="selected === '{{ $tier->value }}' ? 'opacity-100' : 'opacity-0'"></div>
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        <div class="text-center">
            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3.5 bg-cyan-500 hover:bg-cyan-400 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-500/20 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                Subscribe with Stripe
            </button>
            <p class="text-gray-600 text-xs mt-3">Cancel anytime. No long-term commitment.</p>
        </div>
    </form>
</div>
@endsection
