<?php

use App\Models\CmsPage;

it('displays a published cms page to the public', function () {
    $page = CmsPage::create([
        'slug' => 'about-us',
        'title' => 'About Us',
        'body_content' => '<h1>Welcome to our company</h1><p>We are awesome!</p>',
        'is_published' => true,
    ]);

    $response = $this->get('/about-us');

    $response->assertOk();
    $response->assertSee('About Us');
    $response->assertSee('Welcome to our company');
    $response->assertSee('We are awesome!', false);
});

it('returns 404 for unpublished cms pages', function () {
    CmsPage::create([
        'slug' => 'draft-page',
        'title' => 'Draft Page',
        'body_content' => '<p>Secret content</p>',
        'is_published' => false,
    ]);

    $response = $this->get('/draft-page');

    $response->assertNotFound();
});

it('returns 404 for non-existent cms pages', function () {
    $response = $this->get('/non-existent-page');

    $response->assertNotFound();
});

it('displays cta button when present', function () {
    $page = CmsPage::create([
        'slug' => 'services',
        'title' => 'Our Services',
        'body_content' => '<p>We offer great services</p>',
        'cta_text' => 'Get Started',
        'cta_url' => '/contact',
        'is_published' => true,
    ]);

    $response = $this->get('/services');

    $response->assertOk();
    $response->assertSee('Get Started');
    $response->assertSee('/contact', false);
});

it('includes head content when present', function () {
    $page = CmsPage::create([
        'slug' => 'landing',
        'title' => 'Landing Page',
        'head_content' => '<style>.custom-class { color: red; }</style>',
        'body_content' => '<p>Content here</p>',
        'is_published' => true,
    ]);

    $response = $this->get('/landing');

    $response->assertOk();
    $response->assertSee('.custom-class { color: red; }', false);
});

it('applies custom background and text colors', function () {
    $page = CmsPage::create([
        'slug' => 'colored-page',
        'title' => 'Colored Page',
        'body_content' => '<p>Colorful content</p>',
        'background_color' => 'bg-blue-500',
        'text_color' => 'text-white',
        'is_published' => true,
    ]);

    $response = $this->get('/colored-page');

    $response->assertOk();
    $response->assertSee('bg-blue-500', false);
    $response->assertSee('text-white', false);
});

it('uses default colors when none specified', function () {
    $page = CmsPage::create([
        'slug' => 'default-colors',
        'title' => 'Default Colors',
        'body_content' => '<p>Default styling</p>',
        'is_published' => true,
    ]);

    $response = $this->get('/default-colors');

    $response->assertOk();
    $response->assertSee('bg-white', false);
    $response->assertSee('text-gray-900', false);
});
