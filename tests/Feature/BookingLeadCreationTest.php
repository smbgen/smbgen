<?php

use App\Models\Availability;
use App\Models\Booking;
use App\Models\LeadForm;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    // Create an admin user with Google Calendar
    $this->admin = User::factory()->create([
        'role' => 'company_administrator',
        'google_refresh_token' => 'test_token',
    ]);

    // Create an availability for testing
    $this->availability = Availability::factory()->create([
        'user_id' => $this->admin->id,
        'day_of_week' => Carbon::tomorrow()->dayOfWeek,
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'duration' => 30,
        'is_active' => true,
    ]);
});

it('creates a lead form when a booking is submitted', function () {
    config(['business.booking.create_lead' => true]);

    $bookingTime = Carbon::tomorrow()->setTime(10, 0);

    $response = $this->post(route('booking.book'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '555-123-4567',
        'slot' => $bookingTime->toIso8601String(),
        'notes' => 'Need inspection for water damage',
        'property_address' => '123 Main St, City, ST 12345',
        'staff_id' => $this->admin->id,
    ]);

    // Verify booking was created
    $this->assertDatabaseHas('bookings', [
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'customer_phone' => '555-123-4567',
    ]);

    // Verify lead form was created
    $this->assertDatabaseHas('lead_forms', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'source_site' => 'booking_system',
    ]);

    // Verify lead has correct data
    $lead = LeadForm::where('email', 'jane@example.com')->first();
    expect($lead)->not->toBeNull()
        ->and($lead->form_data)->toBeArray()
        ->and($lead->form_data['phone'])->toBe('555-123-4567')
        ->and($lead->form_data['property_address'])->toBe('123 Main St, City, ST 12345')
        ->and($lead->form_data['source_type'])->toBe('booking')
        ->and($lead->form_data['booking_id'])->toBeInt();
});

it('does not create a lead form when config is disabled', function () {
    config(['business.booking.create_lead' => false]);

    $bookingTime = Carbon::tomorrow()->setTime(10, 0);

    $this->post(route('booking.book'), [
        'name' => 'John Smith',
        'email' => 'john@example.com',
        'slot' => $bookingTime->toIso8601String(),
        'notes' => 'Test booking',
        'staff_id' => $this->admin->id,
    ]);

    // Verify booking was created
    $this->assertDatabaseHas('bookings', [
        'customer_name' => 'John Smith',
        'customer_email' => 'john@example.com',
    ]);

    // Verify NO lead form was created
    expect(LeadForm::where('email', 'john@example.com')->exists())->toBeFalse();
});

it('includes booking details in lead message when no notes provided', function () {
    config(['business.booking.create_lead' => true]);

    $bookingTime = Carbon::tomorrow()->setTime(14, 30);

    $this->post(route('booking.book'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'slot' => $bookingTime->toIso8601String(),
        'staff_id' => $this->admin->id,
    ]);

    $lead = LeadForm::where('email', 'test@example.com')->first();

    expect($lead)->not->toBeNull()
        ->and($lead->message)->toContain('Booking request for')
        ->and($lead->message)->toContain($bookingTime->format('M j, Y'));
});

it('respects phone field configuration', function () {
    config(['business.booking.show_phone_field' => false]);

    $bookingTime = Carbon::tomorrow()->setTime(10, 0);

    // Should succeed without phone when field is hidden
    $response = $this->post(route('booking.book'), [
        'name' => 'No Phone User',
        'email' => 'nophone@example.com',
        'slot' => $bookingTime->toIso8601String(),
        'notes' => 'Test without phone',
        'staff_id' => $this->admin->id,
    ]);

    $this->assertDatabaseHas('bookings', [
        'customer_email' => 'nophone@example.com',
        'customer_phone' => null,
    ]);
});

it('requires phone when configured', function () {
    $config = \App\Models\BookingFieldConfig::getConfig();
    $config->update(['show_phone' => true, 'require_phone' => true]);

    $bookingTime = Carbon::tomorrow()->setTime(10, 0);

    // Should fail validation without phone when required
    $response = $this->post(route('booking.book'), [
        'name' => 'Missing Phone',
        'email' => 'missing@example.com',
        'slot' => $bookingTime->toIso8601String(),
        'staff_id' => $this->admin->id,
    ]);

    $response->assertStatus(302); // Redirect due to validation error
    expect(Booking::where('customer_email', 'missing@example.com')->exists())->toBeFalse();
});

it('requires property address when configured', function () {
    $config = \App\Models\BookingFieldConfig::getConfig();
    $config->update(['show_property_address' => true, 'require_property_address' => true]);

    $bookingTime = Carbon::tomorrow()->setTime(10, 0);

    // Should fail validation without property address when required
    $response = $this->post(route('booking.book'), [
        'name' => 'Missing Address',
        'email' => 'missing@example.com',
        'slot' => $bookingTime->toIso8601String(),
        'staff_id' => $this->admin->id,
    ]);

    $response->assertStatus(302); // Redirect due to validation error
    expect(Booking::where('customer_email', 'missing@example.com')->exists())->toBeFalse();
});
