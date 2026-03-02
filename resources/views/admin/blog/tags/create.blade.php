@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Create Tag</h1>
            <p class="admin-page-subtitle">Add a new tag for your blog posts</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.blog.tags.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Tags
            </a>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.blog.tags.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="name" class="form-label">Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="form-input @error('name') border-red-500 @enderror"
                               placeholder="e.g., Web Development"
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="form-label">Slug <span class="text-sm text-gray-400">(optional - auto-generated from name)</span></label>
                        <input type="text" 
                               id="slug" 
                               name="slug" 
                               value="{{ old('slug') }}"
                               class="form-input @error('slug') border-red-500 @enderror"
                               placeholder="e.g., web-development">
                        <p class="text-xs text-gray-400 mt-1">Used in the URL: /blog/tag/slug-here</p>
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="form-label">Description <span class="text-sm text-gray-400">(optional)</span></label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="form-input @error('description') border-red-500 @enderror"
                                  placeholder="Short description for SEO...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-700">
                    <a href="{{ route('admin.blog.tags.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Create Tag
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
