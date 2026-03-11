@extends('layouts.clean-slate')

@section('title', 'Setup — Step 3')

@section('content')
<div class="max-w-lg mx-auto px-6 py-16">

    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-white mb-1">Clean Slate Setup</h1>
        <p class="text-sm text-gray-500">Step 3 of 4 — Your Addresses</p>
    </div>

    {{-- Progress bar --}}
    <div class="flex gap-2 mb-10">
        @foreach(['Profile', 'Contact', 'Addresses', 'Confirm'] as $i => $step)
        <div class="flex-1">
            <div class="h-1 rounded-full {{ $i <= 2 ? 'bg-cyan-500' : 'bg-white/10' }}"></div>
            <p class="text-xs mt-1.5 {{ $i === 2 ? 'text-cyan-400 font-semibold' : ($i < 2 ? 'text-gray-400' : 'text-gray-600') }}">{{ $step }}</p>
        </div>
        @endforeach
    </div>

    <p class="text-sm text-gray-500 mb-5">
        Add current and past addresses — more coverage means better results.
    </p>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <form action="{{ route('cleanslate.onboarding.addresses') }}" method="POST" class="space-y-5">
            @csrf

            @php $addresses = old('addresses', $profile->addresses ?? [['street'=>'','city'=>'','state'=>'','zip'=>'']]); @endphp

            @foreach ($addresses as $i => $address)
            <div class="space-y-3">
                @if($i > 0)
                <div class="border-t border-white/10 pt-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Previous Address {{ $i }}</p>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Street Address</label>
                    <input type="text" name="addresses[{{ $i }}][street]" value="{{ $address['street'] ?? '' }}"
                        placeholder="123 Main St" required
                        class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors">
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-1">
                        <label class="block text-xs font-medium text-gray-400 mb-1">City</label>
                        <input type="text" name="addresses[{{ $i }}][city]" value="{{ $address['city'] ?? '' }}"
                            placeholder="City" required
                            class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">State</label>
                        <input type="text" name="addresses[{{ $i }}][state]" value="{{ $address['state'] ?? '' }}"
                            placeholder="MD" maxlength="2" required
                            class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">ZIP</label>
                        <input type="text" name="addresses[{{ $i }}][zip]" value="{{ $address['zip'] ?? '' }}"
                            placeholder="20815" required
                            class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors">
                    </div>
                </div>
            </div>
            @endforeach

            @error('addresses') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror

            <div class="pt-2 flex items-center gap-4">
                <a href="{{ route('cleanslate.onboarding.contact') }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Back
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-cyan-500 hover:bg-cyan-400 text-white font-semibold rounded-lg text-sm transition-all">
                    Next <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
