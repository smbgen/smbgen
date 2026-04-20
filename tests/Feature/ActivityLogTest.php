<?php

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\ClientFile;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('private');
});

it('logs file upload activity', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    $this->actingAs($admin);

    // Upload a file
    $file = UploadedFile::fake()->create('document.pdf', 100);
    $this->post(route('admin.client.files.upload', $client), [
        'file' => $file,
        'description' => 'Test document',
        'visibility' => 'public',
    ])->assertRedirect();

    // Verify activity log was created
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
        'action' => 'file_upload',
        'model_type' => ClientFile::class,
    ]);

    $log = ActivityLog::where('action', 'file_upload')->first();
    expect($log)->not->toBeNull();
    expect($log->description)->toContain('Uploaded file');
    expect($log->properties)->toHaveKey('client_name');
});

it('logs user login activity', function () {
    $user = User::factory()->create([
        'role' => 'company_administrator',
        'password' => bcrypt('password'),
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Verify login was logged
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $user->id,
        'action' => 'login',
    ]);
});

it('logs user logout activity', function () {
    $user = User::factory()->create(['role' => 'company_administrator']);
    $this->actingAs($user);

    $this->post(route('logout'));

    // Verify logout was logged
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $user->id,
        'action' => 'logout',
    ]);
});

it('logs client creation', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $this->actingAs($admin);

    $this->post(route('clients.store'), [
        'name' => 'Test Client',
        'email' => 'test@example.com',
        'phone' => '555-1234',
    ]);

    $client = Client::where('email', 'test@example.com')->first();

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
        'action' => 'client_create',
        'model_type' => Client::class,
        'model_id' => $client->id,
    ]);
});

it('logs client update with changes', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create(['name' => 'Original Name', 'phone' => '111-1111']);

    $this->actingAs($admin);

    $this->put(route('clients.update', $client), [
        'name' => 'Updated Name',
        'email' => $client->email,
        'phone' => '222-2222',
    ]);

    $log = ActivityLog::where('action', 'client_update')
        ->where('model_id', $client->id)
        ->first();

    expect($log)->not->toBeNull();
    expect($log->properties)->toHaveKey('changes');
    expect($log->properties['changes'])->toHaveKey('name');
});

it('logs client deletion', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    $this->actingAs($admin);

    $clientId = $client->id;
    $this->delete(route('clients.destroy', $client));

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
        'action' => 'client_delete',
        'model_type' => Client::class,
        'model_id' => $clientId,
    ]);
});

it('logs file download activity', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    Storage::disk('private')->put('test.pdf', 'content');
    $file = ClientFile::create([
        'client_id' => $client->id,
        'user_id' => $admin->id,
        'filename' => 'test.pdf',
        'original_name' => 'test.pdf',
        'path' => 'test.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'file_extension' => 'pdf',
        'is_public' => false,
    ]);

    $this->actingAs($admin);

    $this->get(route('admin.client.files.download', ['client' => $client, 'file' => $file]));

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
        'action' => 'file_download',
        'model_type' => ClientFile::class,
        'model_id' => $file->id,
    ]);
});

it('logs file deletion activity', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = Client::factory()->create();

    Storage::disk('private')->put('test.pdf', 'content');
    $file = ClientFile::create([
        'client_id' => $client->id,
        'user_id' => $admin->id,
        'filename' => 'test.pdf',
        'original_name' => 'test.pdf',
        'path' => 'test.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'file_extension' => 'pdf',
        'is_public' => false,
    ]);

    $this->actingAs($admin);

    $fileId = $file->id;
    $this->delete(route('admin.client.files.destroy', ['client' => $client, 'file' => $file]));

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
        'action' => 'file_delete',
        'model_type' => ClientFile::class,
        'model_id' => $fileId,
    ]);
});

it('captures IP address and user agent', function () {
    $user = User::factory()->create([
        'role' => 'company_administrator',
        'password' => bcrypt('password'),
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $log = ActivityLog::where('user_id', $user->id)->where('action', 'login')->first();

    expect($log->ip_address)->not->toBeNull();
    expect($log->user_agent)->not->toBeNull();
});

it('can filter logs by action', function () {
    $user = User::factory()->create(['role' => 'company_administrator']);

    $this->actingAs($user);
    ActivityLogger::log('test_action_1', 'Test 1', null, null);
    ActivityLogger::log('test_action_2', 'Test 2', null, null);

    $logs = ActivityLog::ofAction('test_action_1')->get();

    expect($logs)->toHaveCount(1);
    expect($logs->first()->action)->toBe('test_action_1');
});

it('can filter logs by user', function () {
    $user1 = User::factory()->create(['role' => 'company_administrator']);
    $user2 = User::factory()->create(['role' => 'client']);

    $this->actingAs($user1);
    ActivityLogger::log('test_action', 'Test from user 1', null, null);

    $this->actingAs($user2);
    ActivityLogger::log('test_action', 'Test from user 2', null, null);

    $logs = ActivityLog::byUser($user1->id)->get();

    expect($logs)->toHaveCount(1);
    expect($logs->first()->user_id)->toBe($user1->id);
});

it('can filter logs by date range', function () {
    $user = User::factory()->create(['role' => 'company_administrator']);

    $this->actingAs($user);
    ActivityLogger::log('test_action', 'Test', null, null);

    // Manually update the created_at to simulate an old log
    $oldLog = ActivityLog::first();
    ActivityLog::where('id', $oldLog->id)->update(['created_at' => now()->subDays(10)]);

    ActivityLogger::log('test_action', 'Test recent', null, null);

    $logs = ActivityLog::dateRange(now()->subDays(5), now())->get();

    expect($logs)->toHaveCount(1);
    expect($logs->first()->description)->toBe('Test recent');
});

it('has formatted action attribute', function () {
    $user = User::factory()->create(['role' => 'company_administrator']);

    $this->actingAs($user);
    ActivityLogger::log('file_upload', 'Test', null, null);

    $log = ActivityLog::first();

    expect($log->formatted_action)->toBe('File Upload');
});

it('has action icon attribute', function () {
    $user = User::factory()->create(['role' => 'company_administrator']);

    $this->actingAs($user);
    ActivityLogger::log('file_upload', 'Test', null, null);

    $log = ActivityLog::first();

    expect($log->action_icon)->toBe('📤');
});

it('has action color attribute', function () {
    $user = User::factory()->create(['role' => 'company_administrator']);

    $this->actingAs($user);
    ActivityLogger::log('file_upload', 'Test', null, null);

    $log = ActivityLog::first();

    expect($log->action_color)->toContain('bg-blue');
});
