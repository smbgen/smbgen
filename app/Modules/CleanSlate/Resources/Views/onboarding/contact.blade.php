@extends('layouts.extreme')

@section('title', 'Setup — Step 2')

@section('content')
<div class="max-w-lg mx-auto px-6 py-16">

    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-white mb-1">Extreme Setup</h1>
        <p class="text-sm text-gray-500">Step 2 of 4 — Contact Information</p>
    </div>

    {{-- Progress bar --}}
    <div class="flex gap-2 mb-10">
        @foreach(['Profile', 'Contact', 'Addresses', 'Confirm'] as $i => $step)
        <div class="flex-1">
            <div class="h-1 rounded-full {{ $i <= 1 ? 'bg-cyan-500' : 'bg-white/10' }}"></div>
            <p class="text-xs mt-1.5 {{ $i === 1 ? 'text-cyan-400 font-semibold' : ($i < 1 ? 'text-gray-400' : 'text-gray-600') }}">{{ $step }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <form action="{{ route('cleanslate.onboarding.contact') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Email Addresses</label>
                <p class="text-xs text-gray-500 mb-3">Add all emails associated with your name for maximum coverage.</p>
                <div class="space-y-2">
                    <input type="email" name="emails[]" value="{{ old('emails.0', ($profile->emails[0] ?? '')) }}"
                        placeholder="Primary email" required
                        class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors">
                    <input type="email" name="emails[]" value="{{ old('emails.1', ($profile->emails[1] ?? '')) }}"
                        placeholder="Additional email (optional)"
                        class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors">
                </div>
                @error('emails') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">
                    Phone Numbers <span class="text-gray-500 font-normal">(optional)</span>
                </label>
                <input type="tel" name="phones[]" value="{{ old('phones.0', ($profile->phones[0] ?? '')) }}"
                    placeholder="e.g. 555-867-5309"
                    class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors">
            </div>

            <div class="pt-2 flex items-center gap-4">
                <a href="{{ route('cleanslate.onboarding.profile') }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors flex items-center gap-1.5">
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
