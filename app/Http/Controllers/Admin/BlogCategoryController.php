<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogCategoryRequest;
use App\Http\Requests\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use App\Services\ActivityLogger;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = BlogCategory::withCount('posts')
            ->with('parent')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('admin.blog.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blog.categories.create', compact('categories'));
    }

    /**
     * Store a newly created category
     */
    public function store(StoreBlogCategoryRequest $request)
    {
        $category = BlogCategory::create($request->validated());

        ActivityLogger::log('blog_category_created', 'Created blog category: '.$category->name, $category);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing a category
     */
    public function edit(BlogCategory $category)
    {
        $categories = BlogCategory::where('id', '!=', $category->id)->orderBy('name')->get();

        return view('admin.blog.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category
     */
    public function update(UpdateBlogCategoryRequest $request, BlogCategory $category)
    {
        $category->update($request->validated());

        ActivityLogger::log('blog_category_updated', 'Updated blog category: '.$category->name, $category);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(BlogCategory $category)
    {
        $name = $category->name;
        $category->delete();

        ActivityLogger::log('blog_category_deleted', 'Deleted blog category: '.$name);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
