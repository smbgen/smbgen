<?php

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
    config(['business.features.booking' => true]);
    $this->markTestSkipped('Booking factory and full booking tests need to be implemented');
});

test('admin can access booking dashboard', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.dashboard'));

    $response->assertOk();
    $response->assertViewIs('admin.bookings.dashboard');
});

test('booking dashboard displays booking stats', function () {
    // Create test bookings
    Booking::factory()->count(2)->create([
        'status' => 'pending',
        'booking_date' => now()->addDays(1),
    ]);

    Booking::factory()->count(3)->create([
        'status' => 'confirmed',
        'booking_date' => now()->addDays(2),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.dashboard'));

    $response->assertOk();
    $response->assertSee('Booking System');
    $response->assertSee('Pending');
    $response->assertSee('Upcoming');
});

test('admin can delete booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.bookings.destroy', $booking));

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('bookings', [
        'id' => $booking->id,
    ]);
});

test('admin can send booking reminder', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'customer_email' => 'customer@example.com',
        'customer_name' => 'John Doe',
        'booking_date' => now()->addDays(1),
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.send-reminder', $booking));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    Mail::assertSent(function ($mail) use ($booking) {
        return $mail->hasTo($booking->customer_email);
    });
});

test('non-admin cannot access booking dashboard', function () {
    $regularUser = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($regularUser)
        ->get(route('admin.bookings.dashboard'));

    $response->assertForbidden();
});

test('non-admin cannot delete bookings', function () {
    $regularUser = User::factory()->create(['role' => 'client']);
    $booking = Booking::factory()->create();

    $response = $this->actingAs($regularUser)
        ->delete(route('admin.bookings.destroy', $booking));

    $response->assertForbidden();

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
    ]);
});

test('booking dashboard shows empty state when no bookings', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.dashboard'));

    $response->assertOk();
    $response->assertSee('No bookings yet');
});

test('booking dashboard shows all bookings in table', function () {
    $bookings = Booking::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.dashboard'));

    $response->assertOk();

    foreach ($bookings as $booking) {
        $response->assertSee($booking->customer_name);
        $response->assertSee($booking->customer_email);
    }
});
