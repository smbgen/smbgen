<?php

use App\Models\LeadForm;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view leads index', function () {
    LeadForm::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.leads.index'));

    $response->assertOk();
    $response->assertViewIs('admin.leads.index');
    $response->assertViewHas('leads');
});

test('admin can view single lead', function () {
    $lead = LeadForm::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.leads.show', $lead));

    $response->assertOk();
    $response->assertSee('John Doe');
    $response->assertSee('john@example.com');
});

test('admin can convert lead to client', function () {
    $lead = LeadForm::factory()->create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'form_data' => ['phone' => '555-1234'],
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.leads.convert', $lead));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('clients', [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '555-1234',
    ]);
});

test('admin can delete lead', function () {
    $lead = LeadForm::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.leads.destroy', $lead));

    $response->assertRedirect();

    $this->assertDatabaseMissing('lead_forms', [
        'id' => $lead->id,
    ]);
});

test('admin can export leads to csv', function () {
    LeadForm::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.leads.export.csv'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('non-admin cannot access leads', function () {
    $regularUser = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($regularUser)
        ->get(route('admin.leads.index'));

    $response->assertForbidden();
});

test('non-admin cannot convert leads', function () {
    $regularUser = User::factory()->create(['role' => 'client']);
    $lead = LeadForm::factory()->create();

    $response = $this->actingAs($regularUser)
        ->post(route('admin.leads.convert', $lead));

    $response->assertForbidden();
});

test('non-admin cannot delete leads', function () {
    $regularUser = User::factory()->create(['role' => 'client']);
    $lead = LeadForm::factory()->create();

    $response = $this->actingAs($regularUser)
        ->delete(route('admin.leads.destroy', $lead));

    $response->assertForbidden();

    $this->assertDatabaseHas('lead_forms', [
        'id' => $lead->id,
    ]);
});

test('leads index shows recent leads first', function () {
    $oldLead = LeadForm::factory()->create([
        'created_at' => now()->subDays(5),
        'name' => 'Old Lead',
    ]);

    $newLead = LeadForm::factory()->create([
        'created_at' => now(),
        'name' => 'New Lead',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.leads.index'));

    $response->assertOk();

    // Check that new lead appears before old lead in the response
    $content = $response->getContent();
    $newPos = strpos($content, 'New Lead');
    $oldPos = strpos($content, 'Old Lead');

    expect($newPos)->toBeLessThan($oldPos);
});
