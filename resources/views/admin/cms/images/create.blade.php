@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-100">Upload CMS Images</h2>
            <p class="text-sm text-gray-400 mt-1">Add images for use in CMS content</p>
        </div>
        <a href="{{ route('admin.cms.images.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Images
        </a>
    </div>

    <!-- Upload Form -->
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-gray-100">Upload Images</h3>
            <p class="text-sm text-gray-400 mt-1">Select multiple images to upload at once</p>
        </div>

        <div class="p-6">
            <form id="uploadForm" action="{{ route('admin.cms.images.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Image Upload Zone -->
                <div class="mb-6">
                    <label for="images" class="block text-sm font-medium text-gray-300 mb-2">
                        Image Files <span class="text-red-500">*</span>
                    </label>
                    <div id="uploadZone" class="relative">
                        <div class="flex justify-center px-6 pt-8 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-blue-500 transition-all duration-200 cursor-pointer"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleDrop(event)">
                            <div class="space-y-3 text-center">
                                <div class="flex justify-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                                </div>
                                <div class="flex text-sm text-gray-400 justify-center items-center space-x-2">
                                    <label for="images" class="relative cursor-pointer">
                                        <span class="font-medium text-blue-400 hover:text-blue-300 transition-colors">
                                            Click to browse
                                        </span>
                                        <input id="images" name="images[]" type="file" accept="image/*" multiple class="sr-only" required>
                                    </label>
                                    <span>or drag and drop</span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PNG, JPG, GIF, WebP up to 5MB each • Multiple files supported
                                </p>
                                <div id="fileCount" class="text-xs text-blue-400 hidden">
                                    <span id="selectedCount">0</span> files selected
                                </div>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div id="uploadProgress" class="hidden absolute inset-0 bg-gray-900 bg-opacity-90 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-4"></div>
                                <p class="text-gray-300">Uploading images...</p>
                                <div class="w-64 bg-gray-700 rounded-full h-2 mt-2">
                                    <div id="progressBar" class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <p id="progressText" class="text-sm text-gray-400 mt-1">0%</p>
                            </div>
                        </div>
                    </div>
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Selected Files Preview -->
                <div id="filePreview" class="mb-6 hidden">
                    <label class="block text-sm font-medium text-gray-300 mb-3">Selected Images</label>
                    <div id="previewGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>
                </div>

                <!-- Bulk Alt Text Option -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Bulk Alt Text (Optional)
                    </label>
                    <input type="text" name="bulk_alt_text" id="bulk_alt_text"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Apply this alt text to all images (can be overridden individually)">
                    <p class="mt-1 text-xs text-gray-500">
                        This will be applied to all images. You can edit individual alt text after upload.
                    </p>
                </div>

                <!-- Submit -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.cms.images.index') }}" class="btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" id="uploadBtn" class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-upload mr-2"></i>Upload Images
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Uploads -->
    <div class="mt-8">
        <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-gray-100">Recent Uploads</h3>
                <p class="text-sm text-gray-400 mt-1">Quick access to your recently uploaded images</p>
            </div>
            <div class="p-6">
                <div id="recentUploads" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <!-- Recent uploads will be loaded here -->
                </div>
                <div id="noRecent" class="text-center py-8 text-gray-400">
                    <i class="fas fa-history text-3xl mb-2"></i>
                    <p>No recent uploads</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedFiles = [];
let filePreviews = [];

document.getElementById('images').addEventListener('change', function(e) {
    handleFiles(e.target.files);
});

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadZone').firstElementChild.classList.add('border-blue-500', 'bg-blue-500', 'bg-opacity-5');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadZone').firstElementChild.classList.remove('border-blue-500', 'bg-blue-500', 'bg-opacity-5');
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadZone').firstElementChild.classList.remove('border-blue-500', 'bg-blue-500', 'bg-opacity-5');

    const files = e.dataTransfer.files;
    handleFiles(files);
}

function handleFiles(files) {
    selectedFiles = Array.from(files).filter(file => file.type.startsWith('image/'));

    if (selectedFiles.length > 0) {
        document.getElementById('fileCount').classList.remove('hidden');
        document.getElementById('selectedCount').textContent = selectedFiles.length;
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('uploadBtn').disabled = false;

        updateFilePreviews();
    } else {
        document.getElementById('fileCount').classList.add('hidden');
        document.getElementById('filePreview').classList.add('hidden');
        document.getElementById('uploadBtn').disabled = true;
    }
}

function updateFilePreviews() {
    const previewGrid = document.getElementById('previewGrid');
    previewGrid.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const previewDiv = document.createElement('div');
        previewDiv.className = 'relative bg-gray-700 rounded-lg overflow-hidden border border-gray-600';

        const reader = new FileReader();
        reader.onload = function(e) {
            previewDiv.innerHTML = `
                <div class="aspect-square bg-gray-600 flex items-center justify-center overflow-hidden">
                    <img src="${e.target.result}" alt="${file.name}" class="w-full h-full object-cover">
                </div>
                <div class="p-2">
                    <p class="text-xs text-gray-300 truncate" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                    <input type="text" name="alt_texts[]" placeholder="Alt text (optional)"
                           class="w-full mt-1 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-xs text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <button type="button" onclick="removeFile(${index})"
                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            `;
        };
        reader.readAsDataURL(file);

        previewGrid.appendChild(previewDiv);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    document.getElementById('selectedCount').textContent = selectedFiles.length;

    if (selectedFiles.length === 0) {
        document.getElementById('fileCount').classList.add('hidden');
        document.getElementById('filePreview').classList.add('hidden');
        document.getElementById('uploadBtn').disabled = true;
    } else {
        updateFilePreviews();
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// Load recent uploads on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRecentUploads();
});

function loadRecentUploads() {
    // This would typically make an AJAX call to get recent uploads
    // For now, we'll show a placeholder
    const recentUploads = document.getElementById('recentUploads');
    const noRecent = document.getElementById('noRecent');

    // Simulate loading recent uploads
    fetch('{{ route("admin.cms.images.api.recent") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                recentUploads.innerHTML = data.map(image => `
                    <div class="bg-gray-700 rounded-lg overflow-hidden border border-gray-600 hover:border-gray-500 transition-colors cursor-pointer"
                         onclick="useRecentImage('${image.url}', '${image.filename}')">
                        <div class="aspect-square bg-gray-600 flex items-center justify-center overflow-hidden">
                            <img src="${image.url}" alt="${image.filename}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-2">
                            <p class="text-xs text-gray-300 truncate">${image.filename}</p>
                        </div>
                    </div>
                `).join('');
                noRecent.classList.add('hidden');
            }
        })
        .catch(() => {
            // Keep the no recent uploads message
        });
}

function useRecentImage(url, filename) {
    // Copy URL to clipboard
    navigator.clipboard.writeText(url).then(() => {
        // Show success message
        showNotification('Image URL copied to clipboard!', 'success');
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-600 text-white' : 'bg-blue-600 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection