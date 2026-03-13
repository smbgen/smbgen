@extends('layouts.extreme')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-tight text-white">Dashboard</h1>
            <p class="text-gray-600 text-sm mt-1">{{ $tier?->label() ?? 'Free' }} plan</p>
        </div>
        <a href="{{ route('extreme.demo') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-700 hover:bg-red-600 text-white font-bold uppercase tracking-wide text-sm transition-all border border-red-600/40 shadow-lg shadow-red-900/30">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            New Generation
        </a>
    </div>

    {{-- Quota bar (Starter only) --}}
    @if($monthlyLimit !== null)
    <div class="mb-8 p-4 rounded-2xl bg-white/[0.03] border border-white/[0.07]">
        <div class="flex items-center justify-between mb-2">
            <span class="text-gray-400 text-sm">Generations this month</span>
            <span class="text-white text-sm font-semibold">{{ $usedThisMonth }} / {{ $monthlyLimit }}</span>
        </div>
        <div class="h-1.5 rounded-full bg-white/5 overflow-hidden">
            <div class="h-full rounded-full bg-red-600 transition-all"
                 style="width: {{ min(100, ($usedThisMonth / $monthlyLimit) * 100) }}%"></div>
        </div>
        @if($usedThisMonth >= $monthlyLimit)
        <p class="text-red-400 text-xs mt-2">Limit reached. <a href="{{ route('cleanslate.billing.plans') }}" class="underline">Upgrade to Pro</a> for unlimited generations.</p>
        @endif
    </div>
    @endif

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Generations list --}}
    @if($generations->isEmpty())
    <div class="text-center py-24 rounded-2xl bg-white/[0.02] border border-white/[0.05]">
        <svg class="w-10 h-10 text-gray-700 mx-auto mb-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
        </svg>
        <p class="text-gray-500 text-sm mb-6">No apps generated yet.</p>
        <a href="{{ route('extreme.demo') }}"
           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-red-700 hover:bg-red-600 text-white font-bold uppercase tracking-wide text-sm transition-all border border-red-600/40">
            Build your first app
        </a>
    </div>
    @else
    <div class="space-y-3">
        @foreach($generations as $gen)
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/[0.07] flex items-start justify-between gap-6">
            <div class="min-w-0">
                <p class="text-white text-sm font-medium truncate mb-1">{{ Str::limit($gen->prompt, 100) }}</p>
                <div class="flex items-center gap-3 text-xs text-gray-600">
                    <span>{{ $gen->created_at->diffForHumans() }}</span>
                    @if($gen->file_count > 0)
                    <span>·</span>
                    <span>{{ $gen->file_count }} files</span>
                    @endif
                    @if($gen->test_count > 0)
                    <span>·</span>
                    <span>{{ $gen->test_count }} tests</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <span class="px-2 py-0.5 rounded-md text-xs font-medium border
                    @if($gen->status === 'complete') bg-green-500/10 border-green-500/20 text-green-400
                    @elseif($gen->status === 'generating') bg-red-500/10 border-red-500/20 text-red-400
                    @elseif($gen->status === 'failed') bg-red-900/20 border-red-900/30 text-red-600
                    @else bg-white/5 border-white/10 text-gray-500
                    @endif">
                    {{ ucfirst($gen->status) }}
                </span>
                @if($gen->zip_path)
                <a href="{{ route('cleanslate.generation.download', $gen) }}"
                   class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 hover:text-white text-xs transition-colors">
                    Download
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
