@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Deployment Console'],
    ];
@endphp

@section('content')
<div class="max-w-6xl mx-auto py-6 space-y-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Deployment Console</h1>
            <p class="admin-page-subtitle">Choose which module owns the public site and which product stacks should be live.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('super-admin.deployment-console.update') }}" class="space-y-6">
        @csrf
        @method('PATCH')

        <div class="admin-card">
            <div class="admin-card-header"><h3 class="admin-card-title">Deployment Profile</h3></div>
            <div class="admin-card-body grid gap-6 md:grid-cols-2">
                <div class="form-group">
                    <label class="form-label" for="deployment_name">Deployment Name</label>
                    <input class="form-input" id="deployment_name" name="deployment_name" value="{{ old('deployment_name', $deploymentName) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="deployment_domain">Primary Domain</label>
                    <input class="form-input" id="deployment_domain" name="deployment_domain" value="{{ old('deployment_domain', $deploymentDomain) }}">
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label" for="deployment_environment">Environment</label>
                    <select class="form-input" id="deployment_environment" name="deployment_environment">
                        @foreach(['local', 'staging', 'production'] as $environment)
                            <option value="{{ $environment }}" @selected(old('deployment_environment', $deploymentEnvironment) === $environment)>{{ ucfirst($environment) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h3 class="admin-card-title">Primary Frontend Module</h3></div>
            <div class="admin-card-body space-y-3">
                @foreach($frontendOptions as $module)
                    <label class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <input type="radio" name="frontend_module" value="{{ $module['key'] }}" class="mt-1" @checked(old('frontend_module', $selectedFrontend) === $module['key'])>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $module['name'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $module['description'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h3 class="admin-card-title">Enabled Modules</h3></div>
            <div class="admin-card-body space-y-3">
                @php($enabledModuleKeys = old('enabled_modules', collect($modules)->where('enabled', true)->pluck('key')->all()))
                @foreach($modules as $module)
                    <label class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <input type="checkbox" name="enabled_modules[]" value="{{ $module['key'] }}" class="mt-1" @checked(in_array($module['key'], $enabledModuleKeys, true))>
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $module['name'] }}</div>
                                @if($module['core'])
                                    <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">Core</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $module['description'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Save Deployment Console</button>
        </div>
    </form>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h3 class="admin-card-title">User Management Queue</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Recently logged-in users across all tenants, sorted from latest to oldest.</p>
            </div>
        </div>
        <div class="admin-card-body space-y-4">
            @if($recentlyLoggedInUsers->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Tenant</th>
                                <th>Last Login</th>
                                <th>Assign Tenant</th>
                                <th class="text-right">Super Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentlyLoggedInUsers as $user)
                                <tr>
                                    <td>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </td>
                                    <td>
                                        @if($user->isSuperAdmin())
                                            <span class="rounded bg-purple-100 px-2 py-0.5 text-xs text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">Super Admin</span>
                                        @elseif(in_array($user->role, [\App\Models\User::ROLE_ADMINISTRATOR, \App\Models\User::ROLE_ADMINISTRATOR_LEGACY], true))
                                            <span class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Company Admin</span>
                                        @else
                                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $user->role }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->tenant)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $user->tenant->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->tenant->subdomain ?? 'n/a' }}</div>
                                        @elseif($user->tenant_id)
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $user->tenant_id }}</span>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Central / None</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->last_logged_in_at)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($user->last_logged_in_at)->format('M j, Y g:i A') }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Carbon::parse($user->last_logged_in_at)->diffForHumans() }}</div>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tenants->isNotEmpty())
                                            <form method="POST" action="{{ route('super-admin.users.tenant', $user) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="tenant_id" class="admin-input text-xs py-1 px-2">
                                                    <option value="">— None —</option>
                                                    @foreach($tenants as $tenant)
                                                        <option value="{{ $tenant->id }}" @selected($user->tenant_id === $tenant->id)>
                                                            {{ $tenant->name }}{{ $tenant->subdomain ? ' ('.$tenant->subdomain.')' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <select name="role" class="admin-input text-xs py-1 px-2">
                                                    <option value="{{ \App\Models\User::ROLE_ADMINISTRATOR }}" @selected($user->role === \App\Models\User::ROLE_ADMINISTRATOR)>Company Admin</option>
                                                    <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}" @selected($user->role === \App\Models\User::ROLE_TENANT_ADMIN)>Tenant User</option>
                                                </select>
                                                <button type="submit" class="btn-secondary text-xs whitespace-nowrap">Assign</button>
                                            </form>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">No tenants</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('super-admin.users.super-admin', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_super_admin" value="{{ $user->isSuperAdmin() ? '0' : '1' }}">
                                            <button type="submit" class="btn-secondary text-xs">{{ $user->isSuperAdmin() ? 'Remove' : 'Promote' }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-sm text-gray-600 dark:text-gray-400">
                    No login activity found yet. This queue will populate after users sign in.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection