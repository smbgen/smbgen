<?php

it('returns not found for frontend home when frontend module is disabled', function () {
    config()->set('modules.registry.frontend_site.default_enabled', false);

    $response = $this->get('/');

    $response->assertNotFound();
});

it('serves frontend home when frontend module is enabled', function () {
    config()->set('modules.registry.frontend_site.default_enabled', true);

    $response = $this->get('/');

    $response->assertOk();
});
