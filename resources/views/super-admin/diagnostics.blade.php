@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Diagnostics'],
    ];
@endphp

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-white">Diagnostics</h1>
        <p class="text-sm text-gray-400 mt-1">Inspect and repair the tenancy infrastructure.</p>
    </div>

    {{-- Status overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-900 border {{ $diagnostics['tenancy_enabled'] ? 'border-green-800' : 'border-gray-800' }} rounded-xl p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-400">Tenancy Enabled</p>
                @if ($diagnostics['tenancy_enabled'])
                    <span class="w-2.5 h-2.5 bg-green-400 rounded-full"></span>
                @else
                    <span class="w-2.5 h-2.5 bg-gray-600 rounded-full"></span>
                @endif
            </div>
            <p class="text-lg font-medium mt-2 {{ $diagnostics['tenancy_enabled'] ? 'text-green-400' : 'text-gray-500' }}">
                {{ $diagnostics['tenancy_enabled'] ? 'Yes' : 'No' }}
            </p>
        </div>
        <div class="bg-gray-900 border {{ $diagnostics['tenants_table_exists'] ? 'border-green-800' : 'border-red-800' }} rounded-xl p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-400">Tenants Table</p>
                @if ($diagnostics['tenants_table_exists'])
                    <span class="w-2.5 h-2.5 bg-green-400 rounded-full"></span>
                @else
                    <span class="w-2.5 h-2.5 bg-red-400 rounded-full"></span>
                @endif
            </div>
            <p class="text-lg font-medium mt-2 {{ $diagnostics['tenants_table_exists'] ? 'text-green-400' : 'text-red-400' }}">
                {{ $diagnostics['tenants_table_exists'] ? 'Exists' : 'Missing' }}
            </p>
        </div>
        <div class="bg-gray-900 border {{ $diagnostics['domains_table_exists'] ? 'border-green-800' : 'border-red-800' }} rounded-xl p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-400">Domains Table</p>
                @if ($diagnostics['domains_table_exists'])
                    <span class="w-2.5 h-2.5 bg-green-400 rounded-full"></span>
                @else
                    <span class="w-2.5 h-2.5 bg-red-400 rounded-full"></span>
                @endif
            </div>
            <p class="text-lg font-medium mt-2 {{ $diagnostics['domains_table_exists'] ? 'text-green-400' : 'text-red-400' }}">
                {{ $diagnostics['domains_table_exists'] ? 'Exists' : 'Missing' }}
            </p>
        </div>
    </div>

    {{-- Key metrics --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider mb-4">Platform Metrics</h3>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
            <div>
                <dt class="text-gray-500">Total Tenants</dt>
                <dd class="text-2xl font-semibold text-white mt-0.5">{{ $diagnostics['tenant_count'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Active Tenants</dt>
                <dd class="text-2xl font-semibold text-green-400 mt-0.5">{{ $diagnostics['active_tenants'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Total Domains</dt>
                <dd class="text-2xl font-semibold text-white mt-0.5">{{ $diagnostics['domain_count'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Total Users</dt>
                <dd class="text-2xl font-semibold text-white mt-0.5">{{ $diagnostics['user_count'] }}</dd>
            </div>
        </dl>
    </div>

    {{-- System info --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider mb-4">System Information</h3>
        <dl class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Database Driver</dt>
                <dd class="text-gray-200 mt-0.5 font-mono text-xs">{{ $diagnostics['database_driver'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">PHP Version</dt>
                <dd class="text-gray-200 mt-0.5 font-mono text-xs">{{ $diagnostics['php_version'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Laravel Version</dt>
                <dd class="text-gray-200 mt-0.5 font-mono text-xs">{{ $diagnostics['laravel_version'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">App Environment</dt>
                <dd class="text-gray-200 mt-0.5 font-mono text-xs">{{ $diagnostics['app_env'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tenancy Resolver</dt>
                <dd class="text-gray-200 mt-0.5 font-mono text-xs">{{ $diagnostics['tenancy_resolver'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Queue Driver</dt>
                <dd class="text-gray-200 mt-0.5 font-mono text-xs">{{ $diagnostics['queue_driver'] }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Cache Driver</dt>
                <dd class="text-gray-200 mt-0.5 font-mono text-xs">{{ $diagnostics['cache_driver'] }}</dd>
            </div>
        </dl>
    </div>

    {{-- Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Run Migrations</h3>
            <p class="text-xs text-gray-500">Run <code class="text-gray-400">php artisan migrate</code> to ensure all tables are up to date.</p>
            <form method="POST" action="{{ route('super-admin.diagnostics.run-migrations') }}">
                @csrf
                <button type="submit" onclick="return confirm('Run php artisan migrate now?')"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-900/40 hover:bg-indigo-900/60 border border-indigo-800 text-indigo-300 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-database"></i> Run Migrations
                </button>
            </form>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Tenant Migrations</h3>
            <p class="text-xs text-gray-500">Run migrations for all tenant databases.</p>
            <form method="POST" action="{{ route('super-admin.diagnostics.run-tenant-migrations') }}">
                @csrf
                <button type="submit" onclick="return confirm('Run tenant migrations for all tenants?')"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-900/40 hover:bg-purple-900/60 border border-purple-800 text-purple-300 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-layer-group"></i> Run Tenant Migrations
                </button>
            </form>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Clear Caches</h3>
            <p class="text-xs text-gray-500">Clear config, route, view, and application caches.</p>
            <form method="POST" action="{{ route('super-admin.diagnostics.clear-caches') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-900/40 hover:bg-amber-900/60 border border-amber-800 text-amber-300 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-broom"></i> Clear Caches
                </button>
            </form>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Create Master Tenant</h3>
            <p class="text-xs text-gray-500">Create the central tenant record to represent this installation.</p>
            <form method="POST" action="{{ route('super-admin.diagnostics.create-master-tenant') }}">
                @csrf
                <button type="submit" onclick="return confirm('Create master tenant record?')"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-700 hover:bg-gray-600 border border-gray-600 text-gray-300 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-crown"></i> Create Master Tenant
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
