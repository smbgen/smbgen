<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class BlogCommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(Request $request, BlogPost $post)
    {
        // Rate limiting
        $key = 'comment-submit:'.($request->user()?->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['comment' => 'Too many comments. Please try again later.']);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:blog_comments,id',
            'author_name' => Auth::check() ? 'nullable' : 'required|string|max:255',
            'author_email' => Auth::check() ? 'nullable' : 'required|email|max:255',
        ]);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'author_name' => $validated['author_name'] ?? null,
            'author_email' => $validated['author_email'] ?? null,
            'content' => strip_tags($validated['content']),
            'status' => config('blog.auto_approve_comments', false) ? 'approved' : 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        RateLimiter::hit($key, 60);

        return back()->with('success', 'Comment submitted successfully. It will appear after approval.');
    }

    /**
     * Approve a comment (admin)
     */
    public function approve(BlogComment $comment)
    {
        $this->authorize('update', $comment);

        $comment->approve();

        return back()->with('success', 'Comment approved.');
    }

    /**
     * Reject a comment (admin)
     */
    public function reject(BlogComment $comment)
    {
        $this->authorize('update', $comment);

        $comment->reject();

        return back()->with('success', 'Comment rejected.');
    }

    /**
     * Mark comment as spam (admin)
     */
    public function spam(BlogComment $comment)
    {
        $this->authorize('update', $comment);

        $comment->markAsSpam();

        return back()->with('success', 'Comment marked as spam.');
    }

    /**
     * Delete a comment (admin)
     */
    public function destroy(BlogComment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
