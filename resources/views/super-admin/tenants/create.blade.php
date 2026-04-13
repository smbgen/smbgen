@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Tenants', 'url' => route('super-admin.tenants.index')],
        ['label' => 'New Tenant'],
    ];
@endphp

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-white">New Tenant</h1>
        <p class="text-sm text-gray-400 mt-1">Provision a new tenant account and admin user.</p>
    </div>

    <form method="POST" action="{{ route('super-admin.tenants.store') }}" class="space-y-6">
        @csrf

        {{-- Tenant details --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 space-y-4">
            <h2 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Tenant Details</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="name">Business Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="email">Business Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="subdomain">Subdomain *</label>
                    <div class="flex items-center">
                        <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" required
                               placeholder="acme"
                               class="flex-1 bg-gray-800 border border-gray-700 text-gray-200 rounded-l-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('subdomain') border-red-500 @enderror">
                        <span class="px-3 py-2 bg-gray-700 border border-l-0 border-gray-700 text-gray-400 text-sm rounded-r-lg">.{{ config('app.domain', 'smbgen.app') }}</span>
                    </div>
                    @error('subdomain') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="custom_domain">Custom Domain</label>
                    <input type="text" id="custom_domain" name="custom_domain" value="{{ old('custom_domain') }}"
                           placeholder="app.clientbusiness.com"
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('custom_domain') border-red-500 @enderror">
                    @error('custom_domain') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="plan">Plan *</label>
                    <select id="plan" name="plan" required
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('plan') border-red-500 @enderror">
                        <option value="trial" @selected(old('plan', 'trial') === 'trial')>Trial</option>
                        <option value="starter" @selected(old('plan') === 'starter')>Starter</option>
                        <option value="professional" @selected(old('plan') === 'professional')>Professional</option>
                        <option value="enterprise" @selected(old('plan') === 'enterprise')>Enterprise</option>
                    </select>
                    @error('plan') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="deployment_mode">Deployment Mode *</label>
                    <select id="deployment_mode" name="deployment_mode" required
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('deployment_mode') border-red-500 @enderror">
                        <option value="shared" @selected(old('deployment_mode', 'shared') === 'shared')>Shared Multi-Tenant</option>
                        <option value="dedicated" @selected(old('deployment_mode') === 'dedicated')>Dedicated Enterprise</option>
                    </select>
                    @error('deployment_mode') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="trial_ends_at">Trial Ends At</label>
                    <input type="date" id="trial_ends_at" name="trial_ends_at"
                           value="{{ old('trial_ends_at', now()->addDays(14)->format('Y-m-d')) }}"
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('trial_ends_at') border-red-500 @enderror">
                    @error('trial_ends_at') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Admin user --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 space-y-4">
            <h2 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Admin User</h2>
            <p class="text-xs text-gray-500">This user will be the first administrator for the tenant.</p>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="admin_name">Name *</label>
                    <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('admin_name') border-red-500 @enderror">
                    @error('admin_name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="admin_email">Email *</label>
                    <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('admin_email') border-red-500 @enderror">
                    @error('admin_email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="admin_password">Password *</label>
                    <input type="password" id="admin_password" name="admin_password" required
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 @error('admin_password') border-red-500 @enderror">
                    @error('admin_password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1" for="admin_password_confirmation">Confirm Password *</label>
                    <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" required
                           class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('super-admin.tenants.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-plus"></i>
                Create Tenant
            </button>
        </div>
    </form>
</div>
@endsection
