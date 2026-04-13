<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminClientFileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users and client
        $this->admin = User::factory()->admin()->create();
        $this->clientUser = User::factory()->client()->create();
        $this->client = Client::factory()->create([
            'email' => $this->clientUser->email,
        ]);

        Storage::fake('private');
        Storage::fake('public_cloud');
    }

    public function test_guest_cannot_access_admin_client_files(): void
    {
        $response = $this->get("/admin/clients/{$this->client->id}/files");
        $response->assertRedirect('/login');
    }

    public function test_client_cannot_access_admin_client_files(): void
    {
        $response = $this->actingAs($this->clientUser)
            ->get("/admin/clients/{$this->client->id}/files");
        $response->assertForbidden();
    }

    public function test_admin_can_view_client_files(): void
    {
        // Create files for the client
        $file1 = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'admin-test1.pdf',
            'uploaded_by' => 'admin',
        ]);
        $file2 = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'admin-test2.pdf',
            'uploaded_by' => 'client',
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/clients/{$this->client->id}/files");

        $response->assertOk();
        $response->assertSee($file1->original_name);
        $response->assertSee($file2->original_name);
        $response->assertSee($this->client->name);
    }

    public function test_admin_can_view_all_clients_file_overview(): void
    {
        // Create another client with files
        $otherClient = Client::factory()->create();
        ClientFile::factory()->count(3)->create([
            'client_id' => $this->client->id,
        ]);
        ClientFile::factory()->count(2)->create([
            'client_id' => $otherClient->id,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/clients/files');

        $response->assertOk();
        $response->assertSee($this->client->name);
        $response->assertSee($otherClient->name);
        $response->assertSee('3'); // File count for first client
        $response->assertSee('2'); // File count for second client
    }

    public function test_admin_can_upload_file_for_client(): void
    {
        $file = UploadedFile::fake()->create('admin-upload.pdf', 2000); // 2KB file

        $response = $this->actingAs($this->admin)
            ->post("/admin/clients/{$this->client->id}/files", [
                'file' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'File uploaded successfully.');

        $this->assertDatabaseHas('client_files', [
            'client_id' => $this->client->id,
            'original_name' => 'admin-upload.pdf',
            'uploaded_by' => 'admin',
        ]);

        // Check that file was stored
        $clientFile = ClientFile::where('client_id', $this->client->id)
            ->where('original_name', 'admin-upload.pdf')
            ->first();
        Storage::disk('private')->assertExists($clientFile->path);
    }

    public function test_admin_file_upload_validation_max_size(): void
    {
        $largeFile = UploadedFile::fake()->create('large-admin-file.pdf', 120000); // 120MB file (over 100MB limit)

        $response = $this->actingAs($this->admin)
            ->post("/admin/clients/{$this->client->id}/files", [
                'file' => $largeFile,
            ]);

        $response->assertSessionHasErrors(['file' => 'The file field must not be greater than 102400 kilobytes.']);
    }

    public function test_admin_can_download_client_file(): void
    {
        $file = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'admin-download.pdf',
            'filename' => 'unique-admin-download.pdf',
            'path' => 'client_files/'.$this->client->id.'/unique-admin-download.pdf',
            'uploaded_by' => 'admin',
        ]);

        // Create the file in storage
        Storage::put($file->path, 'fake admin file content');

        $response = $this->actingAs($this->admin)
            ->get("/admin/clients/{$this->client->id}/files/{$file->id}/download");

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename=admin-download.pdf');
    }

    public function test_admin_cannot_download_file_from_wrong_client(): void
    {
        $otherClient = Client::factory()->create();
        $file = ClientFile::factory()->create([
            'client_id' => $otherClient->id,
            'original_name' => 'wrong-client-file.pdf',
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/clients/{$this->client->id}/files/{$file->id}/download");

        $response->assertForbidden();
    }

    public function test_admin_can_delete_client_file(): void
    {
        $file = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'admin-delete.pdf',
            'path' => 'client_files/'.$this->client->id.'/admin-delete.pdf',
            'uploaded_by' => 'admin',
        ]);

        // Create the file in storage
        Storage::put($file->path, 'fake admin file content');

        $response = $this->actingAs($this->admin)
            ->delete("/admin/clients/{$this->client->id}/files/{$file->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'File deleted.');

        $this->assertDatabaseMissing('client_files', [
            'id' => $file->id,
        ]);

        // File should be deleted from storage
        Storage::assertMissing($file->path);
    }

    public function test_admin_cannot_delete_file_from_wrong_client(): void
    {
        $otherClient = Client::factory()->create();
        $file = ClientFile::factory()->create([
            'client_id' => $otherClient->id,
            'original_name' => 'wrong-client-delete.pdf',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete("/admin/clients/{$this->client->id}/files/{$file->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('client_files', [
            'id' => $file->id,
        ]);
    }

    public function test_admin_file_deletion_removes_file_from_storage(): void
    {
        $file = ClientFile::factory()->create([
            'client_id' => $this->client->id,
            'original_name' => 'admin-storage-test.pdf',
            'path' => 'client_files/'.$this->client->id.'/admin-storage-test.pdf',
            'uploaded_by' => 'admin',
        ]);

        // Create the file in storage
        Storage::put($file->path, 'fake admin storage content');

        // Verify file exists
        Storage::assertExists($file->path);

        // Delete the file
        $this->actingAs($this->admin)->delete("/admin/clients/{$this->client->id}/files/{$file->id}");

        // File should be removed from storage
        Storage::assertMissing($file->path);
    }

    public function test_client_file_relationship(): void
    {
        $file = ClientFile::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $this->assertInstanceOf(Client::class, $file->client);
        $this->assertEquals($this->client->id, $file->client->id);
    }

    public function test_client_files_count_relationship(): void
    {
        ClientFile::factory()->count(5)->create([
            'client_id' => $this->client->id,
        ]);

        $client = Client::withCount('files')->find($this->client->id);
        $this->assertEquals(5, $client->files_count);
    }
}
