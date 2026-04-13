@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Tenants'],
    ];
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Tenants</h1>
            <p class="text-sm text-gray-400 mt-1">Manage all tenant accounts on the platform.</p>
        </div>
        <a href="{{ route('super-admin.tenants.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fas fa-plus"></i>
            New Tenant
        </a>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
            <p class="text-3xl font-semibold text-white mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Active</p>
            <p class="text-3xl font-semibold text-green-400 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">On Trial</p>
            <p class="text-3xl font-semibold text-amber-400 mt-1">{{ $stats['trial'] }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Suspended</p>
            <p class="text-3xl font-semibold text-red-400 mt-1">{{ $stats['suspended'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name, email, subdomain..."
                   class="flex-1 min-w-48 bg-gray-800 border border-gray-700 text-gray-200 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
            <select name="plan" class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                <option value="">All Plans</option>
                <option value="trial" @selected(request('plan') === 'trial')>Trial</option>
                <option value="starter" @selected(request('plan') === 'starter')>Starter</option>
                <option value="professional" @selected(request('plan') === 'professional')>Professional</option>
                <option value="enterprise" @selected(request('plan') === 'enterprise')>Enterprise</option>
            </select>
            <select name="status" class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                <option value="">All Statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'plan', 'status']))
                <a href="{{ route('super-admin.tenants.index') }}" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        @if ($tenants->isEmpty())
            <div class="py-16 text-center">
                <i class="fas fa-building text-gray-700 text-4xl mb-3"></i>
                <p class="text-gray-400">No tenants found.</p>
                <a href="{{ route('super-admin.tenants.create') }}" class="mt-3 inline-block text-indigo-400 hover:text-indigo-300 text-sm">Create your first tenant</a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Trial Ends</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach ($tenants as $tenant)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-white">{{ $tenant->name }}</p>
                                    <p class="text-gray-500 text-xs mt-0.5">{{ $tenant->subdomain }}.{{ config('app.domain', 'smbgen.app') }}</p>
                                    <p class="text-gray-500 text-xs">{{ $tenant->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $planColors = [
                                        'trial' => 'bg-gray-700 text-gray-300',
                                        'starter' => 'bg-blue-900/50 text-blue-300',
                                        'professional' => 'bg-purple-900/50 text-purple-300',
                                        'enterprise' => 'bg-amber-900/50 text-amber-300',
                                    ];
                                @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $planColors[$tenant->plan] ?? 'bg-gray-700 text-gray-300' }}">
                                    {{ ucfirst($tenant->plan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($tenant->is_active)
                                    <span class="inline-flex items-center gap-1 text-green-400 text-xs"><span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Active</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-red-400 text-xs"><span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span> Suspended</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">
                                @if ($tenant->trial_ends_at)
                                    <span class="{{ $tenant->isTrialExpired() ? 'text-red-400' : 'text-amber-400' }}">
                                        {{ $tenant->trial_ends_at->format('M j, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">
                                {{ $tenant->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('super-admin.tenants.show', $tenant) }}"
                                       class="text-indigo-400 hover:text-indigo-300 text-xs transition-colors">View</a>
                                    <a href="{{ route('super-admin.tenants.edit', $tenant) }}"
                                       class="text-gray-400 hover:text-white text-xs transition-colors">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($tenants->hasPages())
                <div class="px-6 py-4 border-t border-gray-800">
                    {{ $tenants->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
