@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-100">Files for {{ $client->name }}</h2>
            <p class="text-sm text-gray-400 mt-1">Manage documents and media files</p>
        </div>
        <a href="{{ route('clients.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Clients
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-900/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-900/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- File Upload Card -->
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-gray-100">Upload New File</h3>
        </div>

        <div class="p-6">
            <!-- Storage Status Alert -->
            <x-storage-status-alert />

            <form action="{{ route('admin.client.files.upload', $client) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- File Input -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Select File</label>
                        <input 
                            type="file" 
                            name="file" 
                            class="block w-full text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-600 file:text-white hover:file:bg-blue-500 file:cursor-pointer border border-gray-700 rounded-lg bg-gray-900/50 cursor-pointer" 
                            required 
                        />
                        <p class="text-xs text-gray-400 mt-2">Max size: 100MB. Supported: PDF, DOCX, XLSX, images, etc.</p>
                    </div>

                    <!-- Visibility Toggle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">File Visibility</label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" name="is_public" value="0" checked class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-300 group-hover:text-gray-100">
                                    <i class="fas fa-lock text-yellow-500 mr-1"></i>Private
                                </span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" name="is_public" value="1" class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-300 group-hover:text-gray-100">
                                    <i class="fas fa-globe text-green-500 mr-1"></i>Public
                                </span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <strong>Private:</strong> Secure storage, requires authentication<br>
                            <strong>Public:</strong> Accessible via direct URL
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
                        <textarea 
                            name="description" 
                            rows="3" 
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            placeholder="Add a description or notes about this file..."
                        ></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-upload mr-2"></i>Upload File
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-md px-6 py-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Visibility</label>
                <select name="visibility" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Files</option>
                    <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Public Only</option>
                    <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Private Only</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">File Type</label>
                <select name="category" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Types</option>
                    <option value="document" {{ request('category') === 'document' ? 'selected' : '' }}>Documents</option>
                    <option value="image" {{ request('category') === 'image' ? 'selected' : '' }}>Images</option>
                    <option value="spreadsheet" {{ request('category') === 'spreadsheet' ? 'selected' : '' }}>Spreadsheets</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="btn-primary text-sm">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>

            @if(request('visibility') || request('category'))
                <div class="flex items-end">
                    <a href="{{ route('admin.client.files', $client) }}" class="btn-secondary text-sm">
                        Clear Filters
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Files Table -->
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg overflow-hidden">
        @if($files->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Visibility</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Uploaded By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Uploaded</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($files as $file)
                            <tr class="hover:bg-gray-700/50 transition-colors">
                                <!-- File Name with Icon -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="fas {{ $file->getFileIcon() }} text-blue-400 text-xl mr-3"></i>
                                        <div>
                                            <div class="font-medium text-gray-100">{{ Str::limit($file->original_name, 40) }}</div>
                                            @if($file->description)
                                                <div class="text-xs text-gray-400 mt-1">{{ Str::limit($file->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- File Type -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-700 text-gray-300">
                                        {{ strtoupper($file->file_extension ?? 'N/A') }}
                                    </span>
                                </td>

                                <!-- File Size -->
                                <td class="px-6 py-4 text-gray-300 text-sm">
                                    {{ $file->formatted_size }}
                                </td>

                                <!-- Visibility -->
                                <td class="px-6 py-4">
                                    @if($file->is_public)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-900/30 text-green-400 border border-green-700">
                                            <i class="fas fa-globe mr-1"></i>Public
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-yellow-900/30 text-yellow-400 border border-yellow-700">
                                            <i class="fas fa-lock mr-1"></i>Private
                                        </span>
                                    @endif
                                </td>

                                <!-- Uploaded By -->
                                <td class="px-6 py-4">
                                    @if($file->uploader)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                                {{ substr($file->uploader->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-300">{{ $file->uploader->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $file->uploader->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Unknown</span>
                                    @endif
                                </td>

                                <!-- Upload Date -->
                                <td class="px-6 py-4">
                                    <div class="text-gray-300 text-sm">{{ $file->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $file->created_at->format('g:i A') }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex gap-2">
                                        @if($file->is_public)
                                            <button onclick="copyToClipboard('{{ $file->getPublicUrl() }}', this)" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-400 hover:text-green-300 hover:bg-green-900/20 rounded-lg transition-colors" title="Copy public link">
                                                <i class="fas fa-link mr-1"></i>Copy Link
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('admin.client.files.download', [$client, $file]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-400 hover:text-blue-300 hover:bg-blue-900/20 rounded-lg transition-colors">
                                            <i class="fas fa-download mr-1"></i>Download
                                        </a>

                                        <form action="{{ route('admin.client.files.destroy', [$client, $file]) }}" method="POST" onsubmit="return confirm('Delete this file permanently?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded-lg transition-colors">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($files->hasPages())
                <div class="px-6 py-4 border-t border-gray-700">
                    {{ $files->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <i class="fas fa-folder-open text-gray-600 text-6xl mb-4"></i>
                <h4 class="text-xl font-semibold text-gray-300 mb-2">No files found</h4>
                <p class="text-gray-400 mb-6">
                    @if(request('visibility') || request('category'))
                        No files match your current filters.
                    @else
                        Upload your first file to get started.
                    @endif
                </p>
                @if(request('visibility') || request('category'))
                    <a href="{{ route('admin.client.files', $client) }}" class="btn-secondary">
                        Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(function() {
        // Store original content
        const originalHTML = button.innerHTML;
        
        // Show success feedback
        button.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
        button.classList.remove('text-green-400', 'hover:text-green-300');
        button.classList.add('text-green-300');
        
        // Reset after 2 seconds
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('text-green-300');
            button.classList.add('text-green-400', 'hover:text-green-300');
        }, 2000);
    }).catch(function(err) {
        alert('Failed to copy link: ' + err);
    });
}
</script>
@endpush


