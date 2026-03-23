@extends('layouts.admin')

@section('title', 'Edit LinkedIn Post')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.linkedin.posts.index') }}" class="text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Post</h1>
            <p class="text-gray-400 text-sm mt-0.5">
                Last updated {{ $post->updated_at->diffForHumans() }}
                @if($post->isPublished())
                    &bull; <span class="text-green-400">Published {{ $post->published_at?->format('M j, Y') }}</span>
                @endif
            </p>
        </div>
    </div>

    @if($post->isPublished())
        <div class="bg-yellow-900/30 border border-yellow-700 rounded-lg px-4 py-3 text-sm text-yellow-300 flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            This post has already been published to LinkedIn and cannot be edited.
        </div>
    @endif

    {{-- Editor --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 {{ $post->isPublished() ? 'opacity-60 pointer-events-none' : '' }}">
        @livewire('admin.social-post-editor', ['post' => $post, 'accounts' => $accounts])
    </div>

</div>
@endsection
