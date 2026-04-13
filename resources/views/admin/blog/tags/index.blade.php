@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Blog Tags</h1>
            <p class="admin-page-subtitle">Organize posts with tags</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.blog.posts.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Posts
            </a>
            <a href="{{ route('admin.blog.tags.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>New Tag
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="admin-card">
        <div class="admin-card-body">
            @if($tags->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-tags text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-100 mb-2">No tags yet</h3>
                    <p class="text-gray-400 mb-4">Create your first tag to organize your blog posts</p>
                    <a href="{{ route('admin.blog.tags.create') }}" class="btn-primary inline-block">
                        <i class="fas fa-plus mr-2"></i>Create First Tag
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th class="text-center">Posts</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tags as $tag)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <i class="fas fa-tag mr-1 text-xs"></i>
                                                {{ $tag->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-sm text-gray-400">/blog/tag/{{ $tag->slug }}</code>
                                    </td>
                                    <td class="text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $tag->posts_count }} {{ Str::plural('post', $tag->posts_count) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('blog.tag', $tag->slug) }}" target="_blank" class="text-green-400 hover:text-green-300" title="View on blog">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.blog.tags.edit', $tag) }}" class="text-blue-400 hover:text-blue-300" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.blog.tags.destroy', $tag) }}" method="POST" class="inline" onsubmit="return confirm('Delete this tag? This will not delete the posts.');">
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
