@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">CMS Images</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage images for use in CMS content</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="bulkDeleteBtn" onclick="bulkDelete()" class="hidden btn-danger">
                <i class="fas fa-trash mr-2"></i>Delete Selected (<span id="selectedCountBadge">0</span>)
            </button>
            <a href="{{ route('admin.cms.images.create') }}" class="btn-primary">
                <i class="fas fa-upload mr-2"></i>Upload Images
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Total Images</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ \App\Models\CmsImage::count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-600/20 flex items-center justify-center">
                    <i class="fas fa-images text-blue-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Storage Used</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        @php
                            $totalSize = \App\Models\CmsImage::sum('size');
                            $units = ['B', 'KB', 'MB', 'GB'];
                            $i = 0;
                            while ($totalSize >= 1024 && $i < 3) {
                                $totalSize /= 1024;
                                $i++;
                            }
                        @endphp
                        {{ round($totalSize, 1) }} {{ $units[$i] }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-600/20 flex items-center justify-center">
                    <i class="fas fa-database text-purple-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Recent Uploads</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ \App\Models\CmsImage::where('created_at', '>=', now()->subWeek())->count() }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-500 mt-1">Last 7 days</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-600/20 flex items-center justify-center">
                    <i class="fas fa-clock text-green-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Avg. File Size</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        @php
                            $avgSize = \App\Models\CmsImage::avg('size') ?? 0;
                            $units = ['B', 'KB', 'MB', 'GB'];
                            $i = 0;
                            while ($avgSize >= 1024 && $i < 3) {
                                $avgSize /= 1024;
                                $i++;
                            }
                        @endphp
                        {{ round($avgSize, 1) }} {{ $units[$i] }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-cyan-600/20 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-cyan-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and View Options -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-md px-6 py-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Search and Filters -->
            <div class="flex flex-wrap items-center gap-4 flex-1">
                <form method="GET" class="flex flex-wrap items-center gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Search images...">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sort By</label>
                        <select name="sort" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A-Z</option>
                            <option value="size" {{ request('sort') === 'size' ? 'selected' : '' }}>Size</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="btn-primary text-sm">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>

                    @if(request('search') || request('sort'))
                        <div class="flex items-end">
                            <a href="{{ route('admin.cms.images.index') }}" class="btn-secondary text-sm">
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Selection and View Options -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"
                           class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                    <label for="selectAll" class="text-xs text-gray-600 dark:text-gray-400 cursor-pointer">Select All</label>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-600 dark:text-gray-400">View:</span>
                    <button onclick="setViewMode('grid')" id="gridViewBtn" class="p-2 rounded-lg bg-blue-600 text-white">
                        <i class="fas fa-th"></i>
                    </button>
                    <button onclick="setViewMode('list')" id="listViewBtn" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Images Grid -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden">
        @if($images->count() > 0)
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                    @foreach($images as $image)
                        <div class="relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 hover:border-blue-500 transition-colors group image-card" data-image-id="{{ $image->id }}">
                            <!-- Checkbox Overlay -->
                            <div class="absolute top-2 left-2 z-10">
                                    <input type="checkbox" 
                                        class="image-checkbox w-5 h-5 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-500 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                       value="{{ $image->id }}"
                                       onchange="updateBulkDeleteButton()"
                                       onclick="event.stopPropagation();">
                            </div>
                            <!-- Image -->
                            <div class="aspect-square bg-gray-200 dark:bg-gray-600 flex items-center justify-center overflow-hidden relative cursor-pointer"
                                 onclick="copyImageUrl('{{ $image->getUrl() }}', this)"
                                 title="Click to copy URL">
                                @if($image->isImage())
                                    <img src="{{ $image->getUrl() }}"
                                         alt="{{ $image->alt_text ?: $image->original_name }}"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                @else
                                    <div class="text-gray-600 dark:text-gray-400 text-center">
                                        <i class="fas fa-file-image text-3xl mb-2"></i>
                                        <p class="text-xs">{{ strtoupper($image->file_extension ?? 'FILE') }}</p>
                                    </div>
                                @endif
                                <!-- Overlay indicator -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 flex items-center justify-center transition-all duration-200 opacity-0 group-hover:opacity-100">
                                    <div class="text-white text-center">
                                        <i class="fas fa-copy text-2xl mb-2"></i>
                                        <p class="text-xs font-medium">Click to Copy URL</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-3">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $image->original_name }}">
                                    {{ Str::limit($image->original_name, 20) }}
                                </h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $image->formatted_size }}
                                </p>
                                @if($image->alt_text)
                                    <p class="text-xs text-gray-600 dark:text-gray-500 mt-1 truncate" title="{{ $image->alt_text }}">
                                        Alt: {{ Str::limit($image->alt_text, 15) }}
                                    </p>
                                @endif
                                
                                <!-- URL Preview -->
                                <div class="mt-2">
                                    <input type="text" 
                                           value="{{ $image->getUrl() }}" 
                                           readonly 
                                           onclick="event.stopPropagation(); this.select(); copyToClipboard('{{ $image->getUrl() }}', this.parentElement);"
                                           class="w-full px-2 py-1 text-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer hover:border-blue-500"
                                           title="Click to copy URL">
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between mt-3">
                                    <button onclick="event.stopPropagation(); copyToClipboard('{{ $image->getUrl() }}', this)"
                                            class="text-xs text-blue-400 hover:text-blue-300 transition-colors"
                                            title="Copy URL">
                                        <i class="fas fa-copy"></i>
                                    </button>

                                    <button onclick="event.stopPropagation(); insertImage('{{ $image->getImgTag() }}', '{{ $image->getMarkdown() }}')"
                                            class="text-xs text-green-400 hover:text-green-300 transition-colors"
                                            title="Insert in CMS">
                                        <i class="fas fa-plus"></i>
                                    </button>

                                    <a href="{{ route('admin.cms.images.edit', $image) }}"
                                       class="text-xs text-yellow-400 hover:text-yellow-300 transition-colors"
                                       onclick="event.stopPropagation();"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.cms.images.destroy', $image) }}" method="POST"
                                          onsubmit="event.stopPropagation(); return confirm('Delete this image?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors"
                                                onclick="event.stopPropagation();"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($images->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $images->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <i class="fas fa-images text-gray-600 text-6xl mb-4"></i>
                <h4 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No images found</h4>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    @if(request('search') || request('type'))
                        No images match your current filters.
                    @else
                        Upload your first image to get started.
                    @endif
                </p>
                @if(request('search') || request('type'))
                    <a href="{{ route('admin.cms.images.index') }}" class="btn-secondary mr-4">
                        Clear Filters
                    </a>
                @endif
                <a href="{{ route('admin.cms.images.create') }}" class="btn-primary">
                    <i class="fas fa-upload mr-2"></i>Upload First Image
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Insert Image Modal (for CMS integration) -->
<div id="insertImageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Insert Image</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose how to insert this image:</p>

            <div class="space-y-3">
                <button id="insertHtmlBtn" class="w-full text-left p-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <div class="font-medium text-gray-900 dark:text-gray-100">HTML</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Insert as &lt;img&gt; tag</div>
                </button>

                <button id="insertMarkdownBtn" class="w-full text-left p-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <div class="font-medium text-gray-900 dark:text-gray-100">Markdown</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Insert as ![alt](url) syntax</div>
                </button>
            </div>

            <div class="flex justify-end mt-6">
                <button onclick="closeInsertModal()" class="btn-secondary">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentHtml = '';
let currentMarkdown = '';

// Bulk selection management
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.image-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.image-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const badge = document.getElementById('selectedCountBadge');
    
    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.classList.remove('hidden');
        badge.textContent = checkedBoxes.length;
    } else {
        bulkDeleteBtn.classList.add('hidden');
        document.getElementById('selectAll').checked = false;
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.image-checkbox:checked');
    const imageIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (imageIds.length === 0) {
        return;
    }
    
    if (!confirm(`Delete ${imageIds.length} selected image(s)? This action cannot be undone.`)) {
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.cms.images.bulk-delete") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add method spoofing for DELETE request
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    form.appendChild(methodField);
    
    imageIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'image_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// Fallback copy method using textarea
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.width = '2em';
    textArea.style.height = '2em';
    textArea.style.padding = '0';
    textArea.style.border = 'none';
    textArea.style.outline = 'none';
    textArea.style.boxShadow = 'none';
    textArea.style.background = 'transparent';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        return true;
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        return false;
    } finally {
        document.body.removeChild(textArea);
    }
}

function copyToClipboard(text, button) {
    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showCopySuccess(button);
        }).catch(err => {
            // Fallback to old method
            if (fallbackCopyTextToClipboard(text)) {
                showCopySuccess(button);
            } else {
                alert('Failed to copy to clipboard');
            }
        });
    } else {
        // Use fallback method directly
        if (fallbackCopyTextToClipboard(text)) {
            showCopySuccess(button);
        } else {
            alert('Failed to copy to clipboard');
        }
    }
}

function showCopySuccess(button) {
    const original = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    button.classList.remove('text-blue-400');
    button.classList.add('text-green-400');

    setTimeout(() => {
        button.innerHTML = original;
        button.classList.remove('text-green-400');
        button.classList.add('text-blue-400');
    }, 2000);
}

// Copy image URL when clicking on the image itself
function copyImageUrl(url, element) {
    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            showImageCopySuccess(element);
        }).catch(err => {
            // Fallback to old method
            if (fallbackCopyTextToClipboard(url)) {
                showImageCopySuccess(element);
            } else {
                alert('Failed to copy URL to clipboard');
            }
        });
    } else {
        // Use fallback method directly
        if (fallbackCopyTextToClipboard(url)) {
            showImageCopySuccess(element);
        } else {
            alert('Failed to copy URL to clipboard');
        }
    }
}

function showImageCopySuccess(element) {
    // Show success feedback
    const overlay = element.querySelector('.absolute');
    if (overlay) {
        const originalHTML = overlay.innerHTML;
        overlay.innerHTML = `
            <div class="text-white text-center">
                <i class="fas fa-check-circle text-3xl mb-2 text-green-400"></i>
                <p class="text-sm font-semibold">URL Copied!</p>
            </div>
        `;
        overlay.classList.remove('bg-opacity-0', 'opacity-0');
        overlay.classList.add('bg-opacity-70', 'opacity-100');
        
        setTimeout(() => {
            overlay.innerHTML = originalHTML;
        }, 1500);
    }
}

function insertImage(html, markdown) {
    currentHtml = html;
    currentMarkdown = markdown;
    document.getElementById('insertImageModal').classList.remove('hidden');
}

function closeInsertModal() {
    document.getElementById('insertImageModal').classList.add('hidden');
    currentHtml = '';
    currentMarkdown = '';
}

// Handle insert buttons
document.getElementById('insertHtmlBtn').addEventListener('click', () => {
    // This would integrate with a rich text editor
    // For now, just copy to clipboard
    copyToClipboard(currentHtml, document.getElementById('insertHtmlBtn'));
    closeInsertModal();
});

document.getElementById('insertMarkdownBtn').addEventListener('click', () => {
    copyToClipboard(currentMarkdown, document.getElementById('insertMarkdownBtn'));
    closeInsertModal();
});

// Close modal when clicking outside
document.getElementById('insertImageModal').addEventListener('click', (e) => {
    if (e.target === e.currentTarget) {
        closeInsertModal();
    }
});
</script>
@endsection