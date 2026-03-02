@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Tenants']
    ];
@endphp

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">Tenant Management</h1>
        <p class="text-gray-400">View and manage all organizations on the platform</p>
    </div>
    @if(\Illuminate\Support\Facades\Route::has('super-admin.tenants.create'))
        <a href="{{ route('super-admin.tenants.create') }}" 
            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Create New Tenant
        </a>
    @endif
</div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-6">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search by company, email, subdomain..."
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="plan" class="px-4 py-2 bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Plans</option>
                    <option value="trial" {{ request('plan') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter</option>
                    <option value="professional" {{ request('plan') === 'professional' ? 'selected' : '' }}>Professional</option>
                    <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
            </div>
            <div>
                <select name="status" class="px-4 py-2 bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                Filter
            </button>
            @if(request()->hasAny(['search', 'plan', 'status']))
                <a href="{{ route('super-admin.tenants.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Tenants Table -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900 border-b border-gray-700">
                    <tr>
                        <th class="text-left py-4 px-4 text-gray-400 font-medium text-sm">Company</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-medium text-sm">Email</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-medium text-sm">Subdomain</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-medium text-sm">Plan</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-medium text-sm">Trial Ends</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-medium text-sm">Status</th>
                        <th class="text-right py-4 px-4 text-gray-400 font-medium text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $tenant)
                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/30">
                        <td class="py-4 px-4 text-white font-medium">{{ $tenant->name }}</td>
                        <td class="py-4 px-4 text-gray-300">{{ $tenant->email }}</td>
                        <td class="py-4 px-4 text-gray-300 font-mono text-sm">{{ $tenant->subdomain }}</td>
                        <td class="py-4 px-4">
                            <span class="px-2 py-1 bg-blue-600/20 text-blue-400 rounded text-xs font-medium">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-gray-300 text-sm">
                            @if($tenant->trial_ends_at)
                                {{ $tenant->trial_ends_at->format('M j, Y') }}
                            @else
                                <span class="text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($tenant->is_active)
                                <span class="px-2 py-1 bg-green-600/20 text-green-400 rounded text-xs font-medium">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-600/20 text-red-400 rounded text-xs font-medium">Suspended</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(\Illuminate\Support\Facades\Route::has('super-admin.tenants.edit'))
                                    <a href="{{ route('super-admin.tenants.edit', $tenant) }}" 
                                       class="text-blue-400 hover:text-blue-300 text-sm font-medium" 
                                       title="Edit tenant">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                <a href="{{ route('super-admin.tenants.show', $tenant) }}" 
                                   class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                    View →
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            No tenants found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tenants->hasPages())
        <div class="px-6 py-4 bg-gray-900 border-t border-gray-700">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
