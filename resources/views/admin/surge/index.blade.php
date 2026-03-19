@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">SURGE — Deal Pipeline</h1>
            <p class="admin-page-subtitle">Track deals and leads through your full sales pipeline</p>
        </div>
        <button onclick="document.getElementById('modal-create-deal').classList.remove('hidden')" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>New Deal
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Total Deals</p>
            <p class="text-3xl font-black text-white">{{ $stats['total_deals'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Pipeline Value</p>
            <p class="text-3xl font-black text-orange-400">${{ number_format($stats['pipeline_value'], 0) }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Won Value</p>
            <p class="text-3xl font-black text-emerald-400">${{ number_format($stats['won_value'], 0) }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Won Deals</p>
            <p class="text-3xl font-black text-emerald-400">{{ $stats['won_count'] }}</p>
        </div>
    </div>

    <!-- Kanban Pipeline -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach($stages as $stage)
            @php $stageDeals = $dealsByStage[$stage->value]; @endphp
            <div class="bg-gray-800/60 rounded-xl border border-gray-700 p-3">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black uppercase tracking-widest
                        {{ match($stage->color()) {
                            'blue'    => 'text-blue-400',
                            'violet'  => 'text-violet-400',
                            'amber'   => 'text-amber-400',
                            'orange'  => 'text-orange-400',
                            'emerald' => 'text-emerald-400',
                            'red'     => 'text-red-400',
                        } }}">{{ $stage->label() }}</span>
                    <span class="text-gray-500 text-xs">{{ $stageDeals->count() }}</span>
                </div>
                <div class="space-y-2">
                    @foreach($stageDeals as $deal)
                        <div class="bg-gray-700 rounded-lg p-3 text-xs">
                            <p class="text-white font-bold truncate mb-1">{{ $deal->title }}</p>
                            <p class="text-gray-400">${{ number_format($deal->value, 0) }}</p>
                            @if($deal->client)
                                <p class="text-gray-500 truncate mt-1">{{ $deal->client->name }}</p>
                            @endif
                            <form method="POST" action="{{ route('admin.surge.update', $deal) }}" class="mt-2">
                                @csrf @method('PATCH')
                                <select name="stage" class="w-full text-xs bg-gray-600 border-0 rounded text-gray-300 py-1" onchange="this.form.submit()">
                                    @foreach($stages as $s)
                                        <option value="{{ $s->value }}" {{ $deal->stage === $s ? 'selected' : '' }}>
                                            {{ $s->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Create Deal Modal -->
<div id="modal-create-deal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8 w-full max-w-lg">
        <h3 class="text-white font-black text-xl mb-6">New Deal</h3>
        <form method="POST" action="{{ route('admin.surge.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-input w-full" required placeholder="e.g. Website + Marketing Package">
                </div>
                <div>
                    <label class="form-label">Value ($)</label>
                    <input type="number" name="value" class="form-input w-full" min="0" step="0.01" required placeholder="0.00">
                </div>
                <div>
                    <label class="form-label">Stage</label>
                    <select name="stage" class="form-select w-full" required>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                        @endforeach
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
                <button type="submit" class="btn-primary flex-1">Create Deal</button>
                <button type="button" onclick="document.getElementById('modal-create-deal').classList.add('hidden')" class="btn-secondary flex-1">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
