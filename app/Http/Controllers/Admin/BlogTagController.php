<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogTagRequest;
use App\Http\Requests\UpdateBlogTagRequest;
use App\Models\BlogTag;
use App\Services\ActivityLogger;

class BlogTagController extends Controller
{
    /**
     * Display a listing of tags
     */
    public function index()
    {
        $tags = BlogTag::withCount('posts')
            ->orderBy('name')
            ->get();

        return view('admin.blog.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag
     */
    public function create()
    {
        return view('admin.blog.tags.create');
    }

    /**
     * Store a newly created tag
     */
    public function store(StoreBlogTagRequest $request)
    {
        $tag = BlogTag::create($request->validated());

        ActivityLogger::log('blog_tag_created', 'Created blog tag: '.$tag->name, $tag);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag created successfully!');
    }

    /**
     * Show the form for editing a tag
     */
    public function edit(BlogTag $tag)
    {
        return view('admin.blog.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag
     */
    public function update(UpdateBlogTagRequest $request, BlogTag $tag)
    {
        $tag->update($request->validated());

        ActivityLogger::log('blog_tag_updated', 'Updated blog tag: '.$tag->name, $tag);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag updated successfully!');
    }

    /**
     * Remove the specified tag
     */
    public function destroy(BlogTag $tag)
    {
        $name = $tag->name;
        $tag->delete();

        ActivityLogger::log('blog_tag_deleted', 'Deleted blog tag: '.$name);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag deleted successfully!');
    }
}
