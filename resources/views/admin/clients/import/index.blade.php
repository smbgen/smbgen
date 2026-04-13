@extends('layouts.admin')

@section('title', 'Import Clients')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Import Clients</h1>
            <p class="text-gray-600 dark:text-gray-400">Upload a CSV file to import multiple clients at once</p>
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

        <!-- Upload Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Upload File</h2>
            
            <form action="{{ route('clients.import.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select CSV or Phone Contacts File
                    </label>
                    <input 
                        type="file" 
                        name="csv_file" 
                        id="csv_file" 
                        accept=".csv,.txt,.vcf,.vcard"
                        required
                        class="block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/20 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/30"
                    >
                    @error('csv_file')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Supported: CSV, TXT, VCF (vCard) • Maximum file size: 10MB</p>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200"
                >
                    Upload and Preview
                </button>
            </form>
        </div>

        <!-- Import Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Phone Contacts -->
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                <div class="flex items-start mb-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-green-900 dark:text-green-200 mb-2">Import from Phone</h3>
                        <p class="text-sm text-green-800 dark:text-green-300 mb-3">Upload contacts directly from your iPhone or Android device</p>
                        <ul class="space-y-2 text-sm text-green-700 dark:text-green-300">
                            <li class="flex items-start">
                                <span class="font-semibold mr-2 min-w-[80px]">iPhone:</span>
                                <span>Settings → Contacts → Export vCard</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold mr-2 min-w-[80px]">Android:</span>
                                <span>Contacts → Menu → Export → VCF file</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold mr-2 min-w-[80px]">Format:</span>
                                <span>.vcf or .vcard file</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- CSV Import -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                <div class="flex items-start mb-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-2">Import from CSV</h3>
                        <p class="text-sm text-blue-800 dark:text-blue-300 mb-3">Use a spreadsheet to import multiple clients at once</p>
                        <a href="{{ asset('examples/clients-import-template.csv') }}" download class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition duration-200">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download CSV Template
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CSV Format Guide -->
        <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-3">CSV Format Requirements</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">Your CSV file should include the following columns (header row required):</p>
            
            <div class="bg-white dark:bg-gray-800 rounded-md p-4 mb-4">
                <code class="text-sm text-gray-900 dark:text-gray-100">
                    name,email,phone,property_address,notes,source_site
                </code>
            </div>

            <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <li class="flex items-start">
                    <span class="font-semibold mr-2 min-w-[160px]">name:</span>
                    <span>Required - Client's full name</span>
                </li>
                <li class="flex items-start">
                    <span class="font-semibold mr-2 min-w-[160px]">email:</span>
                    <span>Optional - Client's email address</span>
                </li>
                <li class="flex items-start">
                    <span class="font-semibold mr-2 min-w-[160px]">phone:</span>
                    <span>Optional - Client's phone number</span>
                </li>
                <li class="flex items-start">
                    <span class="font-semibold mr-2 min-w-[160px]">property_address:</span>
                    <span>Optional - Property address</span>
                </li>
                <li class="flex items-start">
                    <span class="font-semibold mr-2 min-w-[160px]">notes:</span>
                    <span>Optional - Any additional notes</span>
                </li>
                <li class="flex items-start">
                    <span class="font-semibold mr-2 min-w-[160px]">source_site:</span>
                    <span>Optional - Source website or system</span>
                </li>
            </ul>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>Example row:</strong><br>
                    <code class="text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded">John Doe,john@example.com,555-1234,123 Main St,New customer,Website</code>
                </p>
            </div>
        </div>

        <!-- Recent Imports -->
        @if($recentImports->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Imports</h2>
                    <a href="{{ route('clients.import.history') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                        View All History
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Results</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentImports as $import)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $import->filename }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $import->created_at->format('M d, Y g:i A') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($import->status === 'completed')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Completed</span>
                                        @elseif($import->status === 'failed')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Failed</span>
                                        @elseif($import->status === 'processing')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Processing</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        @if($import->status === 'completed')
                                            <span class="text-green-600 dark:text-green-400">{{ $import->successful_imports }} succeeded</span>
                                            @if($import->failed_imports > 0)
                                                <span class="text-red-600 dark:text-red-400">, {{ $import->failed_imports }} failed</span>
                                            @endif
                                        @else
                                            {{ $import->total_rows }} rows
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
