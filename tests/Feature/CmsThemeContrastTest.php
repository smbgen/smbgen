<?php

use App\Models\CmsCompanyColors;
use App\Models\CmsPage;

it('defaults to readable cms text color on white background', function () {
    $settings = CmsCompanyColors::getSettings();

    $bodyBackground = strtolower($settings->body_background_color ?? '#ffffff');
    $textColor = strtolower($settings->text_color);

    if ($bodyBackground === '#ffffff') {
        expect($textColor)->not->toBe('#ffffff');
    }
});

it('does not inject nested style tags in the cms layout', function () {
    CmsPage::create([
        'slug' => 'contrast-test',
        'title' => 'Contrast Test',
        'body_content' => '<p>Content</p>',
        'is_published' => true,
    ]);

    $response = $this->get('/contrast-test');

    $response->assertOk();
    $response->assertDontSee('<style><style>', false);
    $response->assertDontSee('<style>\n<style>', false);
});

it('does not render cms theme with white brand text by default', function () {
    CmsPage::create([
        'slug' => 'contrast-css-test',
        'title' => 'Contrast CSS Test',
        'body_content' => '<p>Content</p>',
        'is_published' => true,
    ]);

    $response = $this->get('/contrast-css-test');

    $response->assertOk();
    $response->assertDontSee('--brand-text: #ffffff', false);
});
