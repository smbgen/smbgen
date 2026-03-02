@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-100">Edit CMS Image</h2>
            <p class="text-sm text-gray-400 mt-1">Update image details</p>
        </div>
        <a href="{{ route('admin.cms.images.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Images
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-gray-100">Image Details</h3>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.cms.images.update', $image) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Current Image -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Current Image</label>
                    <div class="border border-gray-600 rounded-lg p-4 bg-gray-700">
                        <img src="{{ $image->getUrl() }}" alt="{{ $image->alt_text ?? 'CMS Image' }}"
                             class="max-w-full h-auto rounded max-h-64 mx-auto">
                        <div class="mt-2 text-center text-sm text-gray-400">
                            {{ $image->filename }} ({{ number_format($image->size / 1024, 1) }} KB)
                        </div>
                    </div>
                </div>

                <!-- Alt Text -->
                <div class="mb-6">
                    <label for="alt_text" class="block text-sm font-medium text-gray-300 mb-2">
                        Alt Text
                    </label>
                    <input type="text" name="alt_text" id="alt_text" value="{{ old('alt_text', $image->alt_text) }}"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">
                        Alternative text for screen readers and when the image fails to load
                    </p>
                    @error('alt_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Info -->
                <div class="mb-6 bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-300 mb-3">Image Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400">Filename:</span>
                            <span class="text-gray-100 ml-2">{{ $image->filename }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Size:</span>
                            <span class="text-gray-100 ml-2">{{ number_format($image->size / 1024, 1) }} KB</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Dimensions:</span>
                            <span class="text-gray-100 ml-2">{{ $image->width ?? 'N/A' }} × {{ $image->height ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Uploaded:</span>
                            <span class="text-gray-100 ml-2">{{ $image->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">MIME Type:</span>
                            <span class="text-gray-100 ml-2">{{ $image->mime_type }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Uploader:</span>
                            <span class="text-gray-100 ml-2">{{ $image->user->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.cms.images.index') }}" class="btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Update Image
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Section -->
    <div class="mt-8 bg-red-900 border border-red-700 rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-red-700">
            <h3 class="text-lg font-semibold text-red-100">Danger Zone</h3>
        </div>

        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-red-100">Delete Image</h4>
                    <p class="text-sm text-red-300 mt-1">
                        Permanently delete this image. This action cannot be undone.
                    </p>
                </div>
                <form action="{{ route('admin.cms.images.destroy', $image) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this image? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash mr-2"></i>Delete Image
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection