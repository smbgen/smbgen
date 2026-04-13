@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">File Management</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Overview of all client files and document management</p>
    </div>

    <!-- Storage Status Alert -->
    <x-storage-status-alert />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Files</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ \App\Models\ClientFile::count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-600/20 flex items-center justify-center">
                    <i class="fas fa-file text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Public Files</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ \App\Models\ClientFile::where('is_public', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-green-600/20 flex items-center justify-center">
                    <i class="fas fa-globe text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Private Files</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ \App\Models\ClientFile::where('is_public', false)->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-yellow-600/20 flex items-center justify-center">
                    <i class="fas fa-lock text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Clients with Files</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ \App\Models\Client::has('files')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-purple-600/20 flex items-center justify-center">
                    <i class="fas fa-users text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-{{ $usingCloudStorage ? '2' : '3' }} gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Storage Used</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        @php
                            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                            $bytes = $totalStorageUsed;
                            $i = 0;
                            while ($bytes >= 1024 && $i < 4) {
                                $bytes /= 1024;
                                $i++;
                            }
                        @endphp
                        {{ round($bytes, 2) }} {{ $units[$i] }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-cyan-600/20 flex items-center justify-center">
                    <i class="fas fa-database text-cyan-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Storage Type</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        @if($usingCloudStorage)
                            <span class="text-lg">Cloud Storage</span>
                        @else
                            <span class="text-lg">Local Storage</span>
                        @endif
                    </p>
                    @if($usingCloudStorage)
                        <p class="text-xs text-gray-600 dark:text-gray-500 mt-1">S3-compatible bucket</p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-lg bg-{{ $usingCloudStorage ? 'purple' : 'teal' }}-600/20 flex items-center justify-center">
                    <i class="fas fa-{{ $usingCloudStorage ? 'cloud' : 'hdd' }} text-{{ $usingCloudStorage ? 'purple' : 'teal' }}-400 text-xl"></i>
                </div>
            </div>
        </div>

        @if(!$usingCloudStorage)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Disk Usage</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                            {{ round(($diskTotalSpace - $diskFreeSpace) / $diskTotalSpace * 100, 1) }}%
                        </p>
                        <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full" 
                                 style="width: {{ min(100, round(($diskTotalSpace - $diskFreeSpace) / $diskTotalSpace * 100, 1)) }}%">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            {{ round($diskFreeSpace / 1024 / 1024 / 1024, 1) }} GB free of {{ round($diskTotalSpace / 1024 / 1024 / 1024, 1) }} GB
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-indigo-600/20 flex items-center justify-center">
                        <i class="fas fa-chart-pie text-indigo-400 text-xl"></i>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow">
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-900/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($clients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total Files</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Last Activity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($clients as $client)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                                {{ substr($client->name, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $client->name }}</div>
                                                @if($client->phone)
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ $client->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700 dark:text-gray-300">{{ $client->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-folder text-blue-400 mr-2"></i>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $client->files_count ?? 0 }}</span>
                                            <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">{{ Str::plural('file', $client->files_count ?? 0) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700 dark:text-gray-300">{{ $client->updated_at->format('M j, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $client->updated_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex gap-2">
                                            <a href="{{ route('clients.show', $client) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                            <a href="{{ route('admin.client.files', $client) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-400 hover:text-blue-300 hover:bg-blue-900/20 rounded-lg transition-colors">
                                                <i class="fas fa-folder-open mr-1"></i>Manage Files
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($clients, 'hasPages') && $clients->hasPages())
                    <div class="mt-6">
                        {{ $clients->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16">
                    <i class="fas fa-folder-open text-gray-400 dark:text-gray-600 text-6xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No clients found</h4>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Clients will appear here once created.</p>
                    <a href="{{ route('clients.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>Create First Client
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Admin Users Files Section -->
    <div class="mt-8 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Admin User Files</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Files uploaded by admin users not associated with specific clients</p>
                </div>
                <button onclick="toggleUploadForm()" class="btn-primary text-sm">
                    <i class="fas fa-upload mr-2"></i>Upload File
                </button>
            </div>

            <!-- Upload Form (Hidden by default) -->
            <div id="adminUploadForm" class="hidden mb-6 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Upload File for {{ auth()->user()->name }}</h4>
                
                <form action="{{ route('admin.user.files.upload', auth()->user()) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- File Input -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select File</label>
                            <input 
                                type="file" 
                                name="file" 
                                class="block w-full text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900/50 cursor-pointer" 
                                required 
                            />
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Max size: 100MB. Supported: PDF, DOCX, XLSX, images, etc.</p>
                        </div>

                        <!-- Visibility Toggle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">File Visibility</label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="is_public" value="0" checked class="w-4 h-4 text-indigo-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                        <i class="fas fa-lock text-yellow-500 mr-1"></i>Private
                                    </span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="is_public" value="1" class="w-4 h-4 text-indigo-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                        <i class="fas fa-globe text-green-500 mr-1"></i>Public
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (Optional)</label>
                            <textarea 
                                name="description" 
                                rows="3" 
                                class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                placeholder="Add a description or notes about this file..."
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-upload mr-2"></i>Upload File
                        </button>
                        <button type="button" onclick="toggleUploadForm()" class="btn-secondary">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current User's Recent Files -->
            @php
                $myFiles = \App\Models\ClientFile::where('user_id', auth()->id())
                    ->whereNull('client_id')
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp

            @if($myFiles->count() > 0)
                <div class="mb-6 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Your Recent Files</h4>
                        <a href="{{ route('admin.user.files', auth()->user()) }}" class="text-xs text-indigo-400 hover:text-indigo-300">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="space-y-2">
                        @foreach($myFiles as $file)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700/50 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center flex-1 min-w-0">
                                    <i class="fas {{ $file->getFileIcon() }} text-indigo-400 text-lg mr-3 flex-shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $file->original_name }}</p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $file->formatted_size }}</p>
                                            @if($file->is_public)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-900/30 text-green-400">
                                                    <i class="fas fa-globe mr-1"></i>Public
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-900/30 text-yellow-400">
                                                    <i class="fas fa-lock mr-1"></i>Private
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-500">{{ $file->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <a href="{{ route('admin.user.files.download', [auth()->user(), $file]) }}" 
                                       class="inline-flex items-center px-2 py-1 text-xs text-blue-400 hover:text-blue-300 hover:bg-blue-900/20 rounded transition-colors"
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('admin.user.files.destroy', [auth()->user(), $file]) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Delete this file permanently?')" 
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 text-xs text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded transition-colors"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
                $adminUsers = \App\Models\User::whereHas('uploadedFiles', function($query) {
                    $query->whereNull('client_id');
                })
                ->withCount(['uploadedFiles' => function($query) {
                    $query->whereNull('client_id');
                }])
                ->orderByDesc('uploaded_files_count')
                ->get();
            @endphp

            @if($adminUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total Files</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Last Upload</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($adminUsers as $user)
                                @php
                                    $lastUpload = \App\Models\ClientFile::where('user_id', $user->id)->whereNull('client_id')->latest()->first();
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                                @if($user->is_admin)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-900/50 text-purple-300 border border-purple-700">
                                                        <i class="fas fa-shield-alt mr-1"></i>Admin
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700 dark:text-gray-300">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-file text-indigo-400 mr-2"></i>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->uploaded_files_count }}</span>
                                            <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">{{ Str::plural('file', $user->uploaded_files_count) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($lastUpload)
                                            <div class="text-sm text-gray-700 dark:text-gray-300">{{ $lastUpload->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $lastUpload->created_at->diffForHumans() }}</div>
                                        @else
                                            <div class="text-sm text-gray-500">N/A</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.user.files', $user) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-400 hover:text-indigo-300 hover:bg-indigo-900/20 rounded-lg transition-colors">
                                            <i class="fas fa-folder-open mr-1"></i>View Files
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-gray-600 text-5xl mb-3"></i>
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No admin files found</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Admin users haven't uploaded any standalone files yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleUploadForm() {
    const form = document.getElementById('adminUploadForm');
    form.classList.toggle('hidden');
}
</script>
@endsection
