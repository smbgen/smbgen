<?php

use App\Models\CmsPage;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

it('allows admin to duplicate a page', function () {
    $originalPage = CmsPage::factory()->create([
        'slug' => 'about-us',
        'title' => 'About Us',
        'body_content' => '<h1>About Our Company</h1>',
        'is_published' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.cms.duplicate', $originalPage));

    $response->assertRedirect();

    // Verify a new page was created
    expect(CmsPage::count())->toBe(2);

    // Get the duplicate
    $duplicate = CmsPage::where('slug', '!=', 'about-us')->first();

    expect($duplicate)->not->toBeNull();
    expect($duplicate->title)->toBe('About Us (Copy)');
    expect($duplicate->body_content)->toBe('<h1>About Our Company</h1>');
    expect($duplicate->is_published)->toBeFalse();
    expect($duplicate->slug)->toContain('about-us-copy-');
});

it('creates duplicate as draft even when original is published', function () {
    $originalPage = CmsPage::factory()->create([
        'slug' => 'services',
        'title' => 'Our Services',
        'is_published' => true,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.cms.duplicate', $originalPage));

    $duplicate = CmsPage::where('slug', '!=', 'services')->first();

    expect($duplicate->is_published)->toBeFalse();
});

it('preserves all content fields when duplicating', function () {
    $originalPage = CmsPage::factory()->create([
        'slug' => 'contact',
        'title' => 'Contact Us',
        'head_content' => '<style>body { margin: 0; }</style>',
        'body_content' => '<h1>Get in Touch</h1>',
        'footer_scripts' => '<script>console.log("test");</script>',
        'cta_text' => 'Call Now',
        'cta_url' => '/call',
        'background_color' => '#ffffff',
        'text_color' => '#000000',
        'show_navbar' => true,
        'show_footer' => false,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.cms.duplicate', $originalPage));

    $duplicate = CmsPage::where('slug', '!=', 'contact')->first();

    expect($duplicate->head_content)->toBe('<style>body { margin: 0; }</style>');
    expect($duplicate->body_content)->toBe('<h1>Get in Touch</h1>');
    expect($duplicate->footer_scripts)->toBe('<script>console.log("test");</script>');
    expect($duplicate->cta_text)->toBe('Call Now');
    expect($duplicate->cta_url)->toBe('/call');
    expect($duplicate->background_color)->toBe('#ffffff');
    expect($duplicate->text_color)->toBe('#000000');
    expect($duplicate->show_navbar)->toBeTrue();
    expect($duplicate->show_footer)->toBeFalse();
});

it('generates unique slug with timestamp', function () {
    $originalPage = CmsPage::factory()->create([
        'slug' => 'pricing',
        'title' => 'Pricing',
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.cms.duplicate', $originalPage));

    $duplicate = CmsPage::where('slug', '!=', 'pricing')->first();

    expect($duplicate->slug)->toStartWith('pricing-copy-');
    expect($duplicate->slug)->toMatch('/^pricing-copy-\d+$/');
});

it('redirects to edit page after duplication', function () {
    $originalPage = CmsPage::factory()->create([
        'slug' => 'blog',
        'title' => 'Blog',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.cms.duplicate', $originalPage));

    $duplicate = CmsPage::where('slug', '!=', 'blog')->first();

    $response->assertRedirect(route('admin.cms.edit', $duplicate));
    $response->assertSessionHas('success', 'Page duplicated successfully! Update the slug and publish when ready.');
});

it('requires authentication to duplicate', function () {
    $page = CmsPage::factory()->create();

    $response = $this->post(route('admin.cms.duplicate', $page));

    $response->assertRedirect(route('login'));
});

it('allows multiple duplicates of the same page', function () {
    $originalPage = CmsPage::factory()->create([
        'slug' => 'features',
        'title' => 'Features',
    ]);

    // Create first duplicate
    $this->actingAs($this->admin)
        ->post(route('admin.cms.duplicate', $originalPage));

    sleep(1); // Ensure different timestamps

    // Create second duplicate
    $this->actingAs($this->admin)
        ->post(route('admin.cms.duplicate', $originalPage));

    // Should have 3 pages total: original + 2 copies
    expect(CmsPage::count())->toBe(3);

    // Both duplicates should have different slugs
    $duplicates = CmsPage::where('slug', '!=', 'features')->get();
    expect($duplicates)->toHaveCount(2);
    expect($duplicates[0]->slug)->not->toBe($duplicates[1]->slug);
});
