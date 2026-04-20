<?php

use App\Models\BookingFieldConfig;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view booking field configuration page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.booking-fields.edit'));

    $response->assertSuccessful();
    $response->assertSee('Booking Form Configuration');
});

test('booking field config has correct default values', function () {
    $config = BookingFieldConfig::getConfig();

    expect($config->show_phone)->toBeTrue();
    expect($config->require_phone)->toBeFalse();
    expect($config->show_property_address)->toBeTrue();
    expect($config->require_property_address)->toBeFalse();
    expect($config->show_notes)->toBeTrue();
    expect($config->require_notes)->toBeFalse();
    expect($config->custom_fields)->toBeEmpty();
});

test('admin can toggle phone field visibility', function () {
    $config = BookingFieldConfig::getConfig();

    $this->actingAs($this->admin)->put(route('admin.booking-fields.update'), [
        'show_phone' => false,
        'require_phone' => false,
        'show_property_address' => true,
        'require_property_address' => false,
        'show_notes' => true,
        'require_notes' => false,
        'custom_fields' => json_encode([]),
    ])->assertRedirect();

    expect($config->fresh()->show_phone)->toBeFalse();
});

test('admin can make property address required', function () {
    $config = BookingFieldConfig::getConfig();

    $this->actingAs($this->admin)->put(route('admin.booking-fields.update'), [
        'show_phone' => true,
        'require_phone' => false,
        'show_property_address' => true,
        'require_property_address' => true,
        'show_notes' => true,
        'require_notes' => false,
        'custom_fields' => json_encode([]),
    ])->assertRedirect();

    expect($config->fresh()->require_property_address)->toBeTrue();
});

test('admin can add custom fields', function () {
    $config = BookingFieldConfig::getConfig();

    $customFields = [
        [
            'type' => 'text',
            'name' => 'budget',
            'label' => 'Budget Range',
            'placeholder' => 'e.g., $100k-$200k',
            'required' => false,
        ],
        [
            'type' => 'select',
            'name' => 'property_type',
            'label' => 'Property Type',
            'options' => 'Residential,Commercial,Land',
            'required' => true,
        ],
    ];

    $this->actingAs($this->admin)->put(route('admin.booking-fields.update'), [
        'show_phone' => true,
        'require_phone' => false,
        'show_property_address' => true,
        'require_property_address' => false,
        'show_notes' => true,
        'require_notes' => false,
        'custom_fields' => json_encode($customFields),
    ])->assertRedirect();

    expect($config->fresh()->custom_fields)->toHaveCount(2);
    expect($config->fresh()->custom_fields[0]['name'])->toBe('budget');
    expect($config->fresh()->custom_fields[1]['name'])->toBe('property_type');
});

test('getAllFields returns correct field structure', function () {
    $config = BookingFieldConfig::getConfig();
    $config->update([
        'custom_fields' => [
            [
                'type' => 'text',
                'name' => 'referral_source',
                'label' => 'How did you hear about us?',
                'required' => false,
            ],
        ],
    ]);

    $fields = $config->getAllFields();

    // Should have: name, email, phone, property_address, notes, + 1 custom
    expect($fields)->toHaveCount(6);

    // Check structure
    $nameField = collect($fields)->firstWhere('name', 'name');
    expect($nameField['required'])->toBeTrue();
    expect($nameField['built_in'])->toBeTrue();

    $customField = collect($fields)->firstWhere('name', 'referral_source');
    expect($customField['built_in'])->toBeFalse();
});

test('hidden fields are not returned in getAllFields', function () {
    $config = BookingFieldConfig::getConfig();
    $config->update([
        'show_phone' => false,
        'show_notes' => false,
    ]);

    $fields = $config->getAllFields();

    // Should have: name, email, property_address (phone and notes hidden)
    expect($fields)->toHaveCount(3);
    expect(collect($fields)->pluck('name')->toArray())->toBe(['name', 'email', 'property_address']);
});
