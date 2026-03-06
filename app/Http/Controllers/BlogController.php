<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of published blog posts
     */
    public function index(Request $request)
    {
        $query = BlogPost::published()
            ->with(['author', 'categories', 'tags'])
            ->orderByDesc('published_at');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(12);
        $categories = BlogCategory::has('publishedPosts')->orderBy('name')->get();
        $popularTags = BlogTag::has('publishedPosts')->withCount('posts')->orderByDesc('posts_count')->limit(20)->get();

        return view('blog.index', compact('posts', 'categories', 'popularTags'));
    }

    /**
     * Display the specified blog post
     */
    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->with(['author', 'categories', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $post->incrementViews();

        // Get related posts (same categories)
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($q) use ($post) {
                $q->whereIn('blog_categories.id', $post->categories->pluck('id'));
            })
            ->with(['author', 'categories'])
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Display posts in a category
     */
    public function category(string $slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $posts = $category->publishedPosts()
            ->with(['author', 'categories', 'tags'])
            ->paginate(12);

        $categories = BlogCategory::has('publishedPosts')->orderBy('name')->get();
        $popularTags = BlogTag::has('publishedPosts')->withCount('posts')->orderByDesc('posts_count')->limit(20)->get();

        return view('blog.category', compact('category', 'posts', 'categories', 'popularTags'));
    }

    /**
     * Display posts with a tag
     */
    public function tag(string $slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();

        $posts = $tag->publishedPosts()
            ->with(['author', 'categories', 'tags'])
            ->paginate(12);

        $categories = BlogCategory::has('publishedPosts')->orderBy('name')->get();
        $popularTags = BlogTag::has('publishedPosts')->withCount('posts')->orderByDesc('posts_count')->limit(20)->get();

        return view('blog.tag', compact('tag', 'posts', 'categories', 'popularTags'));
    }

    /**
     * Display RSS feed
     */
    public function feed()
    {
        $posts = BlogPost::published()
            ->with('author')
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();

        return response()->view('blog.feed', compact('posts'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate XML sitemap
     */
    public function sitemap()
    {
        $posts = BlogPost::published()
            ->orderByDesc('updated_at')
            ->get();

        $categories = BlogCategory::has('publishedPosts')->get();
        $tags = BlogTag::has('publishedPosts')->get();

        return response()->view('blog.sitemap', compact('posts', 'categories', 'tags'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Preview unpublished post (admin only)
     */
    public function preview(string $slug)
    {
        if (! auth()->check() || ! auth()->user()->isAdministrator()) {
            abort(403, 'Unauthorized');
        }

        $post = BlogPost::where('slug', $slug)
            ->with(['author', 'categories', 'tags'])
            ->firstOrFail();

        $relatedPosts = collect();

        return view('blog.show', compact('post', 'relatedPosts'))
            ->with('preview', true);
    }

    /**
     * Full-text search across posts
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (! $query) {
            return redirect()->route('blog.index');
        }

        $posts = BlogPost::published()
            ->with(['author', 'categories', 'tags'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('seo_title', 'like', "%{$query}%")
                    ->orWhere('seo_description', 'like', "%{$query}%")
                    ->orWhere('seo_keywords', 'like', "%{$query}%");
            })
            ->orderByDesc('published_at')
            ->paginate(12);

        $categories = BlogCategory::has('publishedPosts')->orderBy('name')->get();
        $popularTags = BlogTag::has('publishedPosts')->withCount('posts')->orderByDesc('posts_count')->limit(20)->get();

        return view('blog.search', compact('posts', 'query', 'categories', 'popularTags'));
    }
}
