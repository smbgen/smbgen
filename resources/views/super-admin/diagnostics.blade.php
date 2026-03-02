@extends('layouts.super-admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Tenancy Diagnostics</h1>
    <p class="text-gray-400">Debug and setup multi-tenancy configuration</p>
</div>

@if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-100 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-500/20 border border-red-500 text-red-100 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

@if(session('master_tenant_id'))
    <div class="bg-purple-500/20 border border-purple-500 text-purple-100 px-4 py-3 rounded mb-6">
        <p class="font-bold mb-2">Master Tenant Created!</p>
        <p class="mb-2">Tenant ID: <code class="bg-purple-900/50 px-2 py-1 rounded">{{ session('master_tenant_id') }}</code></p>
        <p class="text-sm">{{ session('instruction') }}</p>
    </div>
@endif

@if(session('migration_output') || session('cache_output'))
    <div class="bg-gray-800 border border-gray-700 text-gray-100 px-4 py-3 rounded mb-6 font-mono text-sm">
        <pre class="whitespace-pre-wrap">{{ session('migration_output') ?? session('cache_output') }}</pre>
    </div>
@endif

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <form method="POST" action="{{ route('super-admin.diagnostics.clear-caches') }}">
        @csrf
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition">
            <i class="fas fa-broom mr-2"></i>
            Clear All Caches
        </button>
    </form>

    <form method="POST" action="{{ route('super-admin.diagnostics.run-migrations') }}">
        @csrf
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-lg transition">
            <i class="fas fa-database mr-2"></i>
            Run Migrations
        </button>
    </form>

    <button onclick="document.getElementById('create-tenant-modal').classList.remove('hidden')" 
            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 px-6 rounded-lg transition">
        <i class="fas fa-plus-circle mr-2"></i>
        Create Master Tenant
    </button>
</div>

<!-- Environment Configuration -->
<div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-6">
    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
        <i class="fas fa-cog mr-2 text-blue-400"></i>
        Environment Configuration
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($diagnostics['environment'] as $key => $value)
            <div class="bg-gray-900 p-4 rounded">
                <div class="text-gray-400 text-sm uppercase mb-1">{{ str_replace('_', ' ', $key) }}</div>
                <div class="text-white font-mono text-sm">
                    @if($value === true)
                        <span class="text-green-400">true</span>
                    @elseif($value === false)
                        <span class="text-red-400">false</span>
                    @elseif($value === 'NOT SET')
                        <span class="text-yellow-400">{{ $value }}</span>
                    @else
                        {{ $value ?: '(empty)' }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Tenant Context (Critical for Debugging) -->
<div class="bg-gray-800 rounded-lg p-6 border border-{{ $diagnostics['tenant_context']['is_initialized'] ? 'green' : 'red' }}-700 mb-6">
    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
        <i class="fas fa-info-circle mr-2 text-{{ $diagnostics['tenant_context']['is_initialized'] ? 'green' : 'red' }}-400"></i>
        Current Tenant Context
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-900 p-4 rounded">
            <div class="text-gray-400 text-sm uppercase mb-1">Tenant Initialized</div>
            <div class="text-white font-mono text-sm">
                @if($diagnostics['tenant_context']['is_initialized'])
                    <span class="text-green-400"><i class="fas fa-check-circle"></i> YES</span>
                @else
                    <span class="text-red-400"><i class="fas fa-times-circle"></i> NO</span>
                @endif
            </div>
        </div>
        <div class="bg-gray-900 p-4 rounded">
            <div class="text-gray-400 text-sm uppercase mb-1">Current Tenant ID</div>
            <div class="text-white font-mono text-sm">{{ $diagnostics['tenant_context']['current_tenant_id'] }}</div>
        </div>
    </div>
    @if(!$diagnostics['tenant_context']['is_initialized'])
        <div class="mt-4 p-4 bg-yellow-500/20 border border-yellow-500 rounded text-yellow-100 text-sm">
            <strong>⚠️ No tenant context!</strong> This means tenancy middleware isn't running or no tenant was found for this domain.
        </div>
    @endif
</div>

<!-- Tables Status -->
<div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-6">
    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
        <i class="fas fa-table mr-2 text-green-400"></i>
        Database Tables
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($diagnostics['tables'] as $table => $exists)
            <div class="bg-gray-900 p-4 rounded flex items-center justify-between">
                <div class="text-white">{{ str_replace('_', ' ', $table) }}</div>
                @if($exists)
                    <span class="text-green-400 text-lg"><i class="fas fa-check-circle"></i></span>
                @else
                    <span class="text-red-400 text-lg"><i class="fas fa-times-circle"></i></span>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Middleware Status -->
<div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-6">
    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
        <i class="fas fa-shield-alt mr-2 text-yellow-400"></i>
        Middleware Configuration
    </h2>
    @if(isset($diagnostics['middleware']['tenant_group_exists']))
        <div class="bg-gray-900 p-4 rounded mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-white">Tenant Middleware Group</span>
                @if($diagnostics['middleware']['tenant_group_exists'])
                    <span class="text-green-400"><i class="fas fa-check-circle"></i> Registered</span>
                @else
                    <span class="text-red-400"><i class="fas fa-times-circle"></i> Not Found</span>
                @endif
            </div>
            @if(!empty($diagnostics['middleware']['tenant_middleware']))
                <div class="text-gray-400 text-sm mt-2">
                    <div class="font-semibold mb-1">Middleware Stack:</div>
                    <ul class="list-disc list-inside space-y-1 font-mono text-xs">
                        @foreach($diagnostics['middleware']['tenant_middleware'] as $middleware)
                            <li>{{ $middleware }}</li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-yellow-400 text-sm mt-2">Middleware group is empty (tenancy disabled)</div>
            @endif
        </div>
    @endif
</div>

<!-- Tenants List -->
@if(isset($diagnostics['tenants_count']))
<div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-6">
    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
        <i class="fas fa-building mr-2 text-purple-400"></i>
        Tenants ({{ $diagnostics['tenants_count'] }})
    </h2>
    @if(count($diagnostics['tenants']) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-900 text-gray-400">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Plan</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach($diagnostics['tenants'] as $tenant)
                        <tr class="border-t border-gray-700">
                            <td class="px-4 py-2">{{ $tenant['name'] }}</td>
                            <td class="px-4 py-2">{{ $tenant['email'] }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs bg-blue-600">{{ $tenant['plan'] }}</span>
                            </td>
                            <td class="px-4 py-2">
                                @if($tenant['is_active'])
                                    <span class="text-green-400"><i class="fas fa-circle text-xs"></i> Active</span>
                                @else
                                    <span class="text-red-400"><i class="fas fa-circle text-xs"></i> Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 font-mono text-xs">{{ substr($tenant['id'], 0, 8) }}...</td>
                            <td class="px-4 py-2">
                                <button onclick="runTenantMigration('{{ $tenant['id'] }}')" 
                                        class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-database"></i> Migrate
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-400">No tenants found. Create the master tenant above.</p>
    @endif
</div>
@endif

<!-- Domains List -->
@if(isset($diagnostics['domains_count']))
<div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-6">
    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
        <i class="fas fa-globe mr-2 text-indigo-400"></i>
        Domains ({{ $diagnostics['domains_count'] }})
    </h2>
    @if(count($diagnostics['domains']) > 0)
        <div class="space-y-2">
            @foreach($diagnostics['domains'] as $domain)
                <div class="bg-gray-900 p-3 rounded flex items-center justify-between">
                    <div>
                        <div class="text-white font-semibold">{{ $domain['domain'] }}</div>
                        <div class="text-gray-400 text-xs font-mono">Tenant: {{ substr($domain['tenant_id'], 0, 8) }}...</div>
                    </div>
                    <div class="text-gray-500 text-xs">
                        {{ \Carbon\Carbon::parse($domain['created_at'])->diffForHumans() }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400">No domains configured.</p>
    @endif
</div>
@endif

<!-- Create Master Tenant Modal -->
<div id="create-tenant-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-lg p-8 max-w-md w-full border border-gray-700">
        <h3 class="text-2xl font-bold text-white mb-4">Create Master Tenant</h3>
        <form method="POST" action="{{ route('super-admin.diagnostics.create-master-tenant') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Tenant Name</label>
                <input type="text" name="name" value="SMBGen" required 
                       class="w-full bg-gray-900 text-white border border-gray-700 rounded px-4 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Email</label>
                <input type="email" name="email" value="admin@smbgen.com" required 
                       class="w-full bg-gray-900 text-white border border-gray-700 rounded px-4 py-2">
            </div>
            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Domain</label>
                <input type="text" name="domain" value="smbgen.com" required 
                       class="w-full bg-gray-900 text-white border border-gray-700 rounded px-4 py-2">
            </div>
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded">
                    Create Tenant
                </button>
                <button type="button" onclick="document.getElementById('create-tenant-modal').classList.add('hidden')" 
                        class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden form for tenant migrations -->
<form id="tenant-migration-form" method="POST" action="{{ route('super-admin.diagnostics.run-tenant-migrations') }}" class="hidden">
    @csrf
    <input type="hidden" name="tenant_id" id="tenant-migration-id">
</form>

<script>
function runTenantMigration(tenantId) {
    if (confirm('Run migrations for this tenant? This will create all tables in the tenant database.')) {
        document.getElementById('tenant-migration-id').value = tenantId;
        document.getElementById('tenant-migration-form').submit();
    }
}
</script>

@endsection
