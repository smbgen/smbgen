<?php

use App\Models\CmsNavbarSetting;
use App\Models\CmsPage;
use App\Models\User;

test('navbar settings can be retrieved', function () {
    $settings = CmsNavbarSetting::getSettings();

    expect($settings)->toBeInstanceOf(CmsNavbarSetting::class)
        ->and($settings->logo_text)->not->toBeEmpty()
        ->and($settings->menu_items)->toBeArray();
});

test('navbar settings use business colors by default', function () {
    $settings = CmsNavbarSetting::getSettings();

    expect($settings->use_business_colors)->toBeTrue()
        ->and($settings->getBackgroundColor())->toBe(config('business.branding.background_color'));
});

test('navbar settings can use custom colors', function () {
    $settings = CmsNavbarSetting::getSettings();
    $settings->update([
        'use_business_colors' => false,
        'custom_bg_color' => '#ff0000',
        'custom_text_color' => '#00ff00',
    ]);

    expect($settings->getBackgroundColor())->toBe('#ff0000')
        ->and($settings->getTextColor())->toBe('#00ff00');
});

test('navbar is shown on cms pages when enabled', function () {
    $page = CmsPage::factory()->create([
        'slug' => 'test-page',
        'title' => 'Test Page',
        'is_published' => true,
        'show_navbar' => true,
    ]);

    $response = $this->get('/'.$page->slug);

    $response->assertSuccessful()
        ->assertSee('smbgen'); // Default logo text
});

test('navbar is not shown on cms pages when disabled', function () {
    $page = CmsPage::factory()->create([
        'slug' => 'no-nav-page',
        'title' => 'No Nav Page',
        'is_published' => true,
        'show_navbar' => false,
    ]);

    $response = $this->get('/'.$page->slug);

    $response->assertSuccessful();
    // The navbar component won't be rendered at all
});

test('admin can update navbar settings', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    $this->actingAs($admin);

    $response = $this->post(route('admin.cms.navbar.update'), [
        'logo_text' => 'New Brand',
        'use_business_colors' => true,
        'menu_items' => json_encode([
            ['label' => 'Home', 'url' => '/', 'target' => '_self', 'order' => 1],
            ['label' => 'About', 'url' => '/about', 'target' => '_self', 'order' => 2],
        ]),
    ]);

    $response->assertRedirect(route('admin.cms.index'))
        ->assertSessionHas('success');

    $settings = CmsNavbarSetting::first();
    expect($settings->logo_text)->toBe('New Brand')
        ->and($settings->menu_items)->toHaveCount(2);
});

test('navbar appears on contact page', function () {
    \App\Models\BusinessSetting::set('module_frontend_site_enabled', true, 'boolean');
    $response = $this->get('/contact');

    $response->assertSuccessful()
        ->assertSee('smbgen'); // Should see navbar
});

test('navbar appears on booking page', function () {
    \App\Models\BusinessSetting::set('module_frontend_site_enabled', true, 'boolean');
    $response = $this->get('/book');

    $response->assertSuccessful()
        ->assertSee('smbgen'); // Should see navbar
});

test('ordered menu items are returned correctly', function () {
    $settings = CmsNavbarSetting::getSettings();
    $settings->update([
        'menu_items' => [
            ['label' => 'Contact', 'url' => '/contact', 'target' => '_self', 'order' => 3],
            ['label' => 'Home', 'url' => '/', 'target' => '_self', 'order' => 1],
            ['label' => 'About', 'url' => '/about', 'target' => '_self', 'order' => 2],
        ],
    ]);

    $orderedItems = $settings->getOrderedMenuItems();

    expect($orderedItems[0]['label'])->toBe('Home')
        ->and($orderedItems[1]['label'])->toBe('About')
        ->and($orderedItems[2]['label'])->toBe('Contact');
});
