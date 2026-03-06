<?php

use App\Mail\BookingConfirmation;
use App\Models\Booking;

test('booking confirmation mailable has correct subject', function () {
    $booking = new Booking([
        'booking_date' => now()->addDays(1),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ]);

    $mailable = new BookingConfirmation(
        booking: $booking,
        meetLink: null,
        staffName: 'Test Staff'
    );

    $envelope = $mailable->envelope();

    expect($envelope->subject)->toContain('Appointment Confirmed');
});

test('booking confirmation mailable stores meet link', function () {
    $booking = new Booking([
        'booking_date' => now()->addDays(1),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ]);

    $meetLink = 'https://meet.google.com/abc-defg-hij';

    $mailable = new BookingConfirmation(
        booking: $booking,
        meetLink: $meetLink,
        staffName: 'Test Staff'
    );

    expect($mailable->meetLink)->toBe($meetLink);
});

test('booking confirmation mailable stores staff name', function () {
    $booking = new Booking([
        'booking_date' => now()->addDays(1),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ]);

    $staffName = 'Jane Smith';

    $mailable = new BookingConfirmation(
        booking: $booking,
        meetLink: null,
        staffName: $staffName
    );

    expect($mailable->staffName)->toBe($staffName);
});

test('booking confirmation mailable uses correct view', function () {
    $booking = new Booking([
        'booking_date' => now()->addDays(1),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ]);

    $mailable = new BookingConfirmation(
        booking: $booking,
        meetLink: null,
        staffName: 'Test Staff'
    );

    $content = $mailable->content();

    expect($content->view)->toBe('emails.booking-reminder');
});
