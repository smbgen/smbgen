@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Guided Setup'],
    ];
@endphp

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Super Admin Guided Setup</h1>
            <p class="admin-page-subtitle">Stand up smbgen as a managed platform: pick the frontend, enable modules, and assign super-admin ownership.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('super-admin.guided-setup.store') }}" class="space-y-6">
        @csrf

        <div class="admin-card">
            <div class="admin-card-header"><h3 class="admin-card-title">Step 1: Identify This Deployment</h3></div>
            <div class="admin-card-body grid gap-4 md:grid-cols-2">
                <div class="form-group">
                    <label class="form-label" for="deployment_name">Deployment Name</label>
                    <input class="form-input" id="deployment_name" name="deployment_name" value="{{ old('deployment_name', $deploymentName) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="deployment_environment">Environment</label>
                    <select class="form-input" id="deployment_environment" name="deployment_environment">
                        @foreach(['local', 'staging', 'production'] as $environment)
                            <option value="{{ $environment }}" @selected(old('deployment_environment', $deploymentEnvironment) === $environment)>{{ ucfirst($environment) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label" for="deployment_domain">Primary Domain</label>
                    <input class="form-input" id="deployment_domain" name="deployment_domain" value="{{ old('deployment_domain', $deploymentDomain) }}">
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h3 class="admin-card-title">Step 2: Choose The Frontend Site Module</h3></div>
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
            <div class="admin-card-header"><h3 class="admin-card-title">Step 3: Enable Product Stacks</h3></div>
            <div class="admin-card-body space-y-3">
                @php($enabledModuleKeys = old('enabled_modules', collect($modules)->where('enabled', true)->pluck('key')->all()))
                @foreach($modules as $module)
                    <label class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <input type="checkbox" name="enabled_modules[]" value="{{ $module['key'] }}" class="mt-1" @checked(in_array($module['key'], $enabledModuleKeys, true))>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $module['name'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $module['description'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-secondary">Save Setup Progress</button>
        </div>
    </form>

    <div class="admin-card">
        <div class="admin-card-header"><h3 class="admin-card-title">Step 4: Assign Super Admin Access</h3></div>
        <div class="admin-card-body space-y-3">
            @foreach($users as $user)
                <form method="POST" action="{{ route('super-admin.users.super-admin', $user) }}" class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</div>
                    </div>
                    <input type="hidden" name="is_super_admin" value="{{ $user->isSuperAdmin() ? '0' : '1' }}">
                    <button type="submit" class="btn-secondary">{{ $user->isSuperAdmin() ? 'Remove Super Admin' : 'Promote To Super Admin' }}</button>
                </form>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('super-admin.guided-setup.complete') }}" class="flex justify-end">
        @csrf
        <button type="submit" class="btn-primary">Mark Guided Setup Complete</button>
    </form>
</div>
@endsection