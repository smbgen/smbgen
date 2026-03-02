@extends('layouts.super-admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Platform Overview</h1>
    <p class="text-gray-400">Monitor and manage all tenants across the platform</p>
</div>

<!-- Master Tenant Management -->
<div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-white text-xl font-bold mb-2">Master Tenant Management</h3>
            <p class="text-purple-100 mb-0">Manage smbgen.com marketing site, CMS, and content</p>
        </div>
        <a href="{{ route('super-admin.manage-master-tenant') }}" 
           class="inline-flex items-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-purple-50 transition-all hover:scale-105 shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Manage Master Tenant Site
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('super-admin.diagnostics') }}" class="bg-gradient-to-br from-red-600 to-red-700 rounded-lg p-6 hover:shadow-xl transition-all duration-200 group">
        <div class="flex items-center justify-between mb-4">
            <i class="fas fa-stethoscope text-white text-3xl"></i>
            <i class="fas fa-arrow-right text-white/50 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-1">Diagnostics</h3>
        <p class="text-red-200 text-sm">Debug & setup tenancy</p>
    </a>

    <a href="{{ route('super-admin.tenants.index') }}" class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg p-6 hover:shadow-xl transition-all duration-200 group">
        <div class="flex items-center justify-between mb-4">
            <i class="fas fa-building text-white text-3xl"></i>
            <i class="fas fa-arrow-right text-white/50 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-1">View All Tenants</h3>
        <p class="text-blue-200 text-sm">Manage all organizations</p>
    </a>

    <a href="{{ config('services.stripe.dashboard_url', 'https://dashboard.stripe.com') }}" target="_blank" class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-lg p-6 hover:shadow-xl transition-all duration-200 group">
        <div class="flex items-center justify-between mb-4">
            <i class="fab fa-stripe text-white text-3xl"></i>
            <i class="fas fa-external-link-alt text-white/50 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-1">Stripe Dashboard</h3>
        <p class="text-purple-200 text-sm">Billing & payments</p>
    </a>

    <a href="#trials" class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-lg p-6 hover:shadow-xl transition-all duration-200 group">
        <div class="flex items-center justify-between mb-4">
            <i class="fas fa-clock text-white text-3xl"></i>
            <i class="fas fa-arrow-down text-white/50 group-hover:text-white group-hover:translate-y-1 transition-all"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-1">Expiring Trials</h3>
        <p class="text-yellow-200 text-sm">Action needed soon</p>
    </a>

    <a href="#health" class="bg-gradient-to-br from-green-600 to-green-700 rounded-lg p-6 hover:shadow-xl transition-all duration-200 group">
        <div class="flex items-center justify-between mb-4">
            <i class="fas fa-heartbeat text-white text-3xl"></i>
            <i class="fas fa-arrow-down text-white/50 group-hover:text-white group-hover:translate-y-1 transition-all"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-1">Platform Health</h3>
        <p class="text-green-200 text-sm">System monitoring</p>
    </a>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="text-gray-400 text-sm mb-2">Total Tenants</div>
            <div class="text-3xl font-bold text-white">{{ $stats['total_tenants'] }}</div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="text-gray-400 text-sm mb-2">Active Trials</div>
            <div class="text-3xl font-bold text-blue-400">{{ $stats['active_trials'] }}</div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="text-gray-400 text-sm mb-2">Paying Customers</div>
            <div class="text-3xl font-bold text-green-400">{{ $stats['paying_customers'] }}</div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="text-gray-400 text-sm mb-2">Monthly Revenue</div>
            <div class="text-3xl font-bold text-emerald-400">${{ number_format($stats['revenue_mrr'], 0) }}</div>
        </div>
    </div>

    <!-- Expiring Trials Section -->
    <div id="trials" class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    Trials Expiring Soon
                </h2>
                <p class="text-gray-400 text-sm mt-1">Tenants with trials ending in the next 7 days</p>
            </div>
        </div>
        
        @php
            $expiringTrials = \App\Models\Tenant::whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '>', now())
                ->where('trial_ends_at', '<=', now()->addDays(7))
                ->orderBy('trial_ends_at', 'asc')
                ->get();
        @endphp

        @if($expiringTrials->count() > 0)
            <div class="space-y-3">
                @foreach($expiringTrials as $tenant)
                    <div class="flex items-center justify-between bg-gray-900 p-4 rounded-lg hover:bg-gray-900/70 transition-colors">
                        <div class="flex-1">
                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="text-white font-medium hover:text-blue-400">
                                {{ $tenant->name }}
                            </a>
                            <div class="text-gray-400 text-sm">{{ $tenant->email }}</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-yellow-400 font-medium text-sm">
                                    @if($tenant->trial_ends_at)
                                        {{ \Carbon\Carbon::parse($tenant->trial_ends_at)->diffForHumans() }}
                                    @endif
                                </div>
                                <div class="text-gray-500 text-xs">
                                    @if($tenant->trial_ends_at)
                                        {{ \Carbon\Carbon::parse($tenant->trial_ends_at)->format('M j, Y') }}
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" 
                               class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors">
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-check-circle text-green-400 text-3xl mb-2"></i>
                <p>No trials expiring in the next 7 days</p>
            </div>
        @endif
    </div>

    <!-- Platform Health Section -->
    <div id="health" class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-8">
        <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-heartbeat text-green-400"></i>
            Platform Health
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-900 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-400 text-sm">Database</span>
                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                </div>
                <div class="text-white font-semibold">Operational</div>
            </div>
            
            <div class="bg-gray-900 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-400 text-sm">Queue System</span>
                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                </div>
                <div class="text-white font-semibold">Active</div>
            </div>
            
            <div class="bg-gray-900 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-400 text-sm">Storage</span>
                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                </div>
                <div class="text-white font-semibold">{{ round(disk_free_space('/') / disk_total_space('/') * 100) }}% Free</div>
            </div>
        </div>
    </div>

    <!-- Recent Tenants -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-white">Recent Signups</h2>
            <a href="{{ route('super-admin.tenants.index') }}" class="text-blue-400 hover:text-blue-300 text-sm">View All →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gray-700">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-400 font-medium text-sm">Company</th>
                        <th class="text-left py-3 px-4 text-gray-400 font-medium text-sm">Email</th>
                        <th class="text-left py-3 px-4 text-gray-400 font-medium text-sm">Plan</th>
                        <th class="text-left py-3 px-4 text-gray-400 font-medium text-sm">Trial Ends</th>
                        <th class="text-left py-3 px-4 text-gray-400 font-medium text-sm">Status</th>
                        <th class="text-right py-3 px-4 text-gray-400 font-medium text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTenants as $tenant)
                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/30">
                        <td class="py-3 px-4 text-white font-medium">{{ $tenant->name }}</td>
                        <td class="py-3 px-4 text-gray-300">{{ $tenant->email }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-blue-600/20 text-blue-400 rounded text-xs font-medium">{{ ucfirst($tenant->plan) }}</span>
                        </td>
                        <td class="py-3 px-4 text-gray-300 text-sm">
                            @if($tenant->trial_ends_at)
                                {{ \Carbon\Carbon::parse($tenant->trial_ends_at)->format('M j, Y') }}
                                <span class="text-gray-500">({{ \Carbon\Carbon::parse($tenant->trial_ends_at)->diffForHumans() }})</span>
                            @else
                                <span class="text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($tenant->is_active)
                                <span class="px-2 py-1 bg-green-600/20 text-green-400 rounded text-xs font-medium">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-600/20 text-red-400 rounded text-xs font-medium">Suspended</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                View Details →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">
                            No tenants found. Wait for trial signups to appear here.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
