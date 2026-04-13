@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Platform Billing'],
    ];
@endphp

@section('content')
<div class="max-w-7xl mx-auto py-6 space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-400">Revenue Control</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Platform Billing</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-400">This is the central billing surface for how tenants pay smbgen. Tenant admins still configure their own Stripe, email, calendar, and service integrations inside their tenant admin dashboard.</p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm text-slate-300">
            Tenant self-service lives in <span class="font-medium text-white">Admin Dashboard → Integrations & Services</span>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
            <div class="text-sm text-slate-500">Billable Tenants</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ $stats['billableTenants'] }}</div>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
            <div class="text-sm text-slate-500">Active Subscriptions</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ $stats['activeSubscriptions'] }}</div>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
            <div class="text-sm text-slate-500">Missing Billing Setup</div>
            <div class="mt-2 text-2xl font-semibold text-amber-300">{{ $stats['missingBillingSetup'] }}</div>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
            <div class="text-sm text-slate-500">Trial Tenants</div>
            <div class="mt-2 text-2xl font-semibold text-cyan-300">{{ $stats['trialTenants'] }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
        <form method="GET" action="{{ route('super-admin.billing.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tenant name, email, subdomain, domain..." class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
            <select name="plan" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-cyan-500 focus:outline-none">
                <option value="">All plans</option>
                @foreach (['trial', 'starter', 'professional', 'enterprise'] as $plan)
                    <option value="{{ $plan }}" @selected(request('plan') === $plan)>{{ ucfirst($plan) }}</option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-cyan-400 transition-colors">Filter</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">
        <table class="min-w-full divide-y divide-slate-800 text-sm">
            <thead class="bg-slate-950/70 text-slate-400">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Tenant</th>
                    <th class="px-4 py-3 text-left font-medium">Plan</th>
                    <th class="px-4 py-3 text-left font-medium">Platform Billing</th>
                    <th class="px-4 py-3 text-left font-medium">Tenant Self-Service</th>
                    <th class="px-4 py-3 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse ($tenants as $tenant)
                    <tr>
                        <td class="px-4 py-4 align-top">
                            <div class="font-medium text-white">{{ $tenant->name }}</div>
                            <div class="mt-1 text-xs text-slate-400">{{ $tenant->email }}</div>
                            <div class="mt-1 text-xs font-mono text-slate-500">{{ $tenant->subdomain }}</div>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <div class="text-slate-200">{{ $tenant->subscriptionTier?->name ?? ucfirst($tenant->plan) }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $tenant->subscriptionTier?->formattedPrice() ?? 'No tier price set' }}</div>
                        </td>
                        <td class="px-4 py-4 align-top">
                            @if ($tenant->stripe_subscription_id)
                                <div class="inline-flex rounded-full bg-green-500/10 px-2.5 py-1 text-xs font-medium text-green-300">Subscribed</div>
                                <div class="mt-2 text-xs font-mono text-slate-500">{{ $tenant->stripe_subscription_id }}</div>
                            @elseif ($tenant->stripe_customer_id)
                                <div class="inline-flex rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-medium text-amber-300">Customer Only</div>
                                <div class="mt-2 text-xs font-mono text-slate-500">{{ $tenant->stripe_customer_id }}</div>
                            @else
                                <div class="inline-flex rounded-full bg-slate-800 px-2.5 py-1 text-xs font-medium text-slate-300">Not Configured</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 align-top">
                            <div class="text-slate-200">Tenant admin dashboard</div>
                            <div class="mt-1 text-xs text-slate-500">They configure Stripe and services themselves via Integrations & Services.</div>
                        </td>
                        <td class="px-4 py-4 align-top text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="inline-flex items-center rounded-lg border border-slate-700 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-800 transition-colors">View</a>
                                <form method="POST" action="{{ route('super-admin.tenants.impersonate', $tenant) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center rounded-lg bg-cyan-500 px-3 py-2 text-xs font-medium text-slate-950 hover:bg-cyan-400 transition-colors">Impersonate</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">No tenants found for this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tenants->hasPages())
        <div>
            {{ $tenants->links() }}
        </div>
    @endif
</div>
@endsection