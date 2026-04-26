@extends('layouts.client')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-800 rounded-xl shadow p-6 overflow-hidden">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Client Presentations</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Portal-enabled presentations and deliverables shared specifically with your account.</p>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ $packages->total() }} presentation{{ $packages->total() === 1 ? '' : 's' }} available
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse($packages as $package)
                <a href="{{ route('client.presentations.show', $package) }}" class="block rounded-xl border border-gray-300 bg-white p-5 transition hover:border-indigo-300 hover:bg-indigo-50/40 dark:border-gray-800 dark:bg-gray-900/40 dark:hover:border-indigo-700 dark:hover:bg-indigo-900/10">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $package->name }}</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Shared {{ $package->created_at->format('M j, Y') }}</p>
                        </div>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $package->status_badge_class }}">
                            {{ ucfirst($package->status) }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="text-gray-700 dark:text-gray-300">{{ $package->visible_deliverables_count }} visible {{ \Illuminate\Support\Str::plural('file', $package->visible_deliverables_count) }}</span>
                        <span class="font-medium text-indigo-600 dark:text-indigo-300">Open</span>
                    </div>
                </a>
            @empty
                <div class="lg:col-span-2 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 px-6 py-12 text-center text-gray-600 dark:text-gray-400">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-xl dark:bg-gray-800">🖥️</div>
                    No presentations are available in your portal yet.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $packages->links() }}
        </div>
    </div>
</div>
@endsection