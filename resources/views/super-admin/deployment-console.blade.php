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
</div>
@endsection