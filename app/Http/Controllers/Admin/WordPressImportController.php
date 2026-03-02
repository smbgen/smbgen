<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WordPressImportController extends Controller
{
    /**
     * Show the import form
     */
    public function index()
    {
        return view('admin.blog.import');
    }

    /**
     * Process the WordPress XML import
     */
    public function import(Request $request)
    {
        $request->validate([
            'wordpress_xml' => 'required|file|mimes:xml|max:10240',
            'import_categories' => 'boolean',
            'import_tags' => 'boolean',
            'set_as_published' => 'boolean',
        ]);

        try {
            $xml = simplexml_load_file($request->file('wordpress_xml')->path());

            if (! $xml) {
                return back()->withErrors(['wordpress_xml' => 'Invalid WordPress export file.']);
            }

            $stats = [
                'posts' => 0,
                'categories' => 0,
                'tags' => 0,
                'skipped' => 0,
            ];

            DB::beginTransaction();

            // Import categories first
            $categoryMap = [];
            if ($request->import_categories) {
                $categoryMap = $this->importCategories($xml);
                $stats['categories'] = count($categoryMap);
            }

            // Import tags
            $tagMap = [];
            if ($request->import_tags) {
                $tagMap = $this->importTags($xml);
                $stats['tags'] = count($tagMap);
            }

            // Import posts
            foreach ($xml->channel->item as $item) {
                $postType = (string) $item->children('wp', true)->post_type;

                if ($postType !== 'post') {
                    $stats['skipped']++;

                    continue;
                }

                $status = (string) $item->children('wp', true)->status;
                $post = $this->importPost($item, $categoryMap, $tagMap, (bool) $request->set_as_published);

                if ($post) {
                    $stats['posts']++;
                }
            }

            DB::commit();

            ActivityLogger::log('wordpress_import', 'Imported '.$stats['posts'].' posts from WordPress');

            return redirect()->route('admin.blog.posts.index')
                ->with('success', "Successfully imported {$stats['posts']} posts, {$stats['categories']} categories, and {$stats['tags']} tags. {$stats['skipped']} items skipped.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['wordpress_xml' => 'Import failed: '.$e->getMessage()]);
        }
    }

    /**
     * Import WordPress categories
     */
    protected function importCategories($xml): array
    {
        $categoryMap = [];

        foreach ($xml->channel->children('wp', true)->category as $wpCategory) {
            $slug = (string) $wpCategory->category_nicename;
            $name = (string) $wpCategory->cat_name;

            // Skip uncategorized
            if (strtolower($slug) === 'uncategorized') {
                continue;
            }

            $category = BlogCategory::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => (string) $wpCategory->category_description,
                ]
            );

            $categoryMap[$slug] = $category->id;
        }

        return $categoryMap;
    }

    /**
     * Import WordPress tags
     */
    protected function importTags($xml): array
    {
        $tagMap = [];

        foreach ($xml->channel->children('wp', true)->tag as $wpTag) {
            $slug = (string) $wpTag->tag_slug;
            $name = (string) $wpTag->tag_name;

            $tag = BlogTag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );

            $tagMap[$slug] = $tag->id;
        }

        return $tagMap;
    }

    /**
     * Import a WordPress post
     */
    protected function importPost($item, array $categoryMap, array $tagMap, bool $setAsPublished): ?BlogPost
    {
        $title = (string) $item->title;
        $slug = (string) $item->children('wp', true)->post_name;

        // Skip if slug already exists
        if (BlogPost::where('slug', $slug)->exists()) {
            return null;
        }

        $content = (string) $item->children('content', true)->encoded;
        $excerpt = (string) $item->children('excerpt', true)->encoded;
        $pubDate = (string) $item->pubDate;

        // Convert HTML content to content blocks
        $contentBlocks = $this->convertHtmlToBlocks($content);

        // Determine status
        $wpStatus = (string) $item->children('wp', true)->status;
        $status = match ($wpStatus) {
            'publish' => $setAsPublished ? 'published' : 'draft',
            'draft' => 'draft',
            'future' => 'scheduled',
            default => 'draft',
        };

        $publishedAt = $status === 'published' ? now() : null;
        if ($pubDate) {
            try {
                $publishedAt = \Carbon\Carbon::parse($pubDate);
            } catch (\Exception $e) {
                // Use default
            }
        }

        $post = BlogPost::create([
            'title' => $title,
            'slug' => $slug ?: Str::slug($title),
            'excerpt' => strip_tags($excerpt),
            'content' => $content,
            'content_blocks' => $contentBlocks,
            'author_id' => auth()->id(),
            'status' => $status,
            'published_at' => $publishedAt,
            'seo_title' => $title,
            'seo_description' => Str::limit(strip_tags($excerpt ?: $content), 160),
        ]);

        // Attach categories
        $postCategories = [];
        foreach ($item->category as $category) {
            $domain = (string) $category->attributes()->domain;
            $slug = (string) $category->attributes()->nicename;

            if ($domain === 'category' && isset($categoryMap[$slug])) {
                $postCategories[] = $categoryMap[$slug];
            }
        }
        if (! empty($postCategories)) {
            $post->categories()->sync($postCategories);
        }

        // Attach tags
        $postTags = [];
        foreach ($item->category as $category) {
            $domain = (string) $category->attributes()->domain;
            $slug = (string) $category->attributes()->nicename;

            if ($domain === 'post_tag' && isset($tagMap[$slug])) {
                $postTags[] = $tagMap[$slug];
            }
        }
        if (! empty($postTags)) {
            $post->tags()->sync($postTags);
        }

        return $post;
    }

    /**
     * Convert HTML content to content blocks
     */
    protected function convertHtmlToBlocks(string $html): array
    {
        $blocks = [];

        // Simple block extraction - can be enhanced
        // For now, split by paragraphs and headings

        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach ($dom->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                $tagName = $node->nodeName;
                $content = $dom->saveHTML($node);

                if (in_array($tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
                    $blocks[] = [
                        'type' => 'heading',
                        'content' => strip_tags($content),
                        'level' => $tagName,
                    ];
                } elseif ($tagName === 'blockquote') {
                    $blocks[] = [
                        'type' => 'quote',
                        'content' => strip_tags($content),
                    ];
                } elseif ($tagName === 'pre' || $tagName === 'code') {
                    $blocks[] = [
                        'type' => 'code',
                        'content' => strip_tags($content),
                        'language' => 'text',
                    ];
                } elseif ($tagName === 'img') {
                    $blocks[] = [
                        'type' => 'image',
                        'url' => $node->getAttribute('src'),
                        'alt' => $node->getAttribute('alt'),
                    ];
                } else {
                    // Regular content
                    if (trim($content)) {
                        $blocks[] = [
                            'type' => 'text',
                            'content' => $content,
                        ];
                    }
                }
            }
        }

        return ! empty($blocks) ? $blocks : null;
    }
}
