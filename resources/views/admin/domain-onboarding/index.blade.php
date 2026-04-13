@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Domain Onboarding</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Choose your domain strategy, track setup status, and keep your tenant isolated.</p>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 dark:border-green-900/60 bg-green-50 dark:bg-green-900/20 p-3 text-sm text-green-800 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 dark:border-red-900/60 bg-red-50 dark:bg-red-900/20 p-3 text-sm text-red-800 dark:text-red-300">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/50 p-5 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Use a Custom Domain</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Point your DNS to your tenant workspace and publish under your own brand.</p>
            </div>

            <form method="POST" action="{{ route('admin.domain-onboarding.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="save_domain">

                <div>
                    <label for="custom_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Custom Domain</label>
                    <input
                        id="custom_domain"
                        name="custom_domain"
                        type="text"
                        value="{{ old('custom_domain', $tenant->custom_domain) }}"
                        placeholder="app.yourcompany.com"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div class="rounded-lg border border-blue-200 dark:border-blue-800/70 bg-blue-50 dark:bg-blue-900/20 p-4 text-sm text-blue-900 dark:text-blue-300 space-y-1">
                    <p class="font-semibold">DNS target</p>
                    <p class="font-mono text-xs">CNAME your custom host to {{ $platformDomain }}</p>
                    <p class="text-xs">After DNS propagates, return here and mark your domain as verified.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                        Save Custom Domain
                    </button>
                </div>
            </form>

            @if ($tenant->custom_domain)
                <form method="POST" action="{{ route('admin.domain-onboarding.update') }}" class="pt-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="mark_verified">
                    <button type="submit" class="inline-flex items-center rounded-lg border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20 px-4 py-2 text-sm font-semibold text-green-800 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/35 transition-colors">
                        I Have Pointed DNS, Mark Verified
                    </button>
                </form>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/50 p-5 space-y-4">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Current Status</h3>

            @php
                $statusStyles = [
                    'not_started' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    'pending_dns' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                    'verified' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                    'using_subdomain' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                ];
                $statusLabel = [
                    'not_started' => 'Not Started',
                    'pending_dns' => 'Pending DNS',
                    'verified' => 'Verified',
                    'using_subdomain' => 'Using Platform Subdomain',
                ];
            @endphp

            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusStyles[$status] ?? $statusStyles['not_started'] }}">
                {{ $statusLabel[$status] ?? $statusLabel['not_started'] }}
            </span>

            <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <p><span class="font-medium">Tenant:</span> {{ $tenant->name }}</p>
                <p><span class="font-medium">Workspace:</span> <span class="font-mono text-xs">{{ $platformDomain }}</span></p>
                @if ($tenant->custom_domain)
                    <p><span class="font-medium">Custom Domain:</span> <span class="font-mono text-xs">{{ $tenant->custom_domain }}</span></p>
                @endif
            </div>

            @if ($allDomains->isNotEmpty())
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">All Tenant Domains</p>
                    <div class="space-y-1">
                        @foreach ($allDomains as $domain)
                            <div class="rounded border border-gray-200 dark:border-gray-700 px-2 py-1 text-xs font-mono text-gray-700 dark:text-gray-300">{{ $domain }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.domain-onboarding.update') }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="use_subdomain">
                <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Continue With Platform Subdomain
                </button>
            </form>

            <a href="{{ route('admin.dashboard') }}" class="inline-flex w-full justify-center rounded-lg bg-gray-900 dark:bg-gray-100 px-4 py-2 text-sm font-semibold text-white dark:text-gray-900 hover:opacity-90 transition-opacity">
                Go To Dashboard
            </a>
        </div>
    </div>
</div>
@endsection