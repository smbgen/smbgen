<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;

class CmsPagePublicController extends Controller
{
    /**
     * Display a published CMS page by slug.
     *
     * Note: This controller is only reached if no other routes match.
     * The CMS catch-all route /{slug} is defined LAST in web.php,
     * so all specific routes (login, register, admin, etc.) will
     * match first and never reach this controller.
     */
    public function show(string $slug)
    {
        $page = CmsPage::findBySlug($slug);

        if (! $page || ! $page->is_published) {
            abort(404);
        }

        return view('cms.show', compact('page'));
    }
}
