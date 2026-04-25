<?php

use App\Models\CmsPage;

it('redirects to login when frontend module is disabled for guests', function () {
    config()->set('modules.registry.frontend_site.default_enabled', false);

    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

it('renders published cms home for guests when frontend module is disabled', function () {
    config()->set('modules.registry.frontend_site.default_enabled', false);

    CmsPage::factory()->create([
        'slug' => 'home',
        'title' => 'Homepage',
        'body_content' => '<h1>Public Home</h1>',
        'is_published' => true,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Homepage');
    $response->assertSee('Public Home', false);
});

it('serves frontend home when frontend module is enabled', function () {
    config()->set('modules.registry.frontend_site.default_enabled', true);

    $response = $this->get('/');

    $response->assertOk();
});
