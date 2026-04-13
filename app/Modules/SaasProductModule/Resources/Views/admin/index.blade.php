@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">SaaS Product Module — Admin</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">All customers and removal activity</p>
    </div>
    <a href="{{ route('admin.saasproductmodule.brokers') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 rounded-lg text-white text-xs font-medium transition-all">
        <i class="fas fa-database text-[10px]"></i> Manage Brokers
    </a>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium bg-green-500/10 text-green-400 border border-green-500/20">
        {{ session('success') }}
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 text-center">
        <p class="text-2xl font-extrabold text-white">{{ $stats['total_customers'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Customers</p>
    </div>
    <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 text-center">
        <p class="text-2xl font-extrabold text-blue-400">{{ $stats['active_scans'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Active Scans</p>
    </div>
    <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 text-center">
        <p class="text-2xl font-extrabold text-yellow-400">{{ $stats['pending_removals'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Pending Removals</p>
    </div>
    <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 text-center">
        <p class="text-2xl font-extrabold text-green-400">{{ $stats['confirmed_removals'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Confirmed Removals</p>
    </div>
</div>

{{-- Customer Table --}}
<div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-700">
        <h2 class="text-sm font-semibold text-white">Customers</h2>
    </div>

    @if($profiles->isEmpty())
        <div class="px-5 py-10 text-center text-gray-500 text-sm">No customers yet.</div>
    @else
        <div class="divide-y divide-gray-700/50">
            @foreach($profiles as $profile)
            <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-700/30 transition-colors">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-white truncate">{{ $profile->fullName() }}</p>
                        @if(! $profile->onboarding_complete)
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-yellow-500/10 text-yellow-400">Setup incomplete</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 truncate">{{ $profile->user?->email }}</p>
                </div>
                <div class="flex items-center gap-5 shrink-0 ml-4">
                    <div class="text-center hidden sm:block">
                        <p class="text-sm font-semibold text-white">{{ $profile->scanJobs->count() }}</p>
                        <p class="text-[10px] text-gray-500">scans</p>
                    </div>
                    <div class="text-center hidden sm:block">
                        <p class="text-sm font-semibold {{ $profile->removalRequests->where('status.value', 'pending')->count() > 0 ? 'text-yellow-400' : 'text-white' }}">
                            {{ $profile->removalRequests->count() }}
                        </p>
                        <p class="text-[10px] text-gray-500">removals</p>
                    </div>
                    <div class="text-center hidden sm:block">
                        <p class="text-sm font-semibold {{ ($profile->exposure_score ?? 0) > 50 ? 'text-red-400' : 'text-green-400' }}">
                            {{ $profile->exposure_score ?? '—' }}
                        </p>
                        <p class="text-[10px] text-gray-500">exposure</p>
                    </div>
                    <a href="{{ route('admin.saasproductmodule.customers.show', $profile) }}" class="text-xs text-primary-400 hover:text-primary-300 font-medium transition-colors">
                        View <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-3 border-t border-gray-700">
            {{ $profiles->links() }}
        </div>
    @endif
</div>
@endsection
