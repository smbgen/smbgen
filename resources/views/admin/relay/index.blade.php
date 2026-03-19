@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">RELAY — Email Sequences</h1>
            <p class="admin-page-subtitle">Build and manage automated email nurture sequences</p>
        </div>
        <button onclick="document.getElementById('modal-create-sequence').classList.remove('hidden')" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>New Sequence
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Total Sequences</p>
            <p class="text-3xl font-black text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Active</p>
            <p class="text-3xl font-black text-cyan-400">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Enrolled Contacts</p>
            <p class="text-3xl font-black text-emerald-400">{{ $stats['enrolled'] }}</p>
        </div>
    </div>

    <!-- Sequences List -->
    <div class="space-y-3">
        @forelse($sequences as $sequence)
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-5 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-white font-bold">{{ $sequence->name }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                            {{ $sequence->status === 'active' ? 'bg-emerald-900/50 text-emerald-400' : 'bg-gray-700 text-gray-400' }}">
                            {{ $sequence->status }}
                        </span>
                        <span class="text-gray-500 text-xs">{{ $sequence->trigger->label() }}</span>
                    </div>
                    @if($sequence->description)
                        <p class="text-gray-400 text-sm">{{ $sequence->description }}</p>
                    @endif
                    <div class="flex gap-4 mt-2 text-xs text-gray-500">
                        <span>{{ $sequence->steps_count }} steps</span>
                        <span>{{ $sequence->enrollments_count }} enrollments</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="document.getElementById('modal-enroll-{{ $sequence->id }}').classList.remove('hidden')"
                            class="btn-secondary text-xs">Enroll Contact</button>
                    <form method="POST" action="{{ route('admin.relay.destroy', $sequence) }}" onsubmit="return confirm('Delete this sequence and all its data?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-bold">Delete</button>
                    </form>
                </div>
            </div>

            <!-- Enroll Modal per sequence -->
            <div id="modal-enroll-{{ $sequence->id }}" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
                <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8 w-full max-w-md">
                    <h3 class="text-white font-black text-lg mb-5">Enroll in: {{ $sequence->name }}</h3>
                    <form method="POST" action="{{ route('admin.relay.enroll', $sequence) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-input w-full" required>
                            </div>
                            <div>
                                <label class="form-label">Name (optional)</label>
                                <input type="text" name="contact_name" class="form-input w-full">
                            </div>
                        </div>
                        <div class="flex gap-3 mt-5">
                            <button type="submit" class="btn-primary flex-1">Enroll</button>
                            <button type="button" onclick="document.getElementById('modal-enroll-{{ $sequence->id }}').classList.add('hidden')" class="btn-secondary flex-1">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-10 text-center text-gray-500">
                No sequences yet. Create your first drip sequence above.
            </div>
        @endforelse
    </div>
</div>

<!-- Create Sequence Modal -->
<div id="modal-create-sequence" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8 w-full max-w-lg">
        <h3 class="text-white font-black text-xl mb-6">New Email Sequence</h3>
        <form method="POST" action="{{ route('admin.relay.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Sequence Name</label>
                    <input type="text" name="name" class="form-input w-full" required placeholder="e.g. New Lead Nurture">
                </div>
                <div>
                    <label class="form-label">Description (optional)</label>
                    <input type="text" name="description" class="form-input w-full" placeholder="What does this sequence do?">
                </div>
                <div>
                    <label class="form-label">Trigger</label>
                    <select name="trigger" class="form-select w-full" required>
                        <option value="manual">Manual Enroll</option>
                        <option value="lead_capture">Lead Capture</option>
                        <option value="client_created">New Client</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-primary flex-1">Create Sequence</button>
                <button type="button" onclick="document.getElementById('modal-create-sequence').classList.add('hidden')" class="btn-secondary flex-1">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
