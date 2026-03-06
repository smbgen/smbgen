@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-100">CMS Image Details</h2>
            <p class="text-sm text-gray-400 mt-1">{{ $image->filename }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.cms.images.edit', $image) }}" class="btn-secondary">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.cms.images.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Images
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Image Display -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <div class="aspect-w-16 aspect-h-9 bg-gray-700 rounded-lg overflow-hidden mb-4">
                    <img src="{{ $image->getUrl() }}" alt="{{ $image->alt_text ?? 'CMS Image' }}"
                         class="w-full h-full object-contain">
                </div>

                <!-- Usage Examples -->
                <div class="space-y-4">
                    <h4 class="text-sm font-medium text-gray-300">Usage Examples</h4>

                    <!-- HTML -->
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">HTML</label>
                        <div class="bg-gray-900 rounded p-3">
                            <code class="text-sm text-gray-100 font-mono">{{ $image->getImgTag() }}</code>
                        </div>
                        <button onclick="copyToClipboard('{{ $image->getImgTag() }}')"
                                class="mt-2 text-xs text-blue-400 hover:text-blue-300">
                            <i class="fas fa-copy mr-1"></i>Copy HTML
                        </button>
                    </div>

                    <!-- Markdown -->
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Markdown</label>
                        <div class="bg-gray-900 rounded p-3">
                            <code class="text-sm text-gray-100 font-mono">{{ $image->getMarkdown() }}</code>
                        </div>
                        <button onclick="copyToClipboard('{{ $image->getMarkdown() }}')"
                                class="mt-2 text-xs text-blue-400 hover:text-blue-300">
                            <i class="fas fa-copy mr-1"></i>Copy Markdown
                        </button>
                    </div>

                    <!-- Direct URL -->
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Direct URL</label>
                        <div class="bg-gray-900 rounded p-3">
                            <code class="text-sm text-gray-100 font-mono">{{ $image->getUrl() }}</code>
                        </div>
                        <button onclick="copyToClipboard('{{ $image->getUrl() }}')"
                                class="mt-2 text-xs text-blue-400 hover:text-blue-300">
                            <i class="fas fa-copy mr-1"></i>Copy URL
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Information -->
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-100 mb-4">Image Information</h3>

                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-400">Filename:</span>
                        <p class="text-sm text-gray-100">{{ $image->filename }}</p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-400">Alt Text:</span>
                        <p class="text-sm text-gray-100">{{ $image->alt_text ?? 'Not set' }}</p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-400">Size:</span>
                        <p class="text-sm text-gray-100">{{ number_format($image->size / 1024, 1) }} KB</p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-400">Dimensions:</span>
                        <p class="text-sm text-gray-100">
                            {{ $image->width ?? 'N/A' }} × {{ $image->height ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-400">MIME Type:</span>
                        <p class="text-sm text-gray-100">{{ $image->mime_type }}</p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-400">Path:</span>
                        <p class="text-sm text-gray-100 font-mono">{{ $image->path }}</p>
                    </div>
                </div>
            </div>

            <!-- Upload Info -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-100 mb-4">Upload Information</h3>

                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-400">Uploaded by:</span>
                        <p class="text-sm text-gray-100">{{ $image->user->name ?? 'Unknown' }}</p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-400">Uploaded on:</span>
                        <p class="text-sm text-gray-100">{{ $image->created_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>

                    <div>
                        <span class="text-sm text-gray-400">Last updated:</span>
                        <p class="text-sm text-gray-100">{{ $image->updated_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-100 mb-4">Actions</h3>

                <div class="space-y-3">
                    <a href="{{ route('admin.cms.images.edit', $image) }}"
                       class="w-full btn-secondary text-center block">
                        <i class="fas fa-edit mr-2"></i>Edit Details
                    </a>

                    <button onclick="openInNewTab('{{ $image->getUrl() }}')"
                            class="w-full btn-secondary text-center">
                        <i class="fas fa-external-link-alt mr-2"></i>View Full Size
                    </button>

                    <form action="{{ route('admin.cms.images.destroy', $image) }}" method="POST" class="inline w-full"
                          onsubmit="return confirm('Are you sure you want to delete this image? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full btn-danger">
                            <i class="fas fa-trash mr-2"></i>Delete Image
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.textContent = 'Copied to clipboard!';
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 2000);
    });
}

function openInNewTab(url) {
    window.open(url, '_blank');
}
</script>
@endsection