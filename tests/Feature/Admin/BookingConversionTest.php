<?php

use App\Models\Booking;
use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'company_administrator',
        'email' => 'admin@test.com',
    ]);
});

it('allows admin to convert booking to client', function () {
    $booking = Booking::factory()->create([
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '555-1234',
        'property_address' => '123 Main St',
        'notes' => 'Test booking notes',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.convert-to-client', $booking));

    $response->assertRedirect();

    // Verify client was created
    $client = Client::where('email', 'john@example.com')->first();
    expect($client)->not->toBeNull();
    expect($client->name)->toBe('John Doe');
    expect($client->phone)->toBe('555-1234');
    expect($client->property_address)->toBe('123 Main St');
    expect($client->notes)->toBe('Test booking notes');
    expect($client->source_site)->toBe('Booking Conversion');
});

it('redirects to existing client if email already exists', function () {
    // Create existing client
    $existingClient = Client::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $booking = Booking::factory()->create([
        'customer_email' => 'existing@example.com',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.convert-to-client', $booking));

    $response->assertRedirect(route('clients.show', $existingClient));

    // Verify no duplicate client was created
    expect(Client::where('email', 'existing@example.com')->count())->toBe(1);
});

it('requires authentication to convert booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->post(route('admin.bookings.convert-to-client', $booking));

    $response->assertRedirect(route('login'));
});

it('stores property address when creating booking', function () {
    $bookingData = [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'slot' => now()->addDay()->setTime(10, 0)->toIso8601String(),
        'property_address' => '456 Oak Avenue',
        'notes' => 'Test notes',
    ];

    $response = $this->post(route('booking.book'), $bookingData);

    $booking = Booking::where('customer_email', 'jane@example.com')->first();
    expect($booking)->not->toBeNull();
    expect($booking->property_address)->toBe('456 Oak Avenue');
});

it('displays property address in bookings dashboard', function () {
    Booking::factory()->create([
        'customer_name' => 'Test Customer',
        'property_address' => '789 Pine Street',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('789 Pine Street');
});

it('shows convert to client button only when client does not exist', function () {
    // Booking without existing client
    $booking1 = Booking::factory()->create([
        'customer_email' => 'new@example.com',
    ]);

    // Booking with existing client
    Client::factory()->create(['email' => 'existing@example.com']);
    $booking2 = Booking::factory()->create([
        'customer_email' => 'existing@example.com',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.dashboard'));

    $response->assertSuccessful();
    // Should show convert button for new customer
    $response->assertSee('Convert to Client');
    // Should show checkmark for existing client
    $response->assertSee('Already a client');
});
