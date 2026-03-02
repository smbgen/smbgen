@extends('layouts.blog')

@section('title', 'Search Results for "' . $searchTerm . '"')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Search Results
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Found {{ $totalResults }} {{ Str::plural('result', $totalResults) }} for "<strong>{{ $searchTerm }}</strong>"
            </p>
        </div>

        <!-- Search Form -->
        <div class="mb-8">
            <form action="{{ route('blog.search') }}" method="GET" class="flex gap-2">
                <input type="search" 
                       name="q" 
                       value="{{ $searchTerm }}" 
                       placeholder="Search blog posts..." 
                       class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="btn-primary px-6">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </form>
        </div>

        <!-- Search Results -->
        @if($posts->isNotEmpty())
            <div class="space-y-6">
                @foreach($posts as $post)
                    <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-3">
                                <time class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $post->published_at->format('M d, Y') }}
                                </time>
                                @foreach($post->categories as $category)
                                    <a href="{{ route('blog.category', $category->slug) }}" 
                                       class="text-xs px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                            
                            <h2 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">
                                <a href="{{ route('blog.show', $post->slug) }}" 
                                   class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            @if($post->excerpt)
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    {{ Str::limit($post->excerpt, 200) }}
                                </p>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        By {{ $post->author->name }}
                                    </span>
                                    @if($post->tags->isNotEmpty())
                                        <div class="flex gap-2">
                                            @foreach($post->tags->take(3) as $tag)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    #{{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                
                                <a href="{{ route('blog.show', $post->slug) }}" 
                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    Read more →
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $posts->appends(['q' => $searchTerm])->links() }}
            </div>
        @else
            <!-- No Results -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-search text-6xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    No results found
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    We couldn't find any posts matching "{{ $searchTerm }}"
                </p>
                <a href="{{ route('blog.index') }}" class="btn-primary">
                    Browse All Posts
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
