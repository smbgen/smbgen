@extends('layouts.tenant')

@section('breadcrumb', $module)

@section('content')
<div class="max-w-2xl mx-auto text-center py-20">
    <div class="text-6xl mb-6">{{ $icon }}</div>
    <h1 class="text-3xl font-bold text-white mb-3">{{ $module }}</h1>
    <p class="text-gray-400 text-lg mb-8">This module is active on your account. Full management UI coming soon.</p>
    <a href="{{ route('tenant.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
        ← Back to Dashboard
    </a>
</div>
@endsection
