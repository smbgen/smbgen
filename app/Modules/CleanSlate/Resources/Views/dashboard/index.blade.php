@extends('layouts.clean-slate')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-12">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-10">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Your Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">
                Plan: <span class="text-cyan-400 font-semibold">{{ $tier?->label() ?? '—' }}</span>
            </p>
        </div>
        <a href="{{ route('cleanslate.billing.plans') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-white/10 hover:border-white/20 bg-white/5 text-gray-300 hover:text-white text-xs font-medium transition-all">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
            Manage Plan
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Exposure Score</p>
            <p class="text-5xl font-extrabold {{ ($profile?->exposure_score ?? 0) > 50 ? 'text-red-400' : 'text-cyan-400' }}">
                {{ $profile?->exposure_score ?? 0 }}
            </p>
            <p class="text-xs text-gray-500 mt-1.5">{{ ($profile?->exposure_score ?? 0) > 50 ? 'High exposure' : 'Low exposure' }}</p>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Brokers Scanned</p>
            <p class="text-5xl font-extrabold text-white">{{ $scanJobs->count() }}</p>
            <p class="text-xs text-gray-500 mt-1.5">{{ $scanJobs->where('status.value', 'completed')->count() }} completed</p>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Removals</p>
            <p class="text-5xl font-extrabold text-white">{{ $removalRequests->count() }}</p>
            <p class="text-xs text-gray-500 mt-1.5">{{ $removalRequests->where('status.value', 'confirmed')->count() }} confirmed</p>
        </div>
    </div>

    {{-- Scan Results --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-white">Scan Results</h2>
            <span class="text-xs text-gray-500">{{ $scanJobs->count() }} brokers</span>
        </div>

        @if($scanJobs->isEmpty())
            <div class="px-6 py-12 text-center text-gray-600 text-sm">
                <svg class="w-8 h-8 mx-auto mb-3 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z" /></svg>
                No scans run yet — results will appear here shortly after launch.
            </div>
        @else
            <div class="divide-y divide-white/5">
                @foreach($scanJobs as $job)
                <div class="flex items-center justify-between px-6 py-3.5">
                    <div>
                        <p class="text-sm font-medium text-white">{{ $job->dataBroker->name }}</p>
                        <p class="text-xs text-gray-600">{{ $job->dataBroker->domain }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        @if($job->listings_found > 0)
                            <span class="text-xs font-semibold text-red-400">{{ $job->listings_found }} listing{{ $job->listings_found !== 1 ? 's' : '' }}</span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $job->status->value === 'completed' ? 'bg-cyan-500/10 text-cyan-400' :
                               ($job->status->value === 'running' ? 'bg-violet-500/10 text-violet-400' :
                               ($job->status->value === 'failed' ? 'bg-red-500/10 text-red-400' : 'bg-white/5 text-gray-500')) }}">
                            {{ ucfirst($job->status->value) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Removal Requests --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-white">Removal Requests</h2>
            <span class="text-xs text-gray-500">{{ $removalRequests->count() }} total</span>
        </div>

        @if($removalRequests->isEmpty())
            <div class="px-6 py-12 text-center text-gray-600 text-sm">
                <svg class="w-8 h-8 mx-auto mb-3 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.211-.211.498-.33.796-.33H19.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25h-9.284c-.298 0-.585-.119-.796-.33z" /></svg>
                Removal requests will appear here once listings are found.
            </div>
        @else
            <div class="divide-y divide-white/5">
                @foreach($removalRequests as $req)
                <div class="flex items-center justify-between px-6 py-3.5">
                    <div>
                        <p class="text-sm font-medium text-white">{{ $req->dataBroker->name }}</p>
                        <p class="text-xs text-gray-600">{{ $req->submitted_at?->diffForHumans() ?? 'Not yet submitted' }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $req->status->value === 'confirmed' ? 'bg-cyan-500/10 text-cyan-400' :
                           ($req->status->value === 'submitted' ? 'bg-violet-500/10 text-violet-400' :
                           ($req->status->value === 'failed' ? 'bg-red-500/10 text-red-400' : 'bg-white/5 text-gray-500')) }}">
                        {{ ucfirst($req->status->value) }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
