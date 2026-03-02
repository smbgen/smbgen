@extends('layouts.blog')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-2 text-gray-900">{{ $post->title }}</h1>
    
    <div class="flex items-center gap-4 text-gray-600 mb-8">
        <span>By {{ $post->author->name }}</span>
        <span>•</span>
        <span>{{ $post->published_at->format('F d, Y') }}</span>
        <span>•</span>
        <span>{{ $post->view_count }} views</span>
    </div>

    @if($post->featured_image)
        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full rounded-lg mb-8">
    @endif

    <div class="prose dark:prose-invert max-w-none">
        @if($post->content_blocks && count($post->content_blocks) > 0)
            @foreach($post->content_blocks as $block)
                @include('blog.blocks.' . $block['type'], ['block' => $block])
            @endforeach
        @elseif($post->content)
            {!! $post->content !!}
        @else
            <p class="text-gray-500">No content available.</p>
        @endif
    </div>

    {{-- Categories and Tags --}}
    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
        @if($post->categories->isNotEmpty())
            <div class="mb-4">
                <strong>Categories:</strong>
                @foreach($post->categories as $category)
                    <a href="{{ route('blog.category', $category->slug) }}" class="inline-block px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded mr-2 mb-2">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if($post->tags->isNotEmpty())
            <div>
                <strong>Tags:</strong>
                @foreach($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-sm mr-2 mb-2">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Related Posts --}}
    @if($relatedPosts->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-900">Related Posts</h2>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($relatedPosts as $related)
                    <a href="{{ route('blog.show', $related->slug) }}" class="block group">
                        @if($related->featured_image)
                            <img src="{{ $related->featured_image }}" alt="{{ $related->title }}" class="w-full h-48 object-cover rounded-lg mb-3">
                        @endif
                        <h3 class="font-bold text-gray-900 group-hover:text-blue-600">{{ $related->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $related->published_at->format('M d, Y') }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
