@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Blog Posts</h1>
            <p class="admin-page-subtitle">Manage your blog content</p>
        </div>
        <div class="action-buttons flex gap-3">
            <a href="{{ route('blog.index') }}" target="_blank" class="btn-secondary">
                <i class="fas fa-eye mr-2"></i>View Blog
            </a>
            <a href="{{ route('admin.blog.import.index') }}" class="btn-secondary">
                <i class="fas fa-file-import mr-2"></i>Import from WordPress
            </a>
            <a href="{{ route('admin.blog.posts.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>New Post
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- SEO Best Practices Info --}}
    <div x-data="{ open: false }" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg mb-6 overflow-hidden">
        <button @click="open = !open" class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
            <div class="flex items-center gap-2">
                <i class="fas fa-lightbulb text-blue-600 dark:text-blue-400"></i>
                <span class="font-semibold text-blue-900 dark:text-blue-100">SEO Best Practices: Why /blog Ranks Better</span>
            </div>
            <i class="fas fa-chevron-down text-blue-600 dark:text-blue-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
        </button>
        
        <div x-show="open" x-collapse class="px-4 pb-4 text-sm text-blue-900 dark:text-blue-100">
            <div class="space-y-4 pt-2">
                <p class="leading-relaxed">
                    Using a <code class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/40 rounded font-mono text-xs">/blog</code> subdirectory generally ranks better than alternatives like a separate subdomain (e.g., blog.example.com) or no blog at all. Search engines treat a subdirectory as a direct part of your main domain, allowing the blog's content to actively boost your site's overall authority and keyword visibility.
                </p>

                <div>
                    <h4 class="font-semibold mb-2 flex items-center gap-2 text-blue-900 dark:text-blue-100">
                        <i class="fas fa-arrow-trend-up text-green-600 dark:text-green-400"></i>
                        Why /blog Ranks Better
                    </h4>
                    <ul class="space-y-1.5 ml-6 list-disc">
                        <li><strong>Domain Authority Sharing:</strong> All the "SEO juice" from your blog posts—including backlinks and social signals—flows directly to your primary domain, improving the rankings of your entire site.</li>
                        <li><strong>Keyword Expansion:</strong> A blog allows you to target long-tail and informational keywords that wouldn't naturally fit on product or service pages.</li>
                        <li><strong>Topical Authority:</strong> Consistently covering a subject in depth signals to search engines that your site is an expert source, leading to higher rankings across related topics.</li>
                        <li><strong>Internal Linking:</strong> You can use blog posts to link to your "money pages" (services or products), helping those core pages rank higher.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-2 flex items-center gap-2 text-blue-900 dark:text-blue-100">
                        <i class="fas fa-triangle-exclamation text-amber-600 dark:text-amber-400"></i>
                        When It Can Rank "Worse"
                    </h4>
                    <p class="mb-2">A blog only harms your SEO if it is executed poorly:</p>
                    <ul class="space-y-1.5 ml-6 list-disc">
                        <li><strong>Low Quality/Thin Content:</strong> Publishing generic or AI-generated "slop" without adding value can lead to poor engagement signals, which may drag down your entire site's ranking.</li>
                        <li><strong>Duplicate Content:</strong> Overusing tags or categories can create duplicate content issues that confuse search engines.</li>
                        <li><strong>Cannibalization:</strong> If a blog post targets the exact same keywords as a core service page, they may compete against each other in search results.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-2 flex items-center gap-2 text-blue-900 dark:text-blue-100">
                        <i class="fas fa-star text-yellow-500 dark:text-yellow-400"></i>
                        Best Practices for 2025
                    </h4>
                    <ul class="space-y-1.5 ml-6 list-disc">
                        <li><strong>Nail Search Intent:</strong> Modern algorithms prioritize content that fully answers a user's query over simple keyword matching.</li>
                        <li><strong>Update Regularly:</strong> Refreshing old posts with current data tells search engines your information remains reliable.</li>
                        <li><strong>Optimize for AI Overviews:</strong> Valuable, well-structured blog content is often used as a source for Google's AI Overviews at the top of search results.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search posts..." class="form-input">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('admin.blog.posts.index') }}" class="btn-secondary">Clear</a>
        </form>
    </div>

    {{-- Posts Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Categories</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Views</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($posts as $post)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $post->title }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $post->published_at?->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $post->author->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($post->status === 'published') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($post->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @elseif($post->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @endif">
                                {{ ucfirst($post->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $post->categories->pluck('name')->join(', ') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $post->view_count }}</td>
                        <td class="px-6 py-4 text-right text-sm flex gap-2 justify-end">
                            <a href="{{ route('admin.blog.posts.edit', $post) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Edit</a>
                            <form method="POST" action="{{ route('admin.blog.posts.destroy', $post) }}" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No posts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection
