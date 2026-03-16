@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">

    {{-- Header --}}
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Packages</h1>
            <p class="admin-page-subtitle">Client presentation packages and deliverables</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.packages.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>New Package
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filters --}}
    <div class="admin-card">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.packages.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-gray-400 mb-1 uppercase tracking-wider">Client</label>
                    <select name="client_id" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(request('client_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-36">
                    <label class="block text-xs font-medium text-gray-400 mb-1 uppercase tracking-wider">Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All statuses</option>
                        <option value="draft"  @selected(request('status') === 'draft')>Draft</option>
                        <option value="ready"  @selected(request('status') === 'ready')>Ready</option>
                        <option value="sent"   @selected(request('status') === 'sent')>Sent</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-filter mr-1.5"></i>Filter
                    </button>
                    @if(request()->hasAny(['client_id', 'status']))
                        <a href="{{ route('admin.packages.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-1.5"></i>Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Package Cards --}}
    @if($packages->isEmpty())
        <div class="admin-card">
            <div class="admin-card-body text-center py-16">
                <i class="fas fa-box-open text-4xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg mb-2">
                    @if(request()->hasAny(['client_id', 'status']))
                        No packages match your filters.
                    @else
                        No packages yet.
                    @endif
                </p>
                @if(!request()->hasAny(['client_id', 'status']))
                    <a href="{{ route('admin.packages.create') }}" class="btn-primary mt-2">
                        <i class="fas fa-plus mr-2"></i>Create your first package
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($packages as $package)
            @php
                $delivCount  = $package->deliverable_count;
                $researchCount = $package->research_and_data_count;
                $emailCount  = $package->email_template_count;
                $total       = $package->total_file_count;
            @endphp
            <a href="{{ route('admin.packages.show', $package) }}"
                class="admin-card hover:border-gray-500 transition-colors group block">
                <div class="admin-card-body">

                    {{-- Name + status --}}
                    <div class="flex justify-between items-start gap-3 mb-2">
                        <h3 class="text-gray-100 font-semibold text-base leading-tight group-hover:text-white transition-colors flex-1">
                            {{ $package->name }}
                        </h3>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium shrink-0 {{ $package->status_badge_class }}">
                            {{ ucfirst($package->status) }}
                        </span>
                    </div>

                    {{-- Client --}}
                    <p class="text-sm text-gray-400 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-user text-gray-600 text-xs"></i>
                        {{ $package->client->name ?? '—' }}
                    </p>

                    {{-- File type breakdown --}}
                    <div class="flex items-center gap-3 mb-4 text-xs">
                        @if($delivCount > 0)
                            <span class="flex items-center gap-1 text-purple-300">
                                <i class="fas fa-desktop text-purple-400"></i>
                                {{ $delivCount }}
                            </span>
                        @endif
                        @if($researchCount > 0)
                            <span class="flex items-center gap-1 text-green-300">
                                <i class="fas fa-file-alt text-green-400"></i>
                                {{ $researchCount }}
                            </span>
                        @endif
                        @if($emailCount > 0)
                            <span class="flex items-center gap-1 text-blue-300">
                                <i class="fas fa-envelope text-blue-400"></i>
                                {{ $emailCount }}
                            </span>
                        @endif
                        @if($total === 0)
                            <span class="text-gray-600">No files</span>
                        @else
                            <span class="text-gray-500 ml-auto">{{ $total }} total</span>
                        @endif
                    </div>

                    {{-- Footer: date + portal badge --}}
                    <div class="flex items-center justify-between pt-3 border-t border-gray-700/50">
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>{{ $package->created_at->format('M j, Y') }}
                        </span>
                        @if($package->portal_enabled)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-900/30 text-green-400 border border-green-700/50">
                                <i class="fas fa-globe mr-1"></i>Portal on
                            </span>
                        @endif
                    </div>

                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $packages->links() }}
        </div>
    @endif

</div>
@endsection
