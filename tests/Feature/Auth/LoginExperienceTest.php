<?php

beforeEach(function () {
    $this->withoutVite();
});

it('shows give it a try on the public homepage', function () {
    config()->set('modules.registry.frontend_site.default_enabled', true);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Give it a try');
});

it('shows registration and google guidance on the login screen', function () {
    config()->set('app.tenancy_enabled', false);

    $response = $this->get('/login');

    $response->assertOk()
        ->assertSee('Organization Login')
        ->assertSee('Create a new account')
        ->assertSee('Forgot your password?')
        ->assertSee('One-click login with Google')
        ->assertSee('Flow Map');
});

it('shows workspace creation path on login when tenancy is enabled', function () {
    config()->set('app.tenancy_enabled', true);

    $response = $this->get('/login');

    $response->assertOk()
        ->assertSee('Create a new workspace')
        ->assertSee('Start registration');
});
