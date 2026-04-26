<?php

use App\Models\Client;
use App\Models\Package;
use App\Models\PackageFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('private');

    $this->clientUser = User::factory()->client()->create([
        'email' => 'client@example.com',
    ]);

    $this->otherClientUser = User::factory()->client()->create([
        'email' => 'other-client@example.com',
    ]);

    $this->adminUser = User::factory()->admin()->create();

    $this->client = Client::factory()->create([
        'email' => $this->clientUser->email,
        'user_provisioned_at' => now(),
    ]);

    $this->otherClient = Client::factory()->create([
        'email' => $this->otherClientUser->email,
        'user_provisioned_at' => now(),
    ]);
});

it('shows only the authenticated clients visible presentations', function (): void {
    $visiblePackage = Package::create([
        'name' => 'Q2 Growth Deck',
        'client_id' => $this->client->id,
        'created_by_user_id' => $this->adminUser->id,
        'status' => 'ready',
        'source' => 'zip_upload',
        'portal_enabled' => true,
    ]);

    PackageFile::create([
        'package_id' => $visiblePackage->id,
        'original_name' => 'growth-deck.html',
        'display_name' => 'Growth Deck',
        'type' => 'HTML_PRESENTATION',
        'role' => 'deliverable',
        'storage_path' => 'packages/'.$this->client->id.'/growth-deck.html',
        'storage_disk' => 'private',
        'size_bytes' => 1024,
        'portal_promoted' => true,
    ]);

    $hiddenPackage = Package::create([
        'name' => 'Internal Working Draft',
        'client_id' => $this->client->id,
        'created_by_user_id' => $this->adminUser->id,
        'status' => 'draft',
        'source' => 'zip_upload',
        'portal_enabled' => false,
    ]);

    PackageFile::create([
        'package_id' => $hiddenPackage->id,
        'original_name' => 'draft.html',
        'display_name' => 'Draft',
        'type' => 'HTML_PRESENTATION',
        'role' => 'deliverable',
        'storage_path' => 'packages/'.$this->client->id.'/draft.html',
        'storage_disk' => 'private',
        'size_bytes' => 512,
        'portal_promoted' => true,
    ]);

    $otherClientPackage = Package::create([
        'name' => 'Other Client Deck',
        'client_id' => $this->otherClient->id,
        'created_by_user_id' => $this->adminUser->id,
        'status' => 'ready',
        'source' => 'zip_upload',
        'portal_enabled' => true,
    ]);

    PackageFile::create([
        'package_id' => $otherClientPackage->id,
        'original_name' => 'other.html',
        'display_name' => 'Other Deck',
        'type' => 'HTML_PRESENTATION',
        'role' => 'deliverable',
        'storage_path' => 'packages/'.$this->otherClient->id.'/other.html',
        'storage_disk' => 'private',
        'size_bytes' => 2048,
        'portal_promoted' => true,
    ]);

    $response = $this->actingAs($this->clientUser)
        ->get(route('client.presentations.index'));

    $response->assertOk();
    $response->assertSee('Client Presentations');
    $response->assertSee('Q2 Growth Deck');
    $response->assertDontSee('Internal Working Draft');
    $response->assertDontSee('Other Client Deck');
});

it('shows recent presentations on the client dashboard', function (): void {
    $package = Package::create([
        'name' => 'Launch Presentation',
        'client_id' => $this->client->id,
        'created_by_user_id' => $this->adminUser->id,
        'status' => 'sent',
        'source' => 'multi_file',
        'portal_enabled' => true,
    ]);

    PackageFile::create([
        'package_id' => $package->id,
        'original_name' => 'launch.pdf',
        'display_name' => 'Launch Slides',
        'type' => 'PDF_DOCUMENT',
        'role' => 'deliverable',
        'storage_path' => 'packages/'.$this->client->id.'/launch.pdf',
        'storage_disk' => 'private',
        'size_bytes' => 2048,
        'portal_promoted' => true,
    ]);

    $response = $this->actingAs($this->clientUser)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Client Presentations');
    $response->assertSee('Launch Presentation');
});

it('allows a client to preview only their promoted presentation files', function (): void {
    $package = Package::create([
        'name' => 'Preview Deck',
        'client_id' => $this->client->id,
        'created_by_user_id' => $this->adminUser->id,
        'status' => 'ready',
        'source' => 'zip_upload',
        'portal_enabled' => true,
    ]);

    $file = PackageFile::create([
        'package_id' => $package->id,
        'original_name' => 'preview.html',
        'display_name' => 'Preview Deck',
        'type' => 'HTML_PRESENTATION',
        'role' => 'deliverable',
        'storage_path' => 'packages/'.$this->client->id.'/preview.html',
        'storage_disk' => 'private',
        'size_bytes' => 1024,
        'portal_promoted' => true,
    ]);

    Storage::disk('private')->put($file->storage_path, '<html><body>Preview Deck</body></html>');

    $response = $this->actingAs($this->clientUser)
        ->get(route('client.presentations.files.preview', [$package, $file]));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('text/html');
    $response->assertSee('Preview Deck', false);
});

it('forbids clients from opening another clients presentation files', function (): void {
    $package = Package::create([
        'name' => 'Protected Deck',
        'client_id' => $this->client->id,
        'created_by_user_id' => $this->adminUser->id,
        'status' => 'ready',
        'source' => 'zip_upload',
        'portal_enabled' => true,
    ]);

    $file = PackageFile::create([
        'package_id' => $package->id,
        'original_name' => 'protected.html',
        'display_name' => 'Protected Deck',
        'type' => 'HTML_PRESENTATION',
        'role' => 'deliverable',
        'storage_path' => 'packages/'.$this->client->id.'/protected.html',
        'storage_disk' => 'private',
        'size_bytes' => 1024,
        'portal_promoted' => true,
    ]);

    Storage::disk('private')->put($file->storage_path, '<html><body>Protected Deck</body></html>');

    $response = $this->actingAs($this->otherClientUser)
        ->get(route('client.presentations.files.preview', [$package, $file]));

    $response->assertForbidden();
});

it('shows portal packages when promoted files are not deliverables', function (): void {
    $package = Package::create([
        'name' => 'Research Package',
        'client_id' => $this->client->id,
        'created_by_user_id' => $this->adminUser->id,
        'status' => 'ready',
        'source' => 'zip_upload',
        'portal_enabled' => true,
    ]);

    $file = PackageFile::create([
        'package_id' => $package->id,
        'original_name' => 'research.md',
        'display_name' => 'Research Notes',
        'type' => 'MARKDOWN_RESEARCH',
        'role' => 'research',
        'storage_path' => 'packages/'.$this->client->id.'/research.md',
        'storage_disk' => 'private',
        'size_bytes' => 512,
        'portal_promoted' => true,
    ]);

    Storage::disk('private')->put($file->storage_path, '# Research Notes');

    $indexResponse = $this->actingAs($this->clientUser)
        ->get(route('client.presentations.index'));

    $indexResponse->assertOk();
    $indexResponse->assertSee('Research Package');

    $showResponse = $this->actingAs($this->clientUser)
        ->get(route('client.presentations.show', $package));

    $showResponse->assertOk();
    $showResponse->assertSee('Research Notes');

    $previewResponse = $this->actingAs($this->clientUser)
        ->get(route('client.presentations.files.preview', [$package, $file]));

    $previewResponse->assertOk();
    $previewResponse->assertSee('Research Notes', false);
});
