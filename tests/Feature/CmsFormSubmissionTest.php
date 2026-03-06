<?php

use App\Models\CmsPage;
use App\Models\LeadForm;

beforeEach(function () {
    // Enable CMS feature flag
    config(['business.features.cms' => true]);
});

test('public user can submit form on cms page with lead form', function () {
    $page = CmsPage::factory()->withLeadForm()->create([
        'slug' => 'contact-us',
    ]);

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '(555) 123-4567',
        'property_address' => '123 Main Street, City, State',
        'message' => 'I need help with my project.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify lead was created
    $this->assertDatabaseHas('lead_forms', [
        'cms_page_id' => $page->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'I need help with my project.',
    ]);

    // Verify custom fields stored in form_data JSON
    $lead = LeadForm::where('email', 'john@example.com')->first();
    expect($lead->form_data)->toBeArray();
    expect($lead->form_data['phone'])->toBe('(555) 123-4567');
    expect($lead->form_data['property_address'])->toBe('123 Main Street, City, State');
});

test('form submission validates required fields', function () {
    $page = CmsPage::factory()->withLeadForm()->create();

    $response = $this->post(route('cms.form.submit', $page->slug), [
        // Missing required fields: name, email, message
        'phone' => '(555) 123-4567',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'message']);
});

test('form submission validates email format', function () {
    $page = CmsPage::factory()->withLeadForm()->create();

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'message' => 'Test message',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('form submission allows optional fields to be empty', function () {
    $page = CmsPage::factory()->withLeadForm()->create();

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Test message',
        // phone and property_address are optional
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $lead = LeadForm::where('email', 'john@example.com')->first();
    expect($lead)->not->toBeNull();
});

test('form submission redirects to custom url when configured', function () {
    $page = CmsPage::factory()
        ->withLeadForm()
        ->withRedirect('/thank-you')
        ->create();

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Test message',
    ]);

    $response->assertRedirect('/thank-you');
});

test('form submission shows success message when no redirect configured', function () {
    $page = CmsPage::factory()->withLeadForm()->create([
        'form_success_message' => 'Thank you for contacting us!',
    ]);

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Test message',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Thank you for contacting us!');
});

test('form submission fails for unpublished page', function () {
    $page = CmsPage::factory()
        ->withLeadForm()
        ->unpublished()
        ->create();

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Test message',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Form not found.');
});

test('form submission fails for page without form enabled', function () {
    $page = CmsPage::factory()->create([
        'has_form' => false,
    ]);

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Test message',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Form not found.');
});

test('form submission captures ip address and user agent', function () {
    $page = CmsPage::factory()->withLeadForm()->create();

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Test message',
    ], [
        'User-Agent' => 'Mozilla/5.0 Test Browser',
    ]);

    $response->assertRedirect();

    $lead = LeadForm::where('email', 'john@example.com')->first();
    expect($lead->ip_address)->not->toBeNull();
    expect($lead->user_agent)->toBe('Mozilla/5.0 Test Browser');
});

test('form submission maps alternative field names correctly', function () {
    // Create page with 'full_name' instead of 'name'
    $page = CmsPage::factory()->create([
        'has_form' => true,
        'is_published' => true,
        'form_fields' => [
            [
                'type' => 'text',
                'name' => 'full_name',
                'label' => 'Full Name',
                'required' => true,
            ],
            [
                'type' => 'email',
                'name' => 'email',
                'label' => 'Email',
                'required' => true,
            ],
            [
                'type' => 'textarea',
                'name' => 'comments',
                'label' => 'Comments',
                'required' => true,
            ],
        ],
    ]);

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'full_name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'comments' => 'This is a comment',
    ]);

    $response->assertRedirect();

    // Verify mapping: full_name → name, comments → message
    $this->assertDatabaseHas('lead_forms', [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'message' => 'This is a comment',
    ]);
});

test('form submission stores all custom fields in form_data json', function () {
    $page = CmsPage::factory()->create([
        'has_form' => true,
        'is_published' => true,
        'form_fields' => [
            ['type' => 'text', 'name' => 'name', 'required' => true],
            ['type' => 'email', 'name' => 'email', 'required' => true],
            ['type' => 'textarea', 'name' => 'message', 'required' => true],
            ['type' => 'text', 'name' => 'company', 'required' => false],
            ['type' => 'text', 'name' => 'job_title', 'required' => false],
            ['type' => 'select', 'name' => 'service_type', 'required' => false],
        ],
    ]);

    $response = $this->post(route('cms.form.submit', $page->slug), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Hello',
        'company' => 'Acme Corp',
        'job_title' => 'Manager',
        'service_type' => 'Premium',
    ]);

    $response->assertRedirect();

    $lead = LeadForm::where('email', 'john@example.com')->first();
    expect($lead->form_data)->toHaveKeys(['company', 'job_title', 'service_type']);
    expect($lead->form_data['company'])->toBe('Acme Corp');
    expect($lead->form_data['job_title'])->toBe('Manager');
    expect($lead->form_data['service_type'])->toBe('Premium');
});

test('admin can create cms page with default lead form fields', function () {
    $user = \App\Models\User::factory()->create([
        'role' => 'company_administrator',
    ]);

    $response = $this->actingAs($user)->get(route('admin.cms.create'));

    $response->assertOk();
    $response->assertSee('Form Builder');
});
