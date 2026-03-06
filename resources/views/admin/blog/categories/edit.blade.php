@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Edit Category</h1>
            <p class="admin-page-subtitle">Update category details</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.blog.categories.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Categories
            </a>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.blog.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="name" class="form-label">Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $category->name) }}"
                               class="form-input @error('name') border-red-500 @enderror"
                               placeholder="e.g., Tutorials"
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
                               value="{{ old('slug', $category->slug) }}"
                               class="form-input @error('slug') border-red-500 @enderror"
                               placeholder="e.g., tutorials">
                        <p class="text-xs text-gray-400 mt-1">Used in the URL: /blog/category/{{ $category->slug }}</p>
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
                                  placeholder="Category description for SEO...">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="parent_id" class="form-label">Parent Category <span class="text-sm text-gray-400">(optional)</span></label>
                        <select id="parent_id" 
                                name="parent_id" 
                                class="form-input @error('parent_id') border-red-500 @enderror">
                            <option value="">None (Top Level)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Create sub-categories by selecting a parent</p>
                        @error('parent_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order" class="form-label">Display Order <span class="text-sm text-gray-400">(optional)</span></label>
                        <input type="number" 
                               id="order" 
                               name="order" 
                               value="{{ old('order', $category->order ?? 0) }}"
                               class="form-input @error('order') border-red-500 @enderror"
                               placeholder="0"
                               min="0">
                        <p class="text-xs text-gray-400 mt-1">Lower numbers appear first (0 = default)</p>
                        @error('order')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-200">Posts in this category</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $category->posts()->count() }} {{ Str::plural('post', $category->posts()->count()) }}</p>
                            </div>
                            @if($category->posts()->count() > 0)
                                <a href="{{ route('blog.category', $category->slug) }}" target="_blank" class="btn-secondary text-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i>View on Blog
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-700">
                    <a href="{{ route('admin.blog.categories.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
