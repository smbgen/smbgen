@extends('layouts.client')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('client.presentations.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                <span aria-hidden="true" class="mr-2">←</span>Back to Presentations
            </a>
            <h1 class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ $package->name }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Shared on {{ $package->created_at->format('F j, Y') }} for your account.</p>
        </div>
        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $package->status_badge_class }}">
            {{ ucfirst($package->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($visibleFiles as $file)
            <div class="rounded-xl border border-gray-300 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/40">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $file->display_name }}</p>
                        <p class="mt-1 text-xs font-mono text-gray-500 dark:text-gray-400">{{ $file->original_name }}</p>
                    </div>
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $file->type_badge_class }}">
                        {{ str_replace('_', ' ', $file->type) }}
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>{{ $file->formatted_size }}</span>
                    <span>{{ $file->created_at->format('M j, Y') }}</span>
                </div>

                <a href="{{ route('client.presentations.files.preview', [$package, $file]) }}"
                   target="_blank"
                   rel="noreferrer"
                   class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/70">
                    {{ in_array($file->type, ['WORD_DOCUMENT', 'POWERPOINT'], true) ? 'Download File' : 'Open File' }}
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection