<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogSearchController extends Controller
{
    /**
     * Handle blog search requests
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('q', '');

        $posts = BlogPost::published()
            ->with(['author', 'categories', 'tags'])
            ->search($searchTerm)
            ->orderByDesc('published_at')
            ->paginate(config('blog.search_results_per_page', 15));

        return view('blog.search', [
            'posts' => $posts,
            'searchTerm' => $searchTerm,
            'totalResults' => $posts->total(),
        ]);
    }
}
