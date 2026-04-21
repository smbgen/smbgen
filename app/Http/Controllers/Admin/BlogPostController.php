<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    /**
     * Display a listing of blog posts
     */
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'categories', 'tags']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('blog_categories.id', $request->category);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderByDesc('created_at')->paginate(20);
        $categories = BlogCategory::orderBy('name')->get();
        $statuses = ['draft', 'scheduled', 'published', 'archived'];

        return view('admin.blog.posts.index', compact('posts', 'categories', 'statuses'));
    }

    /**
     * Show the form for creating a new blog post
     */
    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get();
        $tags = BlogTag::orderBy('name')->get();

        return view('admin.blog.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created blog post
     */
    public function store(StoreBlogPostRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        // Handle published_at based on status
        if ($data['status'] === 'published' && empty($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        // If content_blocks is empty, set it to null so WYSIWYG content is used
        if (isset($data['content_blocks']) && empty($data['content_blocks'])) {
            $data['content_blocks'] = null;
        }

        $post = BlogPost::create($data);

        // Sync categories and tags
        if ($request->has('categories')) {
            $post->categories()->sync($request->categories);
        }

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        ActivityLogger::log('blog_post_created', 'Created blog post: '.$post->title, $post);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Blog post created successfully!');
    }

    /**
     * Show the form for editing a blog post
     */
    public function edit(BlogPost $post)
    {
        $post->load(['categories', 'tags']);
        $categories = BlogCategory::orderBy('name')->get();
        $tags = BlogTag::orderBy('name')->get();

        return view('admin.blog.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified blog post
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $post)
    {
        $data = $request->validated();

        // Handle published_at based on status
        if ($data['status'] === 'published' && empty($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        // If content_blocks is empty, set it to null so WYSIWYG content is used
        if (isset($data['content_blocks']) && empty($data['content_blocks'])) {
            $data['content_blocks'] = null;
        }

        $post->update($data);

        // Sync categories and tags
        if ($request->has('categories')) {
            $post->categories()->sync($request->categories);
        } else {
            $post->categories()->sync([]);
        }

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->sync([]);
        }

        ActivityLogger::log('blog_post_updated', 'Updated blog post: '.$post->title, $post);

        $statusLabel = match ($post->status) {
            'published' => 'Published',
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'archived' => 'Archived',
            default => ucfirst($post->status),
        };

        return redirect()->route('admin.blog.posts.edit', $post)
            ->with('success', "Post saved; {$statusLabel}");
    }

    /**
     * Remove the specified blog post
     */
    public function destroy(BlogPost $post)
    {
        $title = $post->title;
        $post->delete();

        ActivityLogger::log('blog_post_deleted', 'Deleted blog post: '.$title);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Blog post deleted successfully!');
    }
}
