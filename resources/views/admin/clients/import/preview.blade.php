@extends('layouts.admin')

@section('title', 'Preview Import')

@section('content')
<div class="container px-4 py-8">
    <div>
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Preview Import</h1>
            <p class="text-gray-600 dark:text-gray-400">Review the data before importing</p>
        </div>

        <!-- Summary Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">File</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $import->filename }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Rows</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ count($data) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Valid Rows</p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">{{ count($validatedData) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Errors</p>
                    <p class="text-lg font-semibold text-red-600 dark:text-red-400">{{ count($errors) }}</p>
                </div>
            </div>
        </div>

        <!-- Errors Section -->
        @if(count($errors) > 0)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-red-900 dark:text-red-200 mb-3">
                    ⚠️ {{ count($errors) }} Row(s) with Errors
                </h3>
                <p class="text-red-800 dark:text-red-300 mb-4">The following rows have validation errors and will be skipped:</p>
                
                <div class="space-y-3 max-h-60 overflow-y-auto">
                    @foreach($errors as $rowIndex => $rowErrors)
                        <div class="bg-white dark:bg-gray-800 rounded-md p-3">
                            <p class="font-semibold text-red-900 dark:text-red-200 mb-1">Row {{ $rowIndex + 1 }}</p>
                            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300">
                                @foreach($rowErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Data Preview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Data Preview</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Row</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($data as $index => $row)
                            <tr class="{{ isset($errors[$index]) ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $row['name'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $row['email'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $row['phone'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($row['property_address'] ?? '-', 30) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if(isset($errors[$index]))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Error</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Valid</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center">
            <a 
                href="{{ route('clients.import.index') }}" 
                class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold rounded-lg transition duration-200"
            >
                Cancel
            </a>

            @if(count($validatedData) > 0)
                <form action="{{ route('clients.import.process', $import) }}" method="POST" class="inline">
                    @csrf
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold rounded-lg transition duration-200"
                        onclick="return confirm('Are you sure you want to import {{ count($validatedData) }} clients?')"
                    >
                        Import {{ count($validatedData) }} Client(s)
                    </button>
                </form>
            @else
                <button 
                    disabled
                    class="px-6 py-3 bg-gray-400 dark:bg-gray-600 text-gray-200 dark:text-gray-400 font-semibold rounded-lg cursor-not-allowed"
                >
                    No Valid Rows to Import
                </button>
            @endif
        </div>
    </div>
</div>
@endsection
