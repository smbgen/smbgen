<?php

use App\Models\CmsImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can view cms images index', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    $response = $this->actingAs($admin)->get(route('admin.cms.images.index'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.cms.images.index');
});

test('admin can upload cms image', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    Storage::fake('public_cloud');

    $file = UploadedFile::fake()->image('test-image.jpg', 100, 100);

    $response = $this->actingAs($admin)->post(route('admin.cms.images.store'), [
        'images' => [$file],
        'bulk_alt_text' => 'Test image alt text',
    ]);

    $response->assertRedirect(route('admin.cms.images.index'));
    $this->assertDatabaseHas('cms_images', [
        'original_name' => 'test-image.jpg',
        'alt_text' => 'Test image alt text',
        'user_id' => $admin->id,
    ]);
});

test('cms image upload validates required fields', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    $response = $this->actingAs($admin)->post(route('admin.cms.images.store'), []);

    $response->assertSessionHasErrors(['images']);
});

test('cms image upload validates file type', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    $file = UploadedFile::fake()->create('test.txt', 100);

    $response = $this->actingAs($admin)->post(route('admin.cms.images.store'), [
        'images' => [$file],
    ]);

    $response->assertSessionHasErrors(['images.0']);
});

test('admin can update cms image', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $image = CmsImage::factory()->create(['user_id' => $admin->id]);

    $response = $this->actingAs($admin)->put(route('admin.cms.images.update', $image), [
        'alt_text' => 'Updated alt text',
    ]);

    $response->assertRedirect(route('admin.cms.images.index'));
    $this->assertDatabaseHas('cms_images', [
        'id' => $image->id,
        'alt_text' => 'Updated alt text',
    ]);
});

test('admin can delete cms image', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $image = CmsImage::factory()->create(['user_id' => $admin->id]);

    Storage::fake('public_cloud');
    Storage::disk('public_cloud')->put($image->path, 'fake content');

    $response = $this->actingAs($admin)->delete(route('admin.cms.images.destroy', $image));

    $response->assertRedirect(route('admin.cms.images.index'));
    $this->assertDatabaseMissing('cms_images', ['id' => $image->id]);
    Storage::disk('public_cloud')->assertMissing($image->path);
});

test('api returns cms images list', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $images = CmsImage::factory()->count(3)->create(['user_id' => $admin->id]);

    $response = $this->actingAs($admin)->get(route('admin.cms.images.api'));

    $response->assertStatus(200);
    $response->assertJsonCount(3, 'images');
});

test('cms image model returns correct url', function () {
    $image = CmsImage::factory()->create(['path' => 'cms/test.jpg']);

    expect($image->getUrl())->toContain('/assets/');
    expect($image->getUrl())->toContain('cms/test.jpg');
});

test('assets route can serve cms image from local disk', function () {
    Storage::fake('public_cloud');

    $path = 'cms/images/test-asset.png';
    Storage::disk('public_cloud')->put($path, 'image-bytes');

    $response = $this->get('/assets/'.$path);

    $response->assertOk();
    expect($response->getContent())->toBe('image-bytes');
});

test('cms image model returns correct img tag', function () {
    $image = CmsImage::factory()->create([
        'path' => 'cms/test.jpg',
        'alt_text' => 'Test image',
    ]);

    $imgTag = $image->getImgTag();
    expect($imgTag)->toContain('<img');
    expect($imgTag)->toContain('src=');
    expect($imgTag)->toContain('alt="Test image"');
});

test('cms image model returns correct markdown', function () {
    $image = CmsImage::factory()->create([
        'path' => 'cms/test.jpg',
        'alt_text' => 'Test image',
    ]);

    $markdown = $image->getMarkdown();
    expect($markdown)->toContain('![');
    expect($markdown)->toContain('](http');
});
