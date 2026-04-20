@extends('layouts.blog')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-8 text-gray-900 dark:text-white">Tag: #{{ $tag->name }}</h1>

    @if($posts->isNotEmpty())
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    @if($post->featured_image)
                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h2 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">
                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600">{{ $post->title }}</a>
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            {{ $post->published_at->format('F d, Y') }} • By {{ $post->author->name }}
                        </p>
                        @if($post->excerpt)
                            <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $post->excerpt }}</p>
                        @endif
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-blue-600 hover:underline">Read more →</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @else
        <p class="text-gray-600 dark:text-gray-400">No posts with this tag yet.</p>
    @endif
</div>
@endsection
