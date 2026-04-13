<?php

use App\Mail\BookingCancellation;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create staff user
    $this->staff = User::factory()->create([
        'name' => 'Staff Member',
        'email' => 'staff@test.com',
        'role' => 'company_administrator',
    ]);

    // Create admin user
    $this->admin = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@test.com',
        'role' => 'company_administrator',
    ]);

    // Create test booking
    $this->booking = Booking::factory()->create([
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '555-1234',
        'booking_date' => now()->addDays(7),
        'booking_time' => '14:00:00',
        'duration' => 60,
        'property_address' => '123 Main St',
        'notes' => 'Test booking',
        'status' => 'confirmed',
        'staff_id' => $this->staff->id,
    ]);
});

test('cancellation emails are sent when booking is deleted', function () {
    Mail::fake();

    // Act as admin and delete booking
    $this->actingAs($this->admin)
        ->delete(route('admin.bookings.destroy', $this->booking))
        ->assertRedirect(route('admin.bookings.dashboard'))
        ->assertSessionHas('success');

    // Assert emails were sent to customer
    Mail::assertSent(BookingCancellation::class, function ($mail) {
        return $mail->hasTo('john@example.com') && $mail->recipientType === 'customer';
    });

    // Assert emails were sent to staff
    Mail::assertSent(BookingCancellation::class, function ($mail) {
        return $mail->hasTo('staff@test.com') && $mail->recipientType === 'staff';
    });

    // Assert booking was deleted
    $this->assertDatabaseMissing('bookings', [
        'id' => $this->booking->id,
    ]);
});

test('cancellation email to customer contains correct details', function () {
    $mail = new BookingCancellation($this->booking, 'customer');

    $mail->assertSeeInHtml('John Doe'); // Customer name is shown in greeting
    // Customer email and phone are not shown in customer emails (only in staff emails)
    $mail->assertSeeInHtml($this->booking->booking_date->format('l, F j, Y'));
    $mail->assertSeeInHtml('60 minutes');
    $mail->assertSeeInHtml('123 Main St');
    $mail->assertSeeInHtml('Test booking');
    $mail->assertSeeInHtml('Appointment Cancelled');
    $mail->assertSeeInHtml('Need to Reschedule?');
});

test('cancellation email to staff contains correct details', function () {
    $mail = new BookingCancellation($this->booking, 'staff');

    $mail->assertSeeInHtml('John Doe');
    $mail->assertSeeInHtml('john@example.com');
    $mail->assertSeeInHtml('555-1234');
    $mail->assertSeeInHtml($this->booking->booking_date->format('l, F j, Y'));
    $mail->assertSeeInHtml('60 minutes');
    $mail->assertSeeInHtml('123 Main St');
    $mail->assertSeeInHtml('Test booking');
    $mail->assertSeeInHtml('Appointment Cancelled');
});

test('cancellation email has correct subject line', function () {
    $mail = new BookingCancellation($this->booking, 'customer');
    $envelope = $mail->envelope();

    $date = $this->booking->booking_date->format('M j, Y');
    $time = \Carbon\Carbon::parse($this->booking->booking_time)->format('g:i A');

    expect($envelope->subject)->toBe("Appointment Cancelled - {$date} at {$time}");
});

test('cancellation email includes reply-to address from config', function () {
    config(['business.contact.email' => 'contact@business.com']);

    $mail = new BookingCancellation($this->booking, 'customer');
    $envelope = $mail->envelope();

    expect($envelope->replyTo)->toHaveCount(1);
    expect($envelope->replyTo[0]->address)->toBe('contact@business.com');
});

test('booking deletion continues even if email fails', function () {
    Mail::fake();
    Mail::shouldReceive('to')->andThrow(new \Exception('Email service down'));

    // Act as admin and delete booking
    $this->actingAs($this->admin)
        ->delete(route('admin.bookings.destroy', $this->booking))
        ->assertRedirect(route('admin.bookings.dashboard'))
        ->assertSessionHas('success');

    // Assert booking was still deleted despite email failure
    $this->assertDatabaseMissing('bookings', [
        'id' => $this->booking->id,
    ]);
});

test('admin email is also notified if configured differently from staff', function () {
    config(['business.contact.email' => 'admin-business@test.com']);

    Mail::fake();

    // Act as admin and delete booking
    $this->actingAs($this->admin)
        ->delete(route('admin.bookings.destroy', $this->booking))
        ->assertRedirect(route('admin.bookings.dashboard'));

    // Assert admin email was sent
    Mail::assertSent(BookingCancellation::class, function ($mail) {
        return $mail->hasTo('admin-business@test.com');
    });

    // Should have 3 emails total: customer, staff, and admin
    Mail::assertSent(BookingCancellation::class, 3);
});

test('only customer and admin emails sent if no staff assigned', function () {
    config(['business.contact.email' => 'admin-business@test.com']);

    // Create booking without staff
    $bookingNoStaff = Booking::factory()->create([
        'customer_name' => 'Jane Smith',
        'customer_email' => 'jane@example.com',
        'staff_id' => null,
    ]);

    Mail::fake();

    // Delete booking
    $this->actingAs($this->admin)
        ->delete(route('admin.bookings.destroy', $bookingNoStaff))
        ->assertRedirect(route('admin.bookings.dashboard'));

    // Assert customer email was sent
    Mail::assertSent(BookingCancellation::class, function ($mail) {
        return $mail->hasTo('jane@example.com');
    });

    // Assert admin email was sent
    Mail::assertSent(BookingCancellation::class, function ($mail) {
        return $mail->hasTo('admin-business@test.com');
    });

    // Should have 2 emails total: customer and admin (no staff)
    Mail::assertSent(BookingCancellation::class, 2);
});
