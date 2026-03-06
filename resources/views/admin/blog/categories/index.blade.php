@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Blog Categories</h1>
            <p class="admin-page-subtitle">Organize posts into categories</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.blog.posts.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Posts
            </a>
            <a href="{{ route('admin.blog.categories.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>New Category
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="admin-card">
        <div class="admin-card-body">
            @if($categories->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-folder text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-100 mb-2">No categories yet</h3>
                    <p class="text-gray-400 mb-4">Create your first category to organize your blog posts</p>
                    <a href="{{ route('admin.blog.categories.create') }}" class="btn-primary inline-block">
                        <i class="fas fa-plus mr-2"></i>Create First Category
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Parent</th>
                                <th class="text-center">Order</th>
                                <th class="text-center">Posts</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                <i class="fas fa-folder mr-1 text-xs"></i>
                                                {{ $category->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-sm text-gray-400">/blog/category/{{ $category->slug }}</code>
                                    </td>
                                    <td>
                                        @if($category->parent)
                                            <span class="text-sm text-gray-400">
                                                <i class="fas fa-arrow-turn-up fa-rotate-90 mr-1 text-xs"></i>
                                                {{ $category->parent->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-sm">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="text-sm text-gray-400">{{ $category->order ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('blog.category', $category->slug) }}" target="_blank" class="text-green-400 hover:text-green-300" title="View on blog">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.blog.categories.edit', $category) }}" class="text-blue-400 hover:text-blue-300" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.blog.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category? This will not delete the posts.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-300" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
