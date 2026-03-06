<?php

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\BookingFieldConfig;
use App\Models\LeadForm;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    // Configure booking fields with custom fields
    $config = BookingFieldConfig::getConfig();
    $config->update([
        'show_phone' => true,
        'require_phone' => false,
        'show_property_address' => true,
        'require_property_address' => false,
        'show_notes' => true,
        'require_notes' => false,
        'custom_fields' => [
            [
                'name' => 'preferred_contact_method',
                'label' => 'Preferred Contact Method',
                'type' => 'text',
                'required' => false,
            ],
            [
                'name' => 'budget_range',
                'label' => 'Budget Range',
                'type' => 'text',
                'required' => false,
            ],
        ],
    ]);

    // Create a staff member with Google credentials
    $this->admin = User::factory()->create([
        'role' => User::ROLE_ADMINISTRATOR,
        'email' => 'admin@test.com',
    ]);

    $this->admin->googleCredential()->create([
        'refresh_token' => 'test-token',
        'calendar_id' => 'primary',
        'external_account_email' => 'admin@test.com',
    ]);

    $this->admin->availabilities()->create([
        'day_of_week' => now()->addDay()->dayOfWeek,
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'duration' => 60,
        'timezone' => 'America/New_York',
        'is_active' => true,
    ]);
});

test('booking stores custom fields in custom_form_data', function () {
    Mail::fake();

    $slot = now()->addDays(2)->setTime(10, 0)->toIso8601String();

    $response = $this->post(route('booking.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '555-1234',
        'property_address' => '123 Main St',
        'notes' => 'Looking forward to meeting',
        'preferred_contact_method' => 'Email',
        'budget_range' => '$50k-$100k',
        'slot' => $slot,
        'staff_id' => $this->admin->id,
    ]);

    $response->assertRedirect(route('booking.confirmation'));

    $booking = Booking::latest()->first();

    expect($booking->customer_name)->toBe('John Doe');
    expect($booking->customer_email)->toBe('john@example.com');
    expect($booking->customer_phone)->toBe('555-1234');
    expect($booking->property_address)->toBe('123 Main St');
    expect($booking->notes)->toBe('Looking forward to meeting');
    expect($booking->custom_form_data)->toBeArray();
    expect($booking->custom_form_data)->toHaveKey('preferred_contact_method');
    expect($booking->custom_form_data['preferred_contact_method'])->toBe('Email');
    expect($booking->custom_form_data)->toHaveKey('budget_range');
    expect($booking->custom_form_data['budget_range'])->toBe('$50k-$100k');
});

test('booking creates lead with custom fields in form_data', function () {
    Mail::fake();

    $slot = now()->addDays(2)->setTime(10, 0)->toIso8601String();

    $this->post(route('booking.store'), [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '555-5678',
        'property_address' => '456 Oak Ave',
        'notes' => 'Urgent request',
        'preferred_contact_method' => 'Phone',
        'budget_range' => '$100k-$200k',
        'slot' => $slot,
        'staff_id' => $this->admin->id,
    ]);

    $lead = LeadForm::latest()->first();

    expect($lead->name)->toBe('Jane Smith');
    expect($lead->email)->toBe('jane@example.com');
    expect($lead->form_data)->toBeArray();
    expect($lead->form_data)->toHaveKey('phone', '555-5678');
    expect($lead->form_data)->toHaveKey('property_address', '456 Oak Ave');
    expect($lead->form_data)->toHaveKey('preferred_contact_method', 'Phone');
    expect($lead->form_data)->toHaveKey('budget_range', '$100k-$200k');
    expect($lead->form_data)->toHaveKey('booking_id');
    expect($lead->form_data)->toHaveKey('source_type', 'booking');
});

test('booking confirmation email includes custom fields', function () {
    Mail::fake();

    $slot = now()->addDays(2)->setTime(10, 0)->toIso8601String();

    $this->post(route('booking.store'), [
        'name' => 'Bob Johnson',
        'email' => 'bob@example.com',
        'phone' => '555-9999',
        'property_address' => '789 Pine St',
        'notes' => 'First time customer',
        'preferred_contact_method' => 'Text',
        'budget_range' => '$200k+',
        'slot' => $slot,
        'staff_id' => $this->admin->id,
    ]);

    $booking = Booking::latest()->first();

    Mail::assertSent(BookingConfirmation::class, function ($mail) {
        $rendered = $mail->render();

        // Check that custom fields are in the email
        return str_contains($rendered, 'Preferred Contact Method')
            && str_contains($rendered, 'Text')
            && str_contains($rendered, 'Budget Range')
            && str_contains($rendered, '$200k+')
            && str_contains($rendered, '789 Pine St')
            && str_contains($rendered, 'First time customer');
    });
});

test('booking handles empty custom fields gracefully', function () {
    Mail::fake();

    $slot = now()->addDays(2)->setTime(10, 0)->toIso8601String();

    $response = $this->post(route('booking.store'), [
        'name' => 'Alice Brown',
        'email' => 'alice@example.com',
        'slot' => $slot,
        'staff_id' => $this->admin->id,
    ]);

    $response->assertRedirect(route('booking.confirmation'));

    $booking = Booking::latest()->first();

    expect($booking->customer_name)->toBe('Alice Brown');
    expect($booking->custom_form_data)->toBeNull();
});

test('booking only stores non-empty custom fields', function () {
    Mail::fake();

    $slot = now()->addDays(2)->setTime(10, 0)->toIso8601String();

    $this->post(route('booking.store'), [
        'name' => 'Charlie Davis',
        'email' => 'charlie@example.com',
        'preferred_contact_method' => 'Email',
        'budget_range' => '', // Empty value
        'slot' => $slot,
        'staff_id' => $this->admin->id,
    ]);

    $booking = Booking::latest()->first();

    expect($booking->custom_form_data)->toBeArray();
    expect($booking->custom_form_data)->toHaveKey('preferred_contact_method');
    expect($booking->custom_form_data)->not->toHaveKey('budget_range');
});

test('booking email displays toggleable fields when present', function () {
    Mail::fake();

    $slot = now()->addDays(2)->setTime(10, 0)->toIso8601String();

    $this->post(route('booking.store'), [
        'name' => 'Diana Evans',
        'email' => 'diana@example.com',
        'phone' => '555-1111',
        'property_address' => '321 Elm St',
        'notes' => 'Looking for consultation',
        'slot' => $slot,
        'staff_id' => $this->admin->id,
    ]);

    Mail::assertSent(BookingConfirmation::class, function ($mail) {
        $rendered = $mail->render();

        // Check that toggleable fields appear in email
        return str_contains($rendered, 'Your Phone')
            && str_contains($rendered, '555-1111')
            && str_contains($rendered, 'Property Address')
            && str_contains($rendered, '321 Elm St')
            && str_contains($rendered, 'Notes')
            && str_contains($rendered, 'Looking for consultation');
    });
});
