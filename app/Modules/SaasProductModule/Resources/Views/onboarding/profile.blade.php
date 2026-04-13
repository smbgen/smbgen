@extends('layouts.saas-product-module')

@section('title', 'Setup — Step 1')

@section('content')
<div class="max-w-lg mx-auto px-6 py-16">

    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-white mb-1">SaaS Product Module Setup</h1>
        <p class="text-sm text-gray-500">Step 1 of 4 — Your Profile</p>
    </div>

    {{-- Progress bar --}}
    <div class="flex gap-2 mb-10">
        @foreach(['Profile', 'Contact', 'Addresses', 'Confirm'] as $i => $step)
        <div class="flex-1">
            <div class="h-1 rounded-full {{ $i === 0 ? 'bg-cyan-500' : 'bg-white/10' }}"></div>
            <p class="text-xs mt-1.5 {{ $i === 0 ? 'text-cyan-400 font-semibold' : 'text-gray-600' }}">{{ $step }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <form action="{{ route('saasproductmodule.onboarding.profile') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $profile->first_name ?? '') }}"
                    class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors" required>
                @error('first_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $profile->last_name ?? '') }}"
                    class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors" required>
                @error('last_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d') ?? '') }}"
                    class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 transition-colors [color-scheme:dark]" required>
                @error('date_of_birth') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-cyan-500 hover:bg-cyan-400 text-white font-semibold rounded-lg text-sm transition-all">
                    Next <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
