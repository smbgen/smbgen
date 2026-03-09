<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsMcpController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CmsPage::query();

        if ($request->query('published_only')) {
            $query->published();
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $pages = $query->orderBy('updated_at', 'desc')
            ->limit($request->query('limit', 50))
            ->get(['id', 'slug', 'title', 'is_published', 'has_form', 'show_navbar', 'show_footer', 'updated_at']);

        return response()->json([
            'count' => $pages->count(),
            'pages' => $pages,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $page = CmsPage::findOrFail($id);

        return response()->json(['page' => $page]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'                   => ['required', 'string', 'max:255'],
            'slug'                    => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', 'unique:cms_pages,slug'],
            'head_content'            => ['nullable', 'string'],
            'body_content'            => ['nullable', 'string'],
            'cta_text'                => ['nullable', 'string', 'max:255'],
            'cta_url'                 => ['nullable', 'string', 'max:500'],
            'is_published'            => ['nullable', 'boolean'],
            'show_navbar'             => ['nullable', 'boolean'],
            'show_footer'             => ['nullable', 'boolean'],
            'has_form'                => ['nullable', 'boolean'],
            'notification_email'      => ['nullable', 'email', 'max:255'],
            'form_submit_button_text' => ['nullable', 'string', 'max:100'],
            'form_success_message'    => ['nullable', 'string', 'max:500'],
        ]);

        // Auto-generate slug from title if not provided
        if (empty($validated['slug'])) {
            $base = Str::slug($validated['title']);
            $slug = $base;
            $i = 1;
            while (CmsPage::where('slug', $slug)->exists()) {
                $slug = "{$base}-{$i}";
                $i++;
            }
            $validated['slug'] = $slug;
        }

        $defaults = [
            'is_published' => false,
            'show_navbar'  => true,
            'show_footer'  => true,
            'has_form'     => false,
        ];

        $page = CmsPage::create(array_merge($defaults, $validated));

        return response()->json(['page' => $page], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $page = CmsPage::findOrFail($id);

        $validated = $request->validate([
            'title'                   => ['sometimes', 'string', 'max:255'],
            'head_content'            => ['nullable', 'string'],
            'body_content'            => ['nullable', 'string'],
            'cta_text'                => ['nullable', 'string', 'max:255'],
            'cta_url'                 => ['nullable', 'string', 'max:500'],
            'is_published'            => ['sometimes', 'boolean'],
            'show_navbar'             => ['sometimes', 'boolean'],
            'show_footer'             => ['sometimes', 'boolean'],
            'has_form'                => ['sometimes', 'boolean'],
            'notification_email'      => ['nullable', 'email', 'max:255'],
            'form_submit_button_text' => ['nullable', 'string', 'max:100'],
            'form_success_message'    => ['nullable', 'string', 'max:500'],
        ]);

        $page->update($validated);

        return response()->json(['page' => $page->fresh()]);
    }
}
