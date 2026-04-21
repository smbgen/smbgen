<?php

use App\Models\BusinessSetting;

beforeEach(function (): void {
    config()->set('modules.registry.frontend_site.default_enabled', true);
    // Ensure module enabled state is stored in DB for middleware check
    BusinessSetting::set('module_frontend_site_enabled', true, 'boolean');
});

it('renders the home page with smbgen core messaging', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('smbgen-core');
    $response->assertSee('Contact');
    $response->assertSee('Client Portal');
});

it('renders the services page with smbgen core services messaging', function () {
    $response = $this->get(route('home.services'));

    $response->assertOk();
    $response->assertSee('Services layer for smbgen-core');
    $response->assertSee('Implementation');
    $response->assertSee('Growth');
});

it('renders the solutions page with simple smbgen core explanation pages', function () {
    $response = $this->get(route('solutions'));

    $response->assertOk();
    $response->assertSee('Portal access. CRM. CMS.');
    $response->assertSee('A superior contact form.');
    $response->assertSee('A CRM your team will actually use.');
});

it('shows the smbgen github org link in public navigation', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('https://github.com/smbgen', false);
    $response->assertSee('smbgen org');
});
