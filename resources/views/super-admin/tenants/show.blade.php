@extends('layouts.super-admin')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Tenants', 'url' => route('super-admin.tenants.index')],
        ['label' => $tenant->name]
    ];
@endphp

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ $tenant->name }}</h1>
            <p class="text-gray-400">Tenant ID: <span class="font-mono text-sm">{{ $tenant->id }}</span></p>
        </div>
        <a href="{{ route('super-admin.tenants.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Tenants
        </a>
    </div>
    
    <!-- Quick Action Buttons -->
    <div class="flex flex-wrap gap-3">
        <form method="POST" action="{{ route('super-admin.tenants.impersonate', $tenant) }}" class="inline-block">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-user-secret"></i>
                Login as Admin
            </button>
        </form>
        
        @if($tenant->stripe_customer_id)
            <a href="https://dashboard.stripe.com/customers/{{ $tenant->stripe_customer_id }}" 
               target="_blank" 
               class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fab fa-stripe"></i>
                View in Stripe
                <i class="fas fa-external-link-alt text-xs"></i>
            </a>
        @endif
        
        @if($tenant->email)
            <a href="mailto:{{ $tenant->email }}" 
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-envelope"></i>
                Email Admin
            </a>
        @endif
        
        <a href="{{ route('super-admin.tenants.show', $tenant) }}#activity" 
           class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
            <i class="fas fa-history"></i>
            Activity Logs
        </a>
    </div>
</div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tenant Details -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Tenant Information</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Company Name</div>
                        <div class="text-white font-medium">{{ $tenant->name }}</div>
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Email</div>
                        <div class="text-white">{{ $tenant->email }}</div>
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Subscription Tier</div>
                        <div>
                            @if($tenant->subscriptionTier)
                                <span class="px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg text-sm font-semibold">
                                    {{ $tenant->subscriptionTier->name }}
                                </span>
                                <div class="text-gray-400 text-xs mt-1">
                                    {{ $tenant->subscriptionTier->description }}
                                </div>
                            @else
                                <span class="px-2 py-1 bg-gray-600/20 text-gray-400 rounded text-sm">
                                    No tier assigned
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Plan</div>
                        <div>
                            <span class="px-2 py-1 bg-blue-600/20 text-blue-400 rounded text-sm font-medium">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Status</div>
                        <div>
                            @if($tenant->is_active)
                                <span class="px-2 py-1 bg-green-600/20 text-green-400 rounded text-sm font-medium">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-600/20 text-red-400 rounded text-sm font-medium">Suspended</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Trial Ends</div>
                        <div class="text-white">
                            @if($tenant->trial_ends_at)
                                {{ $tenant->trial_ends_at->format('M j, Y g:i A') }}
                                <span class="text-gray-500 text-sm">({{ $tenant->trial_ends_at->diffForHumans() }})</span>
                            @else
                                <span class="text-gray-500">N/A</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Created</div>
                        <div class="text-white">{{ $tenant->created_at->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Subscription Tier Details -->
            @if($tenant->subscriptionTier)
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white">Subscription Details</h2>
                    <button type="button" 
                            onclick="document.getElementById('change-tier-form').classList.toggle('hidden')"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-right"></i>
                        Change Tier
                    </button>
                </div>
                
                <!-- Change Tier Form (Hidden by default) -->
                <form id="change-tier-form" method="POST" action="{{ route('super-admin.tenants.change-tier', $tenant) }}" class="hidden mb-6 p-4 bg-gray-900 rounded-lg border border-blue-500/50">
                    @csrf
                    <label for="subscription_tier_id" class="block text-sm font-medium text-gray-300 mb-2">Select New Tier</label>
                    <select name="subscription_tier_id" id="subscription_tier_id" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-blue-500 mb-3">
                        @foreach(\App\Models\SubscriptionTier::active()->ordered()->get() as $tier)
                            <option value="{{ $tier->id }}" {{ $tenant->subscription_tier_id === $tier->id ? 'selected' : '' }}>
                                {{ $tier->name }}
                                @if($tier->price_cents > 0)
                                    - ${{ number_format($tier->price_cents / 100, 2) }}/{{ $tier->billing_period }}
                                @else
                                    - Free
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-check"></i>
                            Change Tier
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('change-tier-form').classList.add('hidden')"
                                class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Current Tier</div>
                        <div class="text-white font-semibold">{{ $tenant->subscriptionTier->name }}</div>
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Price</div>
                        <div class="text-white font-semibold">
                            @if($tenant->subscriptionTier->price_cents > 0)
                                ${{ number_format($tenant->subscriptionTier->price_cents / 100, 2) }}/{{ $tenant->subscriptionTier->billing_period }}
                            @else
                                Free
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Features Grid -->
                <div class="mt-6">
                    <div class="text-gray-400 text-sm font-semibold mb-3">Included Features</div>
                    <div class="grid grid-cols-2 gap-3">
                        @php
                            $features = [
                                'booking' => 'Booking System',
                                'client_area' => 'Client Area',
                                'messaging' => 'Messaging',
                                'landing_pages' => 'Landing Pages',
                                'cms' => 'CMS',
                                'branding' => 'Custom Branding',
                                'billing' => 'Billing & Invoices',
                                'api_access' => 'API Access',
                                'custom_domain' => 'Custom Domain',
                                'white_label' => 'White Label',
                                'phone_system' => 'Phone System',
                                'advanced_reporting' => 'Advanced Reporting',
                                'priority_support' => 'Priority Support',
                            ];
                        @endphp
                        
                        @foreach($features as $key => $label)
                            @if($tenant->subscriptionTier->hasFeature($key))
                                <div class="flex items-center gap-2 p-2 bg-green-900/20 rounded">
                                    <i class="fas fa-check text-green-400"></i>
                                    <span class="text-sm text-green-400">{{ $label }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 p-2 bg-gray-900 rounded opacity-50">
                                    <i class="fas fa-times text-gray-500"></i>
                                    <span class="text-sm text-gray-500">{{ $label }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                <!-- Resource Limits -->
                <div class="mt-6">
                    <div class="text-gray-400 text-sm font-semibold mb-3">Resource Limits</div>
                    <div class="grid grid-cols-2 gap-3">
                        @php
                            $limits = [
                                'max_services' => 'Max Services',
                                'max_clients' => 'Max Clients',
                                'max_users' => 'Max Users',
                                'max_bookings_per_month' => 'Bookings/Month',
                                'storage_gb' => 'Storage (GB)',
                                'api_calls_per_month' => 'API Calls/Month',
                            ];
                        @endphp
                        
                        @foreach($limits as $key => $label)
                            @php
                                $limit = $tenant->subscriptionTier->getLimit($key);
                                $displayValue = match($key) {
                                    'storage_gb' => $limit === 999 ? '∞' : $limit . ' GB',
                                    'api_calls_per_month', 'max_bookings_per_month' => $limit === 999999 ? '∞' : number_format($limit),
                                    default => $limit === 999 ? '∞' : number_format($limit),
                                };
                            @endphp
                            <div class="p-3 bg-gray-900 rounded">
                                <div class="text-gray-400 text-xs">{{ $label }}</div>
                                <div class="text-white font-semibold text-lg">{{ $displayValue }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Domains -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-white">Domains</h2>
                    <button type="button" 
                            onclick="document.getElementById('add-domain-form').classList.toggle('hidden')"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Add Domain
                    </button>
                </div>
                
                <!-- Add Domain Form (Hidden by default) -->
                <form id="add-domain-form" method="POST" action="{{ route('super-admin.tenants.domains.add', $tenant) }}" class="hidden mb-4 p-4 bg-gray-900 rounded-lg border border-blue-500/50">
                    @csrf
                    <label for="new_domain" class="block text-sm font-medium text-gray-300 mb-2">New Domain</label>
                    <div class="flex gap-2">
                        <input type="text" 
                               id="new_domain" 
                               name="domain" 
                               placeholder="example.com or subdomain.example.com"
                               required
                               class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            Add
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('add-domain-form').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-400">
                        <i class="fas fa-info-circle"></i>
                        Enter a fully qualified domain name. DNS must point to this server.
                    </p>
                </form>
                
                <div class="space-y-3">
                    @forelse($tenant->domains as $domain)
                    <div class="flex items-center justify-between bg-gray-900 p-4 rounded-lg group">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <div class="text-white font-mono">{{ $domain->domain }}</div>
                                @if($tenant->primary_domain === $domain->domain)
                                    <span class="px-2 py-0.5 bg-blue-600/20 text-blue-400 rounded text-xs font-medium">PRIMARY</span>
                                @endif
                            </div>
                            <div class="text-gray-400 text-sm">Added {{ $domain->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded text-sm">Active</span>
                            
                            @if($tenant->primary_domain !== $domain->domain)
                                <form method="POST" action="{{ route('super-admin.tenants.domains.primary', [$tenant, $domain]) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            title="Set as primary domain"
                                            class="opacity-0 group-hover:opacity-100 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition-all">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                            @endif
                            
                            @if($tenant->domains->count() > 1)
                                <form method="POST" 
                                      action="{{ route('super-admin.tenants.domains.remove', [$tenant, $domain]) }}" 
                                      class="inline"
                                      onsubmit="return confirm('Remove domain {{ $domain->domain }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            title="Remove domain"
                                            class="opacity-0 group-hover:opacity-100 px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-all">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400">No domains configured</p>
                    @endforelse
                </div>
            </div>

            <!-- Users -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Users ({{ $users->count() }})</h2>
                
                <div class="space-y-3">
                    @forelse($users as $user)
                    <div class="flex items-center justify-between bg-gray-900 p-4 rounded-lg">
                        <div>
                            <div class="text-white font-medium">{{ $user->name }}</div>
                            <div class="text-gray-400 text-sm">{{ $user->email }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-purple-600/20 text-purple-400 rounded text-xs">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400">No users found</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Actions</h2>
                
                <div class="space-y-3">
                    @if(\Illuminate\Support\Facades\Route::has('super-admin.tenants.edit'))
                        <a href="{{ route('super-admin.tenants.edit', $tenant) }}" 
                           class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-edit"></i>
                            Edit Tenant
                        </a>
                    @endif

                    @if($tenant->is_active)
                        <form method="POST" action="{{ route('super-admin.tenants.suspend', $tenant) }}" 
                              onsubmit="return confirm('Are you sure you want to suspend this tenant? They will lose access to their account.')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-ban"></i>
                                Suspend Tenant
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('super-admin.tenants.activate', $tenant) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Activate Tenant
                            </button>
                        </form>
                    @endif
                    
                    @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                        <button type="button" 
                                onclick="document.getElementById('extend-trial-form').classList.toggle('hidden')"
                                class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-clock"></i>
                            Extend Trial
                        </button>
                        
                        <form id="extend-trial-form" method="POST" action="{{ route('super-admin.tenants.extend-trial', $tenant) }}" class="hidden pt-3 border-t border-gray-700">
                            @csrf
                            <label class="block text-sm font-medium text-gray-300 mb-2">Extend trial by (days)</label>
                            <input type="number" name="days" value="7" min="1" max="365" class="w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white mb-3">
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                                Confirm Extension
                            </button>
                        </form>
                    @endif
                    
                    <!-- Delete Tenant Button -->
                    <button type="button" 
                            onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i>
                        Delete Tenant
                    </button>
                </div>
            </div>
            
            <!-- Quick Access Links -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Quick Access</h2>
                
                <div class="space-y-2">
                    @php
                        $primaryDomain = $tenant->domains->first();
                        $tenantUrl = $primaryDomain ? 'https://' . $primaryDomain->domain : null;
                    @endphp
                    
                    @if($tenantUrl)
                        <a href="{{ $tenantUrl }}" target="_blank" class="flex items-center justify-between p-3 bg-gray-900 hover:bg-gray-900/70 rounded-lg text-gray-300 hover:text-white transition-colors group">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-home text-blue-400"></i>
                                <span class="text-sm">Public Website</span>
                            </div>
                            <i class="fas fa-external-link-alt text-xs text-gray-500 group-hover:text-gray-400"></i>
                        </a>
                        
                        <a href="{{ $tenantUrl }}/admin" target="_blank" class="flex items-center justify-between p-3 bg-gray-900 hover:bg-gray-900/70 rounded-lg text-gray-300 hover:text-white transition-colors group">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tachometer-alt text-green-400"></i>
                                <span class="text-sm">Admin Dashboard</span>
                            </div>
                            <i class="fas fa-external-link-alt text-xs text-gray-500 group-hover:text-gray-400"></i>
                        </a>
                        
                        <a href="{{ $tenantUrl }}/admin/clients" target="_blank" class="flex items-center justify-between p-3 bg-gray-900 hover:bg-gray-900/70 rounded-lg text-gray-300 hover:text-white transition-colors group">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-purple-400"></i>
                                <span class="text-sm">Clients</span>
                            </div>
                            <i class="fas fa-external-link-alt text-xs text-gray-500 group-hover:text-gray-400"></i>
                        </a>
                        
                        <a href="{{ $tenantUrl }}/admin/cms" target="_blank" class="flex items-center justify-between p-3 bg-gray-900 hover:bg-gray-900/70 rounded-lg text-gray-300 hover:text-white transition-colors group">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-alt text-yellow-400"></i>
                                <span class="text-sm">CMS Editor</span>
                            </div>
                            <i class="fas fa-external-link-alt text-xs text-gray-500 group-hover:text-gray-400"></i>
                        </a>
                    @else
                        <p class="text-gray-400 text-sm">No domains configured</p>
                    @endif
                </div>
            </div>

            <!-- Billing Info -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Billing</h2>
                
                <div class="space-y-3">
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Stripe Customer</div>
                        @if($tenant->stripe_customer_id)
                            <div class="flex items-center justify-between">
                                <div class="text-white font-mono text-xs truncate mr-2">
                                    {{ $tenant->stripe_customer_id }}
                                </div>
                                <a href="https://dashboard.stripe.com/customers/{{ $tenant->stripe_customer_id }}" 
                                   target="_blank"
                                   class="text-purple-400 hover:text-purple-300 text-xs flex items-center gap-1">
                                    <i class="fab fa-stripe"></i>
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        @else
                            <div class="text-gray-500 text-sm">Not set</div>
                        @endif
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-1">Subscription</div>
                        @if($tenant->stripe_subscription_id)
                            <div class="flex items-center justify-between">
                                <div class="text-white font-mono text-xs truncate mr-2">
                                    {{ $tenant->stripe_subscription_id }}
                                </div>
                                <a href="https://dashboard.stripe.com/subscriptions/{{ $tenant->stripe_subscription_id }}" 
                                   target="_blank"
                                   class="text-purple-400 hover:text-purple-300 text-xs flex items-center gap-1">
                                    <i class="fab fa-stripe"></i>
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        @else
                            <div class="text-gray-500 text-sm">Not set</div>
                        @endif
                    </div>
                    
                    @if($tenant->stripe_customer_id)
                        <div class="pt-3 border-t border-gray-700">
                            <a href="https://dashboard.stripe.com/customers/{{ $tenant->stripe_customer_id }}" 
                               target="_blank"
                               class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                                <i class="fab fa-stripe"></i>
                                Open in Stripe
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- System Info -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-white mb-4">System Info</h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between p-2 bg-gray-900 rounded">
                        <span class="text-gray-400">Database</span>
                        <span class="text-white font-mono text-xs">{{ $tenant->id }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-900 rounded">
                        <span class="text-gray-400">Users</span>
                        <span class="text-white font-semibold">{{ $users->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-900 rounded">
                        <span class="text-gray-400">Domains</span>
                        <span class="text-white font-semibold">{{ $tenant->domains->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-900 rounded">
                        <span class="text-gray-400">Created</span>
                        <span class="text-white">{{ $tenant->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity Log Section -->
    <div id="activity" class="mt-8 bg-gray-800 rounded-lg border border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-history"></i>
            Recent Activity
        </h2>
        <p class="text-gray-400 text-sm">Activity logging feature coming soon...</p>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-lg border border-red-500 max-w-md w-full p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-600/20 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Delete Tenant</h3>
                    <p class="text-gray-400 text-sm">This action cannot be undone</p>
                </div>
            </div>

            <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-4 mb-4">
                <p class="text-red-400 text-sm mb-2">
                    <strong>Warning:</strong> Deleting this tenant will:
                </p>
                <ul class="list-disc list-inside text-red-400 text-sm space-y-1">
                    <li>Permanently delete the tenant database</li>
                    <li>Remove all associated users ({{ $users->count() }})</li>
                    <li>Delete all domains ({{ $tenant->domains->count() }})</li>
                    <li>Remove all client data and files</li>
                    <li>Cancel any active Stripe subscriptions</li>
                </ul>
            </div>

            <div class="mb-4">
                <label for="confirm-name" class="block text-sm font-medium text-gray-300 mb-2">
                    To confirm, type the tenant name: <strong class="text-white">{{ $tenant->name }}</strong>
                </label>
                <input type="text" 
                       id="confirm-name" 
                       class="w-full px-4 py-2 bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-red-500"
                       placeholder="Type tenant name here">
                <p id="confirm-error" class="hidden mt-2 text-sm text-red-500">
                    <i class="fas fa-exclamation-circle"></i>
                    Tenant name does not match
                </p>
            </div>

            <div class="flex gap-3">
                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
                <form method="POST" action="{{ route('super-admin.tenants.destroy', $tenant) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            id="confirm-delete-btn"
                            onclick="confirmDelete()"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors">
                        Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            const input = document.getElementById('confirm-name');
            const error = document.getElementById('confirm-error');
            const expectedName = "{{ $tenant->name }}";
            
            if (input.value.trim() === expectedName) {
                // Submit the form
                document.querySelector('#confirm-delete-btn').closest('form').submit();
            } else {
                error.classList.remove('hidden');
                input.classList.add('border-red-500');
                input.focus();
            }
        }

        // Hide error when user starts typing
        document.getElementById('confirm-name')?.addEventListener('input', function() {
            document.getElementById('confirm-error').classList.add('hidden');
            this.classList.remove('border-red-500');
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('delete-modal').classList.add('hidden');
            }
        });

        // Close modal when clicking outside
        document.getElementById('delete-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>
</div>
@endsection
