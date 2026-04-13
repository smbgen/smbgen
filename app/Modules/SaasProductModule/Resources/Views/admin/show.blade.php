@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.saasproductmodule.index') }}" class="text-gray-500 hover:text-gray-300 transition-colors text-sm">
        <i class="fas fa-arrow-left text-xs"></i> All Customers
    </a>
    <span class="text-gray-700">/</span>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $profile->fullName() }}</h1>
    @if(! $profile->onboarding_complete)
        <span class="px-2 py-0.5 rounded text-xs font-medium bg-yellow-500/10 text-yellow-400">Setup incomplete</span>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Identity --}}
    <div class="space-y-4">
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Identity</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Name</dt>
                    <dd class="text-white font-medium">{{ $profile->fullName() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">DOB</dt>
                    <dd class="text-white">{{ $profile->date_of_birth?->format('M j, Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Account</dt>
                    <dd class="text-white truncate ml-4">{{ $profile->user?->email }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Customer since</dt>
                    <dd class="text-white">{{ $profile->created_at->format('M j, Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Exposure score</dt>
                    <dd class="font-bold {{ ($profile->exposure_score ?? 0) > 50 ? 'text-red-400' : 'text-green-400' }}">
                        {{ $profile->exposure_score ?? 0 }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Contact</h3>
            @forelse($profile->emails ?? [] as $email)
                <p class="text-white text-sm">{{ $email }}</p>
            @empty
                <p class="text-gray-600 text-sm">No emails on file</p>
            @endforelse
            @foreach($profile->phones ?? [] as $phone)
                <p class="text-gray-400 text-sm mt-1">{{ $phone }}</p>
            @endforeach
        </div>

        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Addresses</h3>
            @forelse($profile->addresses ?? [] as $addr)
                <p class="text-white text-sm leading-relaxed">{{ $addr['street'] }}, {{ $addr['city'] }}, {{ $addr['state'] }} {{ $addr['zip'] }}</p>
            @empty
                <p class="text-gray-600 text-sm">No addresses on file</p>
            @endforelse
        </div>
    </div>

    {{-- Right: Scans + Removals --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Scan Jobs --}}
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white">Scan Results</h2>
                <span class="text-xs text-gray-500">{{ $profile->scanJobs->count() }} brokers</span>
            </div>
            @if($profile->scanJobs->isEmpty())
                <div class="px-5 py-8 text-center text-gray-500 text-sm">No scans run yet.</div>
            @else
                <div class="divide-y divide-gray-700/50">
                    @foreach($profile->scanJobs as $job)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $job->dataBroker->name }}</p>
                            <p class="text-xs text-gray-500">{{ $job->dataBroker->domain }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            @if($job->listings_found > 0)
                                <span class="text-xs font-semibold text-red-400">{{ $job->listings_found }} found</span>
                            @endif
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $job->status->value === 'completed' ? 'bg-green-500/10 text-green-400' :
                                   ($job->status->value === 'running'   ? 'bg-blue-500/10 text-blue-400' :
                                   ($job->status->value === 'failed'    ? 'bg-red-500/10 text-red-400' : 'bg-gray-700 text-gray-400')) }}">
                                {{ ucfirst($job->status->value) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Removal Requests --}}
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white">Removal Requests</h2>
                <span class="text-xs text-gray-500">{{ $profile->removalRequests->count() }} total</span>
            </div>
            @if($profile->removalRequests->isEmpty())
                <div class="px-5 py-8 text-center text-gray-500 text-sm">No removal requests yet.</div>
            @else
                <div class="divide-y divide-gray-700/50">
                    @foreach($profile->removalRequests as $req)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $req->dataBroker->name }}</p>
                            <p class="text-xs text-gray-500">{{ $req->submitted_at?->format('M j, Y') ?? 'Not yet submitted' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $req->status->value === 'confirmed'  ? 'bg-green-500/10 text-green-400' :
                               ($req->status->value === 'submitted' ? 'bg-blue-500/10 text-blue-400' :
                               ($req->status->value === 'failed'    ? 'bg-red-500/10 text-red-400' : 'bg-gray-700 text-gray-400')) }}">
                            {{ ucfirst($req->status->value) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
