@extends('layouts.saas-product-module')

@section('title', 'Setup — Confirm')

@section('content')
<div class="max-w-lg mx-auto px-6 py-16">

    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-white mb-1">SaaS Product Module Setup</h1>
        <p class="text-sm text-gray-500">Step 4 of 4 — Confirm &amp; Launch</p>
    </div>

    {{-- Progress bar --}}
    <div class="flex gap-2 mb-10">
        @foreach(['Profile', 'Contact', 'Addresses', 'Confirm'] as $i => $step)
        <div class="flex-1">
            <div class="h-1 rounded-full bg-cyan-500"></div>
            <p class="text-xs mt-1.5 {{ $i === 3 ? 'text-cyan-400 font-semibold' : 'text-gray-400' }}">{{ $step }}</p>
        </div>
        @endforeach
    </div>

    <div class="space-y-4 mb-10">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Identity</h3>
            <p class="text-white font-semibold">{{ $profile->fullName() }}</p>
            <p class="text-gray-400 text-sm">{{ $profile->date_of_birth?->format('F j, Y') }}</p>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Contact</h3>
            @foreach($profile->emails ?? [] as $email)
                <p class="text-white text-sm">{{ $email }}</p>
            @endforeach
            @foreach($profile->phones ?? [] as $phone)
                <p class="text-gray-400 text-sm">{{ $phone }}</p>
            @endforeach
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Addresses</h3>
            @foreach($profile->addresses ?? [] as $addr)
                <p class="text-white text-sm">{{ $addr['street'] }}, {{ $addr['city'] }}, {{ $addr['state'] }} {{ $addr['zip'] }}</p>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-5">
        <a href="{{ route('saasproductmodule.onboarding.addresses') }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Edit
        </a>

        <form action="{{ route('saasproductmodule.onboarding.launch') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-7 py-3 bg-gradient-to-r from-cyan-500 to-violet-500 hover:from-cyan-400 hover:to-violet-400 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-500/20 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.82m5.84-2.56a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.82m2.56-5.84a14.98 14.98 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg>
                Launch Scan
            </button>
        </form>
    </div>
</div>
@endsection
