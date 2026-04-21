<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientFileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users and client
        $this->admin = User::factory()->admin()->create();
        $this->clientUser = User::factory()->client()->create();
        $this->otherClientUser = User::factory()->client()->create();

        // Create corresponding client records
        $this->client = Client::factory()->create([
            'email' => $this->clientUser->email,
            'user_provisioned_at' => now(),
        ]);
        $this->otherClient = Client::factory()->create([
            'email' => $this->otherClientUser->email,
            'user_provisioned_at' => now(),
        ]);

        Storage::fake('private');
        Storage::fake('public_cloud');
    }

    public function test_guest_cannot_access_client_files(): void
    {
        $response = $this->get('/documents');
        $response->assertRedirect('/login');
    }

    public function test_client_can_view_their_files(): void
    {
        // Create files for the client
        $file1 = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'test1.pdf',
            'uploaded_by' => 'client',
        ]);
        $file2 = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'test2.pdf',
            'uploaded_by' => 'client',
        ]);

        $response = $this->actingAs($this->clientUser)->get('/documents');

        $response->assertOk();
        $response->assertSee($file1->original_name);
        $response->assertSee($file2->original_name);
    }

    public function test_client_cannot_view_other_clients_files(): void
    {
        // Create file for other client
        $otherFile = ClientFile::factory()->create([
            'client_id' => $this->otherClient->id,
            'original_name' => 'other-client-file.pdf',
            'uploaded_by' => 'client',
        ]);

        $response = $this->actingAs($this->clientUser)->get('/documents');

        $response->assertOk();
        $response->assertDontSee($otherFile->original_name);
    }

    public function test_client_can_upload_file(): void
    {
        $file = UploadedFile::fake()->create('test-document.pdf', 1000); // 1KB file

        $response = $this->actingAs($this->clientUser)
            ->post('/documents/upload', [
                'file' => $file,
            ]);

        $response->assertRedirect('/documents');
        $response->assertSessionHas('success', 'File uploaded successfully.');

        $this->assertDatabaseHas('client_files', [
            'client_id' => $this->client->id,
            'original_name' => 'test-document.pdf',
            'uploaded_by' => 'client',
        ]);

        // Check that file was stored
        $clientFile = ClientFile::where('client_id', $this->client->id)->first();
        Storage::disk('private')->assertExists($clientFile->path);
    }

    public function test_client_cannot_upload_file_without_client_record(): void
    {
        // Create a user without a corresponding client record
        $userWithoutClient = User::factory()->client()->create();

        $file = UploadedFile::fake()->create('test.pdf', 1000);

        $response = $this->actingAs($userWithoutClient)
            ->post('/documents/upload', [
                'file' => $file,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('client_files', [
            'original_name' => 'test.pdf',
        ]);
    }

    public function test_file_upload_validation_requires_file(): void
    {
        $response = $this->actingAs($this->clientUser)
            ->post('/documents/upload', []);

        $response->assertSessionHasErrors(['file' => 'The file field is required.']);
    }

    public function test_file_upload_validation_max_size(): void
    {
        $largeFile = UploadedFile::fake()->create('large-file.pdf', 60000); // 60MB file (over 50MB limit)

        $response = $this->actingAs($this->clientUser)
            ->post('/documents/upload', [
                'file' => $largeFile,
            ]);

        $response->assertSessionHasErrors(['file' => 'The file field must not be greater than 51200 kilobytes.']);
    }

    public function test_client_can_download_their_file(): void
    {
        $file = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'test-download.pdf',
            'filename' => 'unique-test-download.pdf',
            'path' => 'client_files/'.$this->client->id.'/unique-test-download.pdf',
            'uploaded_by' => 'client',
        ]);

        // Create the file in storage
        Storage::disk('private')->put($file->path, 'fake file content');

        $response = $this->actingAs($this->clientUser)
            ->get("/documents/download/{$file->id}");

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename=test-download.pdf');
    }

    public function test_client_cannot_download_other_clients_file(): void
    {
        $otherFile = ClientFile::factory()->create([
            'client_id' => $this->otherClient->id,
            'original_name' => 'other-file.pdf',
            'uploaded_by' => 'client',
        ]);

        $response = $this->actingAs($this->clientUser)
            ->get("/documents/download/{$otherFile->id}");

        $response->assertForbidden();
    }

    public function test_client_can_delete_their_file(): void
    {
        $file = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'test-delete.pdf',
            'path' => 'client_files/'.$this->client->id.'/test-delete.pdf',
            'uploaded_by' => 'client',
        ]);

        // Create the file in storage
        Storage::disk('private')->put($file->path, 'fake file content');

        $response = $this->actingAs($this->clientUser)
            ->delete("/documents/{$file->id}");

        $response->assertRedirect('/documents');
        $response->assertSessionHas('success', 'File deleted successfully.');

        $this->assertDatabaseMissing('client_files', [
            'id' => $file->id,
        ]);

        // File should be deleted from storage
        Storage::disk('private')->assertMissing($file->path);
    }

    public function test_client_cannot_delete_other_clients_file(): void
    {
        $otherFile = ClientFile::factory()->create([
            'client_id' => $this->otherClient->id,
            'original_name' => 'other-file.pdf',
            'uploaded_by' => 'client',
        ]);

        $response = $this->actingAs($this->clientUser)
            ->delete("/documents/{$otherFile->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('client_files', [
            'id' => $otherFile->id,
        ]);
    }

    public function test_file_deletion_removes_file_from_storage(): void
    {
        $file = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'test-storage.pdf',
            'path' => 'client_files/'.$this->client->id.'/test-storage.pdf',
            'uploaded_by' => 'client',
        ]);

        // Create the file in storage
        Storage::disk('private')->put($file->path, 'fake file content');

        // Verify file exists
        Storage::disk('private')->assertExists($file->path);

        // Delete the file
        $this->actingAs($this->clientUser)->delete("/documents/{$file->id}");

        // File should be removed from storage
        Storage::disk('private')->assertMissing($file->path);
    }

    public function test_admin_cannot_access_client_file_endpoints(): void
    {
        // Admin should not be able to access client file endpoints
        $response = $this->actingAs($this->admin)->get('/documents');
        $response->assertForbidden();
    }

    public function test_unverified_client_cannot_access_portal(): void
    {
        // Create unverified client user
        $unverifiedUser = User::factory()->client()->create([
            'email_verified_at' => null,
        ]);

        Client::factory()->create([
            'email' => $unverifiedUser->email,
        ]);

        $response = $this->actingAs($unverifiedUser)->get('/documents');

        $response->assertRedirect('/verify-email');
    }

    public function test_unverified_client_cannot_upload_file(): void
    {
        // Create unverified client user
        $unverifiedUser = User::factory()->client()->create([
            'email_verified_at' => null,
        ]);

        Client::factory()->create([
            'email' => $unverifiedUser->email,
        ]);

        $file = UploadedFile::fake()->create('test-document.pdf', 1000);

        $response = $this->actingAs($unverifiedUser)
            ->post('/documents/upload', [
                'file' => $file,
            ]);

        $response->assertRedirect('/verify-email');
    }

    public function test_verified_client_can_access_portal(): void
    {
        // Ensure client user is verified
        $this->clientUser->email_verified_at = now();
        $this->clientUser->save();

        $response = $this->actingAs($this->clientUser)->get('/documents');

        $response->assertOk();
    }
}
