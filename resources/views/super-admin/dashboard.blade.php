@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard'],
    ];
@endphp

@section('content')
<div class="max-w-7xl mx-auto py-6 space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-400">Platform Control</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Super Admin Console</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-400">Operate smbgen as a deployment platform: assign super admins, control tenancy, choose the primary frontend stack, and manage module topology.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('super-admin.guided-setup') }}" class="inline-flex items-center rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 hover:bg-slate-800 transition-colors">Guided Setup</a>
            <a href="{{ route('super-admin.deployment-console') }}" class="inline-flex items-center rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-cyan-400 transition-colors">Deployment Console</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><div class="text-sm text-slate-500">Deployment</div><div class="mt-2 text-2xl font-semibold text-white">{{ $deploymentName }}</div></div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><div class="text-sm text-slate-500">Environment</div><div class="mt-2 text-2xl font-semibold text-white">{{ strtoupper($deploymentEnvironment) }}</div></div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><div class="text-sm text-slate-500">Super Admins</div><div class="mt-2 text-2xl font-semibold text-white">{{ $superAdminCount }}</div></div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><div class="text-sm text-slate-500">Company Admins</div><div class="mt-2 text-2xl font-semibold text-white">{{ $administratorCount }}</div></div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h3 class="admin-card-title">Tenant Accounts</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Overview of all tenant accounts on this platform.</p>
            </div>
            <a href="{{ route('super-admin.tenants.index') }}" class="btn-secondary text-sm">Manage Tenants</a>
        </div>
        <div class="admin-card-body">
            @php
                $tenantsTableExists = false;
                try {
                    $tenantsTableExists = \Illuminate\Support\Facades\Schema::hasTable('tenants');
                } catch (\Throwable $e) {}
            @endphp
            @if ($tenantsTableExists)
                @php
                    $tenantStats = [
                        'total' => \App\Models\Tenant::query()->count(),
                        'active' => \App\Models\Tenant::query()->where('is_active', true)->count(),
                        'trial' => \App\Models\Tenant::query()->where('plan', 'trial')->count(),
                    ];
                @endphp
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $tenantStats['total'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Tenants</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $tenantStats['active'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Active</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                        <div class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $tenantStats['trial'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">On Trial</div>
                    </div>
                </div>
            @else
                <div class="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                        <p class="text-sm text-amber-700 dark:text-amber-300">Tenancy tables not found. Run migrations to enable multi-tenancy.</p>
                    </div>
                    <a href="{{ route('super-admin.diagnostics') }}" class="text-sm text-amber-600 dark:text-amber-400 hover:underline font-medium">Diagnostics →</a>
                </div>
            @endif
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h3 class="admin-card-title">Module Topology</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Current module footprint for this smbgen deployment.</p>
            </div>
        </div>
        <div class="admin-card-body space-y-4">
            @foreach($modules as $module)
                <div class="flex flex-col gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $module['name'] }}</h4>
                            @if($module['core'])
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">Core</span>
                            @endif
                            @if($module['selected_frontend'])
                                <span class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Primary Frontend</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $module['description'] }}</p>
                    </div>
                    <span class="rounded px-2.5 py-1 text-xs font-medium {{ $module['enabled'] ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">{{ $module['enabled'] ? 'Enabled' : 'Disabled' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
