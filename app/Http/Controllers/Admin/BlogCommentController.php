<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    /**
     * Display a listing of comments
     */
    public function index(Request $request)
    {
        $query = BlogComment::with(['post', 'user'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by post
        if ($request->filled('post_id')) {
            $query->where('blog_post_id', $request->post_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('author_email', 'like', "%{$search}%");
            });
        }

        $comments = $query->paginate(30);
        $posts = BlogPost::orderBy('title')->get();

        return view('admin.blog.comments.index', compact('comments', 'posts'));
    }

    /**
     * Bulk approve comments
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:blog_comments,id',
        ]);

        BlogComment::whereIn('id', $validated['comment_ids'])
            ->update(['status' => 'approved']);

        return back()->with('success', count($validated['comment_ids']).' comments approved.');
    }

    /**
     * Bulk delete comments
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:blog_comments,id',
        ]);

        BlogComment::whereIn('id', $validated['comment_ids'])->delete();

        return back()->with('success', count($validated['comment_ids']).' comments deleted.');
    }
}
