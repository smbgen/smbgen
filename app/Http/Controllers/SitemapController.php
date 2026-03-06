<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\CmsPage;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap
     */
    public function index(): Response
    {
        $posts = BlogPost::published()
            ->orderByDesc('updated_at')
            ->get();

        $categories = BlogCategory::has('posts')->get();
        $pages = CmsPage::published()->get();

        $xml = view('sitemap.xml', compact('posts', 'categories', 'pages'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }
}
