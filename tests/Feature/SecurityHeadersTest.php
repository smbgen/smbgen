<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

it('applies security headers to responses', function () {
    $response = $this->get('/');

    $response->assertHeader('Content-Security-Policy')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-XSS-Protection', '1; mode=block')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
});

it('applies HSTS header in production', function () {
    // Set environment to production
    Config::set('app.env', 'production');

    $response = $this->get('/');

    $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
});

it('does not apply HSTS header in non-production', function () {
    // Set environment to local
    Config::set('app.env', 'local');

    $response = $this->get('/');

    $response->assertHeaderMissing('Strict-Transport-Security');
});

it('does not redirect to HTTPS in non-production', function () {
    // Set environment to local
    Config::set('app.env', 'local');

    $response = $this->get('/');

    // Should not redirect
    $response->assertStatus(200);
});
