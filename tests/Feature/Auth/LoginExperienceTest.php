<?php

beforeEach(function () {
    $this->withoutVite();
});

it('shows give it a try on the public homepage', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Give it a try');
});

it('shows registration and google guidance on the login screen', function () {
    $response = $this->get('/login');

    $response->assertOk()
        ->assertSee('Create a new workspace')
        ->assertSee('Start registration')
        ->assertSee('One-click login with Google')
        ->assertSee('Flow Map');
});
