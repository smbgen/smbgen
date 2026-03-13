@extends('layouts.extreme')

@section('title', 'Welcome to Extreme')

@section('content')
<div class="max-w-2xl mx-auto px-6 py-20 text-center">

    <div class="relative w-16 h-16 mx-auto mb-8">
        <div class="absolute inset-0 rounded-2xl bg-red-600 opacity-25 blur-md"></div>
        <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-red-600 to-red-900 border border-red-500/40 flex items-center justify-center shadow-xl shadow-red-900/50">
            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
        </div>
    </div>

    <h1 class="text-4xl font-black uppercase tracking-tight text-white mb-3">
        Welcome, {{ $user->name }}
    </h1>
    <p class="text-gray-500 text-base mb-10 max-w-md mx-auto">
        Your subscription is active. You're ready to start generating full-stack apps.
    </p>

    <form action="{{ route('cleanslate.onboarding.profile') }}" method="POST">
        @csrf
        <button type="submit"
            class="inline-flex items-center gap-3 px-12 py-4 rounded-xl bg-red-700 hover:bg-red-600 text-white font-black uppercase tracking-widest text-lg transition-all shadow-xl shadow-red-900/50 border border-red-600/40">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            Start Building
        </button>
    </form>

</div>
@endsection
