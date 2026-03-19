@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Tenants</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">White-label agency portals running on this platform</p>
        </div>
        <button onclick="document.getElementById('create-tenant-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
            + New Tenant
        </button>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-500/10 border border-green-500/30 text-green-700 dark:text-green-300 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tenant table --}}
    <div class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Domain</th>
                    <th class="px-4 py-3 text-left">Plan</th>
                    <th class="px-4 py-3 text-left">Modules</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Created</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</div>
                            <div class="text-xs text-gray-500">{{ $tenant->owner_email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @foreach($tenant->domains as $domain)
                                <a href="http://{{ $domain->domain }}" target="_blank"
                                   class="text-primary-600 dark:text-primary-400 hover:underline text-xs">
                                    {{ $domain->domain }}
                                </a>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $tenant->plan === 'agency' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' :
                                   ($tenant->plan === 'scale' ? 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300' :
                                   ($tenant->plan === 'growth' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' :
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) }}">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($tenant->modules_enabled ?? [] as $mod)
                                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded uppercase">{{ $mod }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex w-2 h-2 rounded-full {{ $tenant->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $tenant->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.tenants.destroy', $tenant->id) }}"
                                  onsubmit="return confirm('Delete {{ $tenant->name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No tenants yet. Create one to spin up a white-label portal.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($tenants->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700/50">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Create tenant modal --}}
<div id="create-tenant-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Create New Tenant</h2>
            <button onclick="document.getElementById('create-tenant-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.tenants.store') }}" class="px-6 py-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                <input type="text" name="name" required
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Subdomain slug</label>
                <div class="flex rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600">
                    <input type="text" name="slug" required pattern="[a-z0-9\-]+"
                           placeholder="acme"
                           class="flex-1 px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <span class="px-3 py-2 text-xs text-gray-500 bg-gray-50 dark:bg-gray-700 border-l border-gray-300 dark:border-gray-600">.smbgen.com</span>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Owner Email</label>
                <input type="email" name="owner_email" required
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Plan</label>
                <select name="plan" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <option value="starter">Starter — CAST only</option>
                    <option value="growth">Growth — CAST + RELAY + SIGNAL</option>
                    <option value="scale">Scale — + SURGE + VAULT</option>
                    <option value="agency">Agency — All modules</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Create Tenant
                </button>
                <button type="button" onclick="document.getElementById('create-tenant-modal').classList.add('hidden')"
                        class="flex-1 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
