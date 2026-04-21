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

it('shows standard organization login when tenancy is disabled', function () {
    config()->set('app.tenancy_enabled', false);

    $response = $this->get('/login');

    $response->assertOk()
        ->assertSee(config('app.company_name', config('app.name')))
        ->assertSee('Welcome back')
        ->assertSee('Organization Login')
        ->assertSee('Create a new account')
        ->assertSee('One-click login with Google')
        ->assertSee('Forgot your password?')
        ->assertSee('Switch to dark mode', false)
        ->assertDontSee('Create a new workspace');
});

it('shows workspace creation path on login when tenancy is enabled', function () {
    config()->set('app.tenancy_enabled', true);

    $response = $this->get('/login');

    $response->assertOk()
        ->assertSee('Create a new workspace')
        ->assertSee('Start registration');
});
