<?php

it('redirects to login when frontend module is disabled for guests', function () {
    config()->set('modules.registry.frontend_site.default_enabled', false);

    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

it('serves frontend home when frontend module is enabled', function () {
    config()->set('modules.registry.frontend_site.default_enabled', true);

    $response = $this->get('/');

    $response->assertOk();
});
