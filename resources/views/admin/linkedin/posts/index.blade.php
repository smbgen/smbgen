@extends('layouts.admin')

@section('title', 'LinkedIn Posts')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="fab fa-linkedin text-blue-500"></i> LinkedIn Posts
            </h1>
            <p class="text-gray-400 text-sm mt-1">Manage drafts, scheduled, and published posts.</p>
        </div>
        <a href="{{ route('admin.linkedin.posts.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-700 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-pen"></i> New Post
        </a>
    </div>

    {{-- Session messages --}}
    @if(session('status'))
        <div class="bg-green-900/40 border border-green-700 text-green-300 rounded-lg px-4 py-3 text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-900/40 border border-red-700 text-red-300 rounded-lg px-4 py-3 text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Status tabs --}}
    <div class="flex gap-1 bg-gray-800 rounded-lg p-1 w-fit">
        @foreach(['all' => 'All', 'draft' => 'Drafts', 'scheduled' => 'Scheduled', 'published' => 'Published', 'failed' => 'Failed'] as $tab => $label)
            <a href="{{ route('admin.linkedin.posts.index', ['status' => $tab]) }}"
                class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors
                    {{ $status === $tab ? 'bg-gray-600 text-white' : 'text-gray-400 hover:text-white' }}">
                {{ $label }}
                <span class="ml-1 text-xs {{ $status === $tab ? 'text-gray-300' : 'text-gray-600' }}">{{ $counts[$tab] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Posts table --}}
    @if($posts->isEmpty())
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-10 text-center">
            <i class="fas fa-newspaper text-gray-600 text-4xl mb-3"></i>
            <p class="text-gray-400">No {{ $status === 'all' ? '' : $status }} posts yet.</p>
            <a href="{{ route('admin.linkedin.posts.create') }}" class="text-blue-400 hover:text-blue-300 text-sm mt-2 inline-block">
                Create your first post →
            </a>
        </div>
    @else
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-700/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Post</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">Account</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left hidden lg:table-cell">Date</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($posts as $post)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3">
                                @if($post->title)
                                    <p class="text-white font-medium">{{ $post->title }}</p>
                                @endif
                                <p class="text-gray-400 text-xs line-clamp-2 max-w-xs">{{ Str::limit($post->content, 120) }}</p>
                                @if(!empty($post->media_paths))
                                    <span class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                        <i class="fas fa-image"></i> {{ count($post->media_paths) }} image(s)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="text-gray-300 text-xs">{{ $post->socialAccount->page_name ?? $post->socialAccount->account_name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $post->getStatusBadgeClass() }} text-white">
                                    {{ ucfirst($post->status) }}
                                </span>
                                @if($post->isFailed() && $post->error_message)
                                    <p class="text-red-400 text-xs mt-1 max-w-xs truncate" title="{{ $post->error_message }}">{{ Str::limit($post->error_message, 60) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell text-gray-400 text-xs">
                                @if($post->isScheduled() && $post->scheduled_at)
                                    <span class="text-blue-400">{{ $post->scheduled_at->format('M j, Y g:i A') }}</span>
                                @elseif($post->isPublished() && $post->published_at)
                                    {{ $post->published_at->format('M j, Y g:i A') }}
                                @else
                                    {{ $post->updated_at->diffForHumans() }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$post->isPublished())
                                        <a href="{{ route('admin.linkedin.posts.edit', $post) }}"
                                            class="text-gray-400 hover:text-white transition-colors" title="Edit">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.linkedin.posts.publish', $post) }}"
                                            onclick="return confirm('Publish this post to LinkedIn now?')"
                                            class="text-blue-400 hover:text-blue-300 transition-colors" title="Publish now">
                                            <i class="fab fa-linkedin text-sm"></i>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.linkedin.posts.destroy', $post) }}" class="inline"
                                        onsubmit="return confirm('Delete this post?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
            <div class="px-4">{{ $posts->links() }}</div>
        @endif
    @endif

</div>
@endsection
