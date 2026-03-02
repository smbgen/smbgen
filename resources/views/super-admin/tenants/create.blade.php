@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Tenants', 'url' => route('super-admin.tenants.index')],
        ['label' => 'Create Tenant']
    ];
@endphp

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Create New Tenant</h1>
    <p class="text-gray-400">Set up a new organization with dedicated database and admin account</p>
</div>

<form method="POST" action="{{ route('super-admin.tenants.store') }}" class="space-y-6">
    @csrf

    <!-- Tenant Information -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Tenant Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                    Company Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 bg-gray-900 border @error('name') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                    Company Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
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
                    <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" required
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
                <input type="text" id="custom_domain" name="custom_domain" value="{{ old('custom_domain') }}"
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
                    <option value="trial" {{ old('plan') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="basic" {{ old('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                    <option value="pro" {{ old('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                    <option value="enterprise" {{ old('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
                @error('plan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="trial_ends_at" class="block text-sm font-medium text-gray-300 mb-2">
                    Trial End Date (Optional)
                </label>
                <input type="date" id="trial_ends_at" name="trial_ends_at" value="{{ old('trial_ends_at', now()->addDays(14)->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 bg-gray-900 border @error('trial_ends_at') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('trial_ends_at')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-red-600 bg-gray-900 border-gray-700 rounded focus:ring-red-500">
                    <span class="text-sm text-gray-300">Tenant is active</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Admin User Information -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Admin User Account</h2>
        <p class="text-gray-400 text-sm mb-4">This account will have full administrative access to the tenant</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="admin_name" class="block text-sm font-medium text-gray-300 mb-2">
                    Admin Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required
                    class="w-full px-4 py-2 bg-gray-900 border @error('admin_name') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('admin_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="admin_email" class="block text-sm font-medium text-gray-300 mb-2">
                    Admin Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required
                    class="w-full px-4 py-2 bg-gray-900 border @error('admin_email') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('admin_email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="admin_password" class="block text-sm font-medium text-gray-300 mb-2">
                    Admin Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="admin_password" name="admin_password" required minlength="8"
                    class="w-full px-4 py-2 bg-gray-900 border @error('admin_password') border-red-500 @else border-gray-700 @enderror text-white rounded-lg focus:ring-2 focus:ring-red-500">
                @error('admin_password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" required minlength="8"
                    class="w-full px-4 py-2 bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-red-500">
            </div>
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
        <a href="{{ route('super-admin.tenants.index') }}" 
            class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
            Cancel
        </a>
        <button type="submit" 
            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Create Tenant
        </button>
    </div>
</form>

<script>
// Auto-generate subdomain from company name
document.getElementById('name')?.addEventListener('input', function(e) {
    const subdomainInput = document.getElementById('subdomain');
    if (subdomainInput && !subdomainInput.value) {
        const subdomain = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .substring(0, 30);
        subdomainInput.value = subdomain;
    }
});
</script>
@endsection
