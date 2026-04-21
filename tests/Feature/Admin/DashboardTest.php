<?php

use App\Models\Client;
use App\Models\LeadForm;
use App\Models\Tenant;
use App\Models\User;

beforeEach(function () {
    $this->withoutVite();
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can access dashboard', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewIs('admin.dashboard');
});

test('non-admin cannot access dashboard', function () {
    $this->withoutExceptionHandling();
    $regularUser = User::factory()->create(['role' => 'client']);

    $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    $this->expectExceptionMessage('Unauthorized. Administrator access required.');

    $this->actingAs($regularUser)
        ->get(route('admin.dashboard'));
});

test('guest cannot access dashboard', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('login'));
});

test('dashboard displays correct stat cards', function () {
    // Create test data
    Client::factory()->count(3)->create();
    LeadForm::factory()->count(2)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Recent Activity');
    $response->assertSee('Latest Leads');
    $response->assertSee('Latest Bookings');
    $response->assertSee('Latest Payments');
});

test('dashboard shows bookings stat when appointments feature is enabled', function () {
    config(['business.features.booking' => true]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Bookings');
})->skip('Booking factory needs to be created');

test('dashboard shows cms pages stat when cms feature is enabled', function () {
    config(['business.features.cms' => true]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Core Modules');
    $response->assertSee('CMS');
});

test('dashboard displays top action links', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Leads');
    $response->assertSee('Welcome, '.$this->admin->name);
});

test('dashboard displays recent leads', function () {
    LeadForm::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Latest Leads');
});

test('dashboard displays crm and cms cards', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('CRM');
    $response->assertSee('CMS');
});

test('dashboard displays crm action links', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Clients');
    $response->assertSee('Messages');
});

test('dashboard shows bookings section when appointments enabled', function () {
    config(['business.features.booking' => true]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Latest Bookings');
});

test('dashboard still renders bookings card when appointments disabled', function () {
    config(['business.features.booking' => false]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Latest Bookings');
});

test('dashboard shows tenant domain onboarding status card when tenancy is enabled', function () {
    config()->set('app.tenancy_enabled', true);

    $tenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Acme Dashboard',
        'email' => 'owner@acme-dashboard.test',
        'subdomain' => 'acme-dashboard',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
        'custom_domain' => 'app.acme-dashboard.example',
    ]);
    $tenant->setAttribute('custom_domain_status', 'pending_dns');
    $tenant->save();

    $this->admin->update([
        'tenant_id' => $tenant->id,
    ]);

    app()->instance('currentTenant', $tenant);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Domain Setup Status');
    $response->assertSee('Pending DNS');
    $response->assertSee('Manage Domain Setup');
});
