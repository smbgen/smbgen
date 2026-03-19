@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">CAST — Managed Sites</h1>
            <p class="admin-page-subtitle">Track and manage all client websites delivered on the platform</p>
        </div>
        <button onclick="document.getElementById('modal-create-site').classList.remove('hidden')" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Add Site
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Total Sites</p>
            <p class="text-3xl font-black text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Live</p>
            <p class="text-3xl font-black text-emerald-400">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">In Build</p>
            <p class="text-3xl font-black text-amber-400">{{ $stats['building'] }}</p>
        </div>
    </div>

    <!-- Sites Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($sites as $site)
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-5 flex flex-col gap-3">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-white font-bold">{{ $site->name }}</p>
                        @if($site->domain)
                            <a href="https://{{ $site->domain }}" target="_blank" class="text-emerald-400 text-xs hover:underline">{{ $site->domain }}</a>
                        @else
                            <span class="text-gray-500 text-xs">No domain set</span>
                        @endif
                    </div>
                    <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                        {{ match($site->status->value) {
                            'active'   => 'bg-emerald-900/50 text-emerald-400',
                            'building' => 'bg-amber-900/50 text-amber-400',
                            'paused'   => 'bg-gray-700 text-gray-400',
                        } }}">{{ $site->status->value }}</span>
                </div>

                @if($site->client)
                    <p class="text-gray-400 text-xs"><i class="fas fa-user mr-1"></i>{{ $site->client->name }}</p>
                @endif
                @if($site->notes)
                    <p class="text-gray-500 text-xs">{{ $site->notes }}</p>
                @endif
                @if($site->launched_at)
                    <p class="text-gray-500 text-xs">Launched {{ $site->launched_at->format('M d, Y') }}</p>
                @endif

                <form method="POST" action="{{ route('admin.cast.destroy', $site) }}" onsubmit="return confirm('Remove this site?')" class="mt-auto pt-2 border-t border-gray-700">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-bold">Remove</button>
                </form>
            </div>
        @empty
            <div class="col-span-3 bg-gray-800 rounded-xl border border-gray-700 p-10 text-center text-gray-500">
                No sites tracked yet. Add your first managed site above.
            </div>
        @endforelse
    </div>
</div>

<!-- Add Site Modal -->
<div id="modal-create-site" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8 w-full max-w-lg">
        <h3 class="text-white font-black text-xl mb-6">Add Managed Site</h3>
        <form method="POST" action="{{ route('admin.cast.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Site Name</label>
                    <input type="text" name="name" class="form-input w-full" required placeholder="e.g. Acme Corp Website">
                </div>
                <div>
                    <label class="form-label">Domain (optional)</label>
                    <input type="text" name="domain" class="form-input w-full" placeholder="e.g. acme.com">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select w-full" required>
                        <option value="building">Building</option>
                        <option value="active">Active / Live</option>
                        <option value="paused">Paused</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Client (optional)</label>
                    <select name="client_id" class="form-select w-full">
                        <option value="">— No client —</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Notes (optional)</label>
                    <textarea name="notes" rows="2" class="form-input w-full"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-primary flex-1">Add Site</button>
                <button type="button" onclick="document.getElementById('modal-create-site').classList.add('hidden')" class="btn-secondary flex-1">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
