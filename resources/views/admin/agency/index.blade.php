@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Agency Portals</h1>
            <p class="admin-page-subtitle">Manage white-label agency portal instances</p>
        </div>
        <button onclick="document.getElementById('modal-create-portal').classList.remove('hidden')" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>New Portal
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Total Portals</p>
            <p class="text-3xl font-black text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Active</p>
            <p class="text-3xl font-black text-emerald-400">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Sites Managed</p>
            <p class="text-3xl font-black text-indigo-400">{{ $stats['sites'] }}</p>
        </div>
    </div>

    <!-- Portals -->
    <div class="space-y-3">
        @forelse($portals as $portal)
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-5 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-white font-bold">{{ $portal->name }}</span>
                        <span class="text-gray-500 text-xs font-mono">/{{ $portal->slug }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                            {{ $portal->status === 'active' ? 'bg-emerald-900/50 text-emerald-400' : 'bg-gray-700 text-gray-400' }}">
                            {{ $portal->status }}
                        </span>
                    </div>
                    <div class="flex gap-4 text-xs text-gray-500">
                        <span>Owner: {{ $portal->owner?->name ?? '—' }}</span>
                        <span>{{ $portal->sites->count() }} / {{ $portal->max_client_sites }} sites</span>
                        <span>Created {{ $portal->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.agency.destroy', $portal) }}" onsubmit="return confirm('Delete this portal?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-bold">Delete</button>
                </form>
            </div>
        @empty
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-10 text-center text-gray-500">
                No agency portals yet. Create the first one above.
            </div>
        @endforelse
    </div>
</div>

<!-- Create Portal Modal -->
<div id="modal-create-portal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8 w-full max-w-lg">
        <h3 class="text-white font-black text-xl mb-6">New Agency Portal</h3>
        <form method="POST" action="{{ route('admin.agency.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Portal Name</label>
                    <input type="text" name="name" class="form-input w-full" required placeholder="e.g. L7 Labs Portal">
                </div>
                <div>
                    <label class="form-label">Max Client Sites</label>
                    <input type="number" name="max_client_sites" value="10" min="1" max="100" class="form-input w-full" required>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-primary flex-1">Create Portal</button>
                <button type="button" onclick="document.getElementById('modal-create-portal').classList.add('hidden')" class="btn-secondary flex-1">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
