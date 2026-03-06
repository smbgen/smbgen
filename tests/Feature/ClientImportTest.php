<?php

use App\Models\Client;
use App\Models\ClientImport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
    $this->actingAs($this->admin);
});

test('admin can access import page', function () {
    $response = $this->get(route('clients.import.index'));

    $response->assertSuccessful();
    $response->assertSee('Import Clients');
    $response->assertSee('Upload CSV File');
});

test('admin can upload valid csv file', function () {
    $csvContent = "name,email,phone,property_address,notes,source_site\n";
    $csvContent .= "John Doe,john@example.com,555-1234,123 Main St,Test note,Website\n";
    $csvContent .= 'Jane Smith,jane@example.com,555-5678,456 Oak Ave,Another note,Referral';

    $file = UploadedFile::fake()->createWithContent('clients.csv', $csvContent);

    $response = $this->post(route('clients.import.upload'), [
        'csv_file' => $file,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(ClientImport::count())->toBe(1);

    $import = ClientImport::first();
    expect($import->user_id)->toBe($this->admin->id);
    expect($import->status)->toBe('pending');
    expect($import->total_rows)->toBe(2);
});

test('csv upload requires a file', function () {
    $response = $this->post(route('clients.import.upload'), []);

    $response->assertSessionHasErrors('csv_file');
});

test('csv must be valid format', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->post(route('clients.import.upload'), [
        'csv_file' => $file,
    ]);

    $response->assertSessionHasErrors('csv_file');
});

test('admin can preview csv data', function () {
    $csvContent = "name,email,phone,property_address,notes,source_site\n";
    $csvContent .= 'John Doe,john@example.com,555-1234,123 Main St,Test note,Website';

    $file = UploadedFile::fake()->createWithContent('clients.csv', $csvContent);

    $uploadResponse = $this->post(route('clients.import.upload'), [
        'csv_file' => $file,
    ]);

    $import = ClientImport::first();

    $response = $this->get(route('clients.import.preview', $import));

    $response->assertSuccessful();
    $response->assertSee('John Doe');
    $response->assertSee('john@example.com');
    $response->assertSee('Preview Import');
});

test('admin can process valid import', function () {
    $csvContent = "name,email,phone,property_address,notes,source_site\n";
    $csvContent .= "John Doe,john@example.com,555-1234,123 Main St,Test note,Website\n";
    $csvContent .= 'Jane Smith,jane@example.com,555-5678,456 Oak Ave,Another note,Referral';

    $file = UploadedFile::fake()->createWithContent('clients.csv', $csvContent);

    $this->post(route('clients.import.upload'), ['csv_file' => $file]);

    $import = ClientImport::first();

    $response = $this->post(route('clients.import.process', $import));

    $response->assertRedirect(route('clients.import.index'));
    $response->assertSessionHas('success');

    expect(Client::count())->toBe(2);
    expect(Client::where('email', 'john@example.com')->exists())->toBeTrue();
    expect(Client::where('email', 'jane@example.com')->exists())->toBeTrue();

    $import->refresh();
    expect($import->status)->toBe('completed');
    expect($import->successful_imports)->toBe(2);
    expect($import->failed_imports)->toBe(0);
});

test('import skips invalid rows', function () {
    $csvContent = "name,email,phone,property_address,notes,source_site\n";
    $csvContent .= "John Doe,john@example.com,555-1234,123 Main St,Test note,Website\n";
    $csvContent .= ",invalid@email,555-5678,456 Oak Ave,Missing name,Referral\n";
    $csvContent .= 'Jane Smith,jane@example.com,555-9999,789 Pine St,Valid,Website';

    $file = UploadedFile::fake()->createWithContent('clients.csv', $csvContent);

    $this->post(route('clients.import.upload'), ['csv_file' => $file]);

    $import = ClientImport::first();

    $this->post(route('clients.import.process', $import));

    expect(Client::count())->toBe(2);
    expect(Client::where('email', 'john@example.com')->exists())->toBeTrue();
    expect(Client::where('email', 'jane@example.com')->exists())->toBeTrue();
    expect(Client::where('email', 'invalid@email')->exists())->toBeFalse();

    $import->refresh();
    expect($import->successful_imports)->toBe(2);
    expect($import->failed_imports)->toBe(1);
});

test('import preview shows validation errors', function () {
    $csvContent = "name,email,phone,property_address,notes,source_site\n";
    $csvContent .= ',invalid@email,555-5678,456 Oak Ave,Missing name,Referral';

    $file = UploadedFile::fake()->createWithContent('clients.csv', $csvContent);

    $this->post(route('clients.import.upload'), ['csv_file' => $file]);

    $import = ClientImport::first();

    $response = $this->get(route('clients.import.preview', $import));

    $response->assertSuccessful();
    $response->assertSee('Row(s) with Errors');
});

test('user cannot access another users import', function () {
    $otherUser = User::factory()->create(['role' => 'company_administrator']);

    $import = ClientImport::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->get(route('clients.import.preview', $import));

    $response->assertForbidden();
});

test('recent imports display on index page', function () {
    ClientImport::factory()->count(3)->create([
        'user_id' => $this->admin->id,
        'status' => 'completed',
    ]);

    $response = $this->get(route('clients.import.index'));

    $response->assertSuccessful();
    $response->assertSee('Recent Imports');
});
