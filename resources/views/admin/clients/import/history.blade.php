@extends('layouts.admin')

@section('title', 'Import History')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Import History</h1>
                <p class="text-gray-600 dark:text-gray-400">View all your client import history</p>
            </div>
            <a href="{{ route('clients.import.index') }}" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                New Import
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Import History Table -->
        @if($imports->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Rows</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Successful</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Failed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($imports as $import)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        <div class="font-medium">{{ $import->filename }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div>{{ $import->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs">{{ $import->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($import->status === 'completed')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                Completed
                                            </span>
                                        @elseif($import->status === 'failed')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                Failed
                                            </span>
                                        @elseif($import->status === 'processing')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                Processing
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $import->total_rows }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($import->successful_imports > 0)
                                            <span class="text-green-600 dark:text-green-400 font-medium">{{ $import->successful_imports }}</span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">0</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($import->failed_imports > 0)
                                            <span class="text-red-600 dark:text-red-400 font-medium">{{ $import->failed_imports }}</span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">0</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($import->errors && count($import->errors) > 0)
                                            <button 
                                                onclick="showErrors{{ $import->id }}()"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                            >
                                                View Errors
                                            </button>
                                            
                                            <script>
                                                function showErrors{{ $import->id }}() {
                                                    const errors = @json($import->errors);
                                                    let errorHtml = '<div class="space-y-2">';
                                                    errors.forEach((error, index) => {
                                                        errorHtml += `<div class="text-sm"><strong>Row ${error.row || index + 1}:</strong> ${error.message || error}</div>`;
                                                    });
                                                    errorHtml += '</div>';
                                                    
                                                    // Show in a modal or alert
                                                    if (confirm('View errors in browser console?')) {
                                                        console.log('Import Errors:', errors);
                                                    }
                                                }
                                            </script>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($imports->hasPages())
                    <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $imports->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Import History</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">You haven't imported any clients yet</p>
                <a href="{{ route('clients.import.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold rounded-lg transition duration-200">
                    Start Your First Import
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
