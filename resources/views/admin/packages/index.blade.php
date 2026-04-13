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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client</label>
                    <select name="client_id" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(request('client_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All statuses</option>
                        <option value="draft"  @selected(request('status') === 'draft')>Draft</option>
                        <option value="ready"  @selected(request('status') === 'ready')>Ready</option>
                        <option value="sent"   @selected(request('status') === 'sent')>Sent</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-primary">Filter</button>
                    @if(request()->hasAny(['client_id', 'status']))
                        <a href="{{ route('admin.packages.index') }}" class="btn-secondary">Clear</a>
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
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">No packages yet.</p>
                <a href="{{ route('admin.packages.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Create your first package
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($packages as $package)
            <div class="admin-card hover:border-gray-500 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.packages.show', $package) }}'">
                <div class="admin-card-body">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-gray-900 dark:text-gray-100 font-semibold text-base leading-tight">{{ $package->name }}</h3>
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $package->status_badge_class }}">
                            {{ ucfirst($package->status) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        <i class="fas fa-user mr-1"></i>{{ $package->client->name ?? '—' }}
                    </p>

                    {{-- File summary --}}
                    <div class="flex flex-wrap gap-2 mb-3 text-xs text-gray-600 dark:text-gray-400">
                        @if($package->deliverable_count > 0)
                            <span><i class="fas fa-desktop mr-1 text-purple-400"></i>{{ $package->deliverable_count }} deliverable{{ $package->deliverable_count !== 1 ? 's' : '' }}</span>
                        @endif
                        @if($package->research_count > 0)
                            <span><i class="fas fa-file-alt mr-1 text-green-400"></i>{{ $package->research_count }} research</span>
                        @endif
                        @if($package->email_template_count > 0)
                            <span><i class="fas fa-envelope mr-1 text-blue-400"></i>{{ $package->email_template_count }} email{{ $package->email_template_count !== 1 ? 's' : '' }}</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600 dark:text-gray-500">{{ $package->created_at->format('M j, Y') }}</span>
                        <div class="flex items-center gap-2">
                            @if($package->portal_enabled)
                                <span class="text-xs px-2 py-0.5 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-300 dark:border-green-700">
                                    <i class="fas fa-globe mr-1"></i>Portal on
                                </span>
                            @endif
                            <a href="{{ route('admin.packages.show', $package) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300" onclick="event.stopPropagation()">
                                View <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $packages->links() }}
        </div>
    @endif

</div>
@endsection
