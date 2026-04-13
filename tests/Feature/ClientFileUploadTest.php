<?php

use App\Models\Client;
use App\Models\ClientFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses()->group('file-improves');

beforeEach(function () {
    // Set up storage for testing
    Storage::fake('private');
    Storage::fake('public_cloud');
});

it('allows admin to upload a public file for a client', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    $file = UploadedFile::fake()->create('document.pdf', 1024); // 1MB file

    $this->actingAs($admin)
        ->post(route('admin.client.files.upload', $client), [
            'file' => $file,
            'is_public' => true,
            'description' => 'Test public document',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // Verify file record was created
    expect(ClientFile::count())->toBe(1);

    $clientFile = ClientFile::first();
    expect($clientFile->client_id)->toBe($client->id)
        ->and($clientFile->is_public)->toBeTrue()
        ->and($clientFile->description)->toBe('Test public document')
        ->and($clientFile->mime_type)->toBe('application/pdf')
        ->and($clientFile->file_size)->toBeGreaterThan(0)
        ->and($clientFile->uploaded_by)->toBe('admin');

    // Verify file was stored on public_cloud disk
    Storage::disk('public_cloud')->assertExists($clientFile->path);
});

it('allows admin to upload a private file for a client', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    $file = UploadedFile::fake()->image('photo.jpg');

    $this->actingAs($admin)
        ->post(route('admin.client.files.upload', $client), [
            'file' => $file,
            'is_public' => false,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $clientFile = ClientFile::first();
    expect($clientFile->is_public)->toBeFalse();

    // Verify file was stored on private disk
    Storage::disk('private')->assertExists($clientFile->path);
});

it('allows client to upload a file (always private)', function () {
    $user = User::factory()->create(['email' => 'client@example.com']);
    $client = Client::factory()->create([
        'email' => 'client@example.com',
        'user_provisioned_at' => now(), // Provision the client
    ]);

    $file = UploadedFile::fake()->create('report.docx', 500);

    $this->actingAs($user)
        ->post(route('client.files.upload'), [
            'file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $clientFile = ClientFile::first();
    expect($clientFile->client_id)->toBe($client->id)
        ->and($clientFile->is_public)->toBeFalse() // Client uploads are always private
        ->and($clientFile->uploaded_by)->toBe('client');

    Storage::disk('private')->assertExists($clientFile->path);
});

it('validates file upload size limit', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    $file = UploadedFile::fake()->create('huge-file.pdf', 102401); // 100MB + 1KB (over admin limit)

    $this->actingAs($admin)
        ->post(route('admin.client.files.upload', $client), [
            'file' => $file,
        ])
        ->assertSessionHasErrors('file');

    expect(ClientFile::count())->toBe(0);
});

it('allows admin to download a client file', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    Storage::fake('private');
    $file = UploadedFile::fake()->create('test.pdf', 100);
    $path = $file->storeAs("client_files/{$client->id}", 'test.pdf', 'private');

    $clientFile = ClientFile::create([
        'client_id' => $client->id,
        'filename' => 'test.pdf',
        'original_name' => 'test.pdf',
        'path' => $path,
        'uploaded_by' => 'admin',
        'mime_type' => 'application/pdf',
        'file_size' => 100 * 1024,
        'file_extension' => 'pdf',
        'is_public' => false,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.client.files.download', [$client, $clientFile]));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application');
});

it('allows client to download their own file', function () {
    $user = User::factory()->create(['email' => 'client@example.com']);
    $client = Client::factory()->create(['email' => 'client@example.com']);

    Storage::fake('private');
    $file = UploadedFile::fake()->create('my-file.pdf', 100);
    $path = $file->storeAs("client_files/{$client->id}", 'my-file.pdf', 'private');

    $clientFile = ClientFile::create([
        'client_id' => $client->id,
        'filename' => 'my-file.pdf',
        'original_name' => 'my-file.pdf',
        'path' => $path,
        'uploaded_by' => 'client',
        'mime_type' => 'application/pdf',
        'file_size' => 100 * 1024,
        'file_extension' => 'pdf',
        'is_public' => false,
    ]);

    $response = $this->actingAs($user)
        ->get(route('client.files.download', $clientFile));

    $response->assertStatus(200);
});

it('prevents client from downloading another clients file', function () {
    $user = User::factory()->create(['email' => 'client1@example.com']);
    $client1 = Client::factory()->create(['email' => 'client1@example.com']);
    $client2 = Client::factory()->create(['email' => 'client2@example.com']);

    Storage::fake('private');
    $file = UploadedFile::fake()->create('secret.pdf', 100);
    $path = $file->storeAs("client_files/{$client2->id}", 'secret.pdf', 'private');

    $clientFile = ClientFile::create([
        'client_id' => $client2->id,
        'filename' => 'secret.pdf',
        'original_name' => 'secret.pdf',
        'path' => $path,
        'uploaded_by' => 'admin',
        'mime_type' => 'application/pdf',
        'file_size' => 100 * 1024,
        'file_extension' => 'pdf',
        'is_public' => false,
    ]);

    $this->actingAs($user)
        ->get(route('client.files.download', $clientFile))
        ->assertForbidden();
});

it('allows admin to delete a client file', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    Storage::fake('private');
    $file = UploadedFile::fake()->create('to-delete.pdf', 100);
    $path = $file->storeAs("client_files/{$client->id}", 'to-delete.pdf', 'private');

    $clientFile = ClientFile::create([
        'client_id' => $client->id,
        'filename' => 'to-delete.pdf',
        'original_name' => 'to-delete.pdf',
        'path' => $path,
        'uploaded_by' => 'admin',
        'mime_type' => 'application/pdf',
        'file_size' => 100 * 1024,
        'file_extension' => 'pdf',
        'is_public' => false,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.client.files.destroy', [$client, $clientFile]))
        ->assertRedirect()
        ->assertSessionHas('success');

    // Verify file was deleted from storage
    Storage::disk('private')->assertMissing($path);

    // Verify database record was deleted
    expect(ClientFile::count())->toBe(0);
});

it('tracks file metadata correctly', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    $file = UploadedFile::fake()->image('photo.jpg')->size(2048); // 2MB

    $this->actingAs($admin)
        ->post(route('admin.client.files.upload', $client), [
            'file' => $file,
            'is_public' => true,
            'description' => 'Client profile photo',
        ]);

    $clientFile = ClientFile::first();

    expect($clientFile->mime_type)->toBe('image/jpeg')
        ->and($clientFile->file_extension)->toBe('jpg')
        ->and($clientFile->file_size)->toBeGreaterThan(0)
        ->and($clientFile->getFileIcon())->toBe('fa-file-image')
        ->and($clientFile->getFileCategory())->toBe('image')
        ->and($clientFile->isImage())->toBeTrue()
        ->and($clientFile->formatted_size)->toContain('MB');
});

it('uses correct storage disk based on visibility', function () {
    $publicFile = ClientFile::factory()->create(['is_public' => true]);
    $privateFile = ClientFile::factory()->create(['is_public' => false]);

    expect($publicFile->getStorageDisk())->toBe('public_cloud')
        ->and($privateFile->getStorageDisk())->toBe('private');
});

it('generates public URL only for public files', function () {
    $publicFile = ClientFile::factory()->create(['is_public' => true, 'path' => 'test/file.pdf']);
    $privateFile = ClientFile::factory()->create(['is_public' => false]);

    expect($publicFile->getPublicUrl())->not->toBeNull()
        ->and($publicFile->getPublicUrl())->toContain('storage')
        ->and($privateFile->getPublicUrl())->toBeNull();
});
