@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Tenants', 'url' => route('super-admin.tenants.index')],
        ['label' => $tenant->name, 'url' => route('super-admin.tenants.show', $tenant)],
        ['label' => 'Edit']
    ];
@endphp

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Edit Tenant: {{ $tenant->name }}</h1>
    <p class="text-gray-400">Update tenant information and settings</p>
</div>

<form method="POST" action="{{ route('super-admin.tenants.update', $tenant) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Tenant Information -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Tenant Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                    Company Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required
                    class="w-full px-4 py-2 bg-gray-900 border @error('name') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                    Company Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $tenant->email) }}" required
                    class="w-full px-4 py-2 bg-gray-900 border @error('email') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subdomain" class="block text-sm font-medium text-gray-300 mb-2">
                    Subdomain <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain', $tenant->subdomain) }}" required
                        pattern="[a-z0-9\-]+" title="Only lowercase letters, numbers, and hyphens allowed"
                        class="flex-1 px-4 py-2 bg-gray-900 border @error('subdomain') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                    <span class="text-gray-400">.{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost' }}</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and hyphens</p>
                @error('subdomain')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="custom_domain" class="block text-sm font-medium text-gray-300 mb-2">
                    Custom Domain (Optional)
                </label>
                <input type="text" id="custom_domain" name="custom_domain" value="{{ old('custom_domain', $tenant->custom_domain) }}"
                    placeholder="example.com"
                    class="w-full px-4 py-2 bg-gray-900 border @error('custom_domain') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('custom_domain')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="plan" class="block text-sm font-medium text-gray-300 mb-2">
                    Plan <span class="text-red-500">*</span>
                </label>
                <select id="plan" name="plan" required
                    class="w-full px-4 py-2 bg-gray-900 border @error('plan') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                    <option value="trial" {{ old('plan', $tenant->plan) === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="basic" {{ old('plan', $tenant->plan) === 'basic' ? 'selected' : '' }}>Basic</option>
                    <option value="pro" {{ old('plan', $tenant->plan) === 'pro' ? 'selected' : '' }}>Pro</option>
                    <option value="enterprise" {{ old('plan', $tenant->plan) === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
                @error('plan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="trial_ends_at" class="block text-sm font-medium text-gray-300 mb-2">
                    Trial End Date (Optional)
                </label>
                <input type="date" id="trial_ends_at" name="trial_ends_at" 
                    value="{{ old('trial_ends_at', $tenant->trial_ends_at?->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 bg-gray-900 border @error('trial_ends_at') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('trial_ends_at')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-red-600 bg-gray-900 border-gray-700 rounded focus:ring-red-500">
                    <span class="text-sm text-gray-300">Tenant is active</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Tenant Metadata -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Metadata</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Tenant ID</label>
                <input type="text" value="{{ $tenant->id }}" disabled
                    class="w-full px-4 py-2 bg-gray-900 border border-gray-700 text-gray-500 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Created</label>
                <input type="text" value="{{ $tenant->created_at->format('M d, Y g:i A') }}" disabled
                    class="w-full px-4 py-2 bg-gray-900 border border-gray-700 text-gray-500 rounded-lg">
            </div>

            @if($tenant->stripe_customer_id)
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Stripe Customer ID</label>
                <input type="text" value="{{ $tenant->stripe_customer_id }}" disabled
                    class="w-full px-4 py-2 bg-gray-900 border border-gray-700 text-gray-500 rounded-lg">
            </div>
            @endif

            @if($tenant->stripe_subscription_id)
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Stripe Subscription ID</label>
                <input type="text" value="{{ $tenant->stripe_subscription_id }}" disabled
                    class="w-full px-4 py-2 bg-gray-900 border border-gray-700 text-gray-500 rounded-lg">
            </div>
            @endif
        </div>
    </div>

    <!-- Error Summary -->
    @if($errors->any())
        <div class="bg-red-900/20 border border-red-500 rounded-lg p-4">
            <div class="flex items-center gap-2 text-red-500 mb-2">
                <i class="fas fa-exclamation-circle"></i>
                <h3 class="font-semibold">Please correct the following errors:</h3>
            </div>
            <ul class="list-disc list-inside text-sm text-red-400 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex items-center justify-end gap-4">
        <a href="{{ route('super-admin.tenants.show', $tenant) }}" 
            class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
            Cancel
        </a>
        <button type="submit" 
            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
            <i class="fas fa-save mr-2"></i>
            Update Tenant
        </button>
    </div>
</form>
@endsection
