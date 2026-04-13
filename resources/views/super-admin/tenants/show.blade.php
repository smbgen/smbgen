@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Tenants', 'url' => route('super-admin.tenants.index')],
        ['label' => $tenant->name],
    ];
@endphp

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">{{ $tenant->name }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $tenant->subdomain }}.{{ config('app.domain', 'smbgen.app') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.tenants.edit', $tenant) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-pencil-alt"></i> Edit
            </a>
            <form method="POST" action="{{ route('super-admin.tenants.impersonate', $tenant) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-user-secret"></i> Impersonate
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Details card --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider mb-4">Details</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500">ID</dt>
                        <dd class="text-gray-200 font-mono text-xs mt-0.5">{{ $tenant->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-200 mt-0.5">{{ $tenant->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Plan</dt>
                        <dd class="mt-0.5">
                            @php
                                $planColors = [
                                    'trial' => 'bg-gray-700 text-gray-300',
                                    'starter' => 'bg-blue-900/50 text-blue-300',
                                    'professional' => 'bg-purple-900/50 text-purple-300',
                                    'enterprise' => 'bg-amber-900/50 text-amber-300',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $planColors[$tenant->plan] ?? 'bg-gray-700 text-gray-300' }}">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="mt-0.5">
                            @if ($tenant->is_active)
                                <span class="inline-flex items-center gap-1 text-green-400 text-xs"><span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Active</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-red-400 text-xs"><span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span> Suspended</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Deployment Mode</dt>
                        <dd class="text-gray-200 mt-0.5">{{ ucfirst($tenant->deployment_mode ?? 'shared') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Trial Ends</dt>
                        <dd class="mt-0.5 {{ $tenant->isTrialExpired() ? 'text-red-400' : 'text-amber-400' }}">
                            {{ $tenant->trial_ends_at?->format('M j, Y') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Created</dt>
                        <dd class="text-gray-200 mt-0.5">{{ $tenant->created_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @if ($tenant->custom_domain)
                        <div class="col-span-2">
                            <dt class="text-gray-500">Custom Domain</dt>
                            <dd class="text-gray-200 mt-0.5">{{ $tenant->custom_domain }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Domains --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider mb-4">Domains</h3>

                @if ($tenant->domains->isEmpty())
                    <p class="text-sm text-gray-500">No domains configured.</p>
                @else
                    <ul class="space-y-2 mb-4">
                        @foreach ($tenant->domains as $domain)
                            <li class="flex items-center justify-between py-2 border-b border-gray-800 last:border-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-200 font-mono">{{ $domain->domain }}</span>
                                    @if ($tenant->custom_domain === $domain->domain)
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-900/40 text-indigo-300 border border-indigo-800">Primary</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    @if ($tenant->custom_domain !== $domain->domain)
                                        <form method="POST" action="{{ route('super-admin.tenants.domains.primary', [$tenant, $domain]) }}">
                                            @csrf
                                            <button class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">Set Primary</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('super-admin.tenants.domains.remove', [$tenant, $domain]) }}"
                                          onsubmit="return confirm('Remove this domain?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-400 hover:text-red-300 transition-colors">Remove</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('super-admin.tenants.domains.add', $tenant) }}" class="flex gap-2 mt-3">
                    @csrf
                    <input type="text" name="domain" placeholder="new-domain.example.com"
                           class="flex-1 bg-gray-800 border border-gray-700 text-gray-200 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm rounded-lg transition-colors">
                        Add Domain
                    </button>
                </form>
                @error('domain') <p class="mt-2 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Users --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider mb-4">Users ({{ $users->count() }})</h3>

                @if ($users->isEmpty())
                    <p class="text-sm text-gray-500 mb-6">No users in this tenant yet.</p>
                @else
                    <div class="mb-6">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-800">
                                    <th class="text-left pb-2 text-xs text-gray-500">Name</th>
                                    <th class="text-left pb-2 text-xs text-gray-500">Email</th>
                                    <th class="text-left pb-2 text-xs text-gray-500">Role</th>
                                    <th class="text-left pb-2 text-xs text-gray-500">Status</th>
                                    <th class="text-left pb-2 text-xs text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="py-2.5 text-gray-200">{{ $user->name }}</td>
                                        <td class="py-2.5 text-gray-400 text-xs">{{ $user->email }}</td>
                                        <td class="py-2.5">
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">
                                                {{ ucfirst(str_replace('_', ' ', $user->role ?? 'user')) }}
                                            </span>
                                        </td>
                                        <td class="py-2.5">
                                            @if ($user->email_verified_at)
                                                <span class="inline-flex items-center gap-1 text-green-400 text-xs">
                                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Verified
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-amber-400 text-xs">
                                                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> Unverified
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2.5">
                                            <div class="flex items-center gap-2">
                                                @if (!$user->email_verified_at)
                                                    <form method="POST" action="{{ route('super-admin.tenants.users.verify', [$tenant, $user]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                                                            Verify
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('super-admin.tenants.users.remove', [$tenant, $user]) }}" class="inline" onsubmit="return confirm('Remove this user from the tenant?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Add New User Form --}}
                <div class="border-t border-gray-800 pt-4">
                    <h4 class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Add New User</h4>
                    <form method="POST" action="{{ route('super-admin.tenants.users.store', $tenant) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Name</label>
                                <input type="text" name="name" required
                                       class="w-full bg-gray-800 border border-gray-700 text-gray-200 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                                       placeholder="John Doe">
                                @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Email</label>
                                <input type="email" name="email" required
                                       class="w-full bg-gray-800 border border-gray-700 text-gray-200 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                                       placeholder="john@example.com">
                                @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Password</label>
                                <input type="password" name="password" required
                                       class="w-full bg-gray-800 border border-gray-700 text-gray-200 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                                       placeholder="At least 8 characters">
                                @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Role</label>
                                <select name="role" required
                                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                                    <option value="company_administrator">Company Administrator</option>
                                    <option value="team_member">Team Member</option>
                                    <option value="client">Client</option>
                                    <option value="staff">Staff</option>
                                </select>
                                @error('role') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Add User
                        </button>
                    </form>
                    @error('error') <p class="text-xs text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Actions sidebar --}}
        <div class="space-y-4">

            {{-- Subscription actions --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 space-y-3">
                <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Actions</h3>

                @if ($tenant->is_active)
                    <form method="POST" action="{{ route('super-admin.tenants.suspend', $tenant) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Suspend this tenant?')"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-900/40 hover:bg-red-900/60 border border-red-800 text-red-300 text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-ban"></i> Suspend Tenant
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('super-admin.tenants.activate', $tenant) }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-900/40 hover:bg-green-900/60 border border-green-800 text-green-300 text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-check-circle"></i> Activate Tenant
                        </button>
                    </form>
                @endif
            </div>

            {{-- Extend trial --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 space-y-3">
                <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Extend Trial</h3>
                <form method="POST" action="{{ route('super-admin.tenants.extend-trial', $tenant) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">New trial end date</label>
                        <input type="date" name="trial_ends_at"
                               value="{{ $tenant->trial_ends_at?->format('Y-m-d') ?? now()->addDays(14)->format('Y-m-d') }}"
                               class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-900/40 hover:bg-amber-900/60 border border-amber-800 text-amber-300 text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-calendar-plus"></i> Extend Trial
                    </button>
                </form>
            </div>

            {{-- Change plan --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 space-y-3">
                <h3 class="text-sm font-medium text-gray-300 uppercase tracking-wider">Change Plan</h3>
                <form method="POST" action="{{ route('super-admin.tenants.change-tier', $tenant) }}" class="space-y-3">
                    @csrf
                    <select name="plan"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="trial" @selected($tenant->plan === 'trial')>Trial</option>
                        <option value="starter" @selected($tenant->plan === 'starter')>Starter</option>
                        <option value="professional" @selected($tenant->plan === 'professional')>Professional</option>
                        <option value="enterprise" @selected($tenant->plan === 'enterprise')>Enterprise</option>
                    </select>
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-900/40 hover:bg-indigo-900/60 border border-indigo-800 text-indigo-300 text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-exchange-alt"></i> Change Plan
                    </button>
                </form>
            </div>

            {{-- Danger zone --}}
            <div class="bg-gray-900 border border-red-900/50 rounded-xl p-5 space-y-3">
                <h3 class="text-sm font-medium text-red-400 uppercase tracking-wider">Danger Zone</h3>
                <form method="POST" action="{{ route('super-admin.tenants.destroy', $tenant) }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Permanently delete {{ $tenant->name }}? This cannot be undone.')"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-900/30 hover:bg-red-900/60 border border-red-800/60 text-red-400 text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-trash-alt"></i> Delete Tenant
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
