<?php

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

test('booking confirmation email can be sent', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ]);

    // Send the confirmation email
    Mail::to($booking->customer_email)
        ->send(new BookingConfirmation(
            booking: $booking,
            meetLink: $booking->google_meet_link,
            staffName: 'Test Staff'
        ));

    // Assert email was sent
    Mail::assertSent(BookingConfirmation::class, function ($mail) use ($booking) {
        return $mail->hasTo($booking->customer_email) &&
               $mail->booking->customer_name === $booking->customer_name;
    });
});

test('booking confirmation email contains meet link when available', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'customer_email' => 'test@example.com',
        'google_meet_link' => 'https://meet.google.com/abc-defg-hij',
    ]);

    $mail = new BookingConfirmation(
        booking: $booking,
        meetLink: $booking->google_meet_link,
        staffName: 'Test Staff'
    );

    Mail::to($booking->customer_email)->send($mail);

    Mail::assertSent(BookingConfirmation::class, function ($mail) use ($booking) {
        return $mail->meetLink === $booking->google_meet_link;
    });
});

test('booking confirmation email works without meet link', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'customer_email' => 'test@example.com',
        'google_meet_link' => null,
    ]);

    $mail = new BookingConfirmation(
        booking: $booking,
        meetLink: null,
        staffName: 'Test Staff'
    );

    Mail::to($booking->customer_email)->send($mail);

    Mail::assertSent(BookingConfirmation::class);
});

test('booking confirmation email has correct subject line', function () {
    $booking = Booking::factory()->create([
        'booking_date' => now()->addDays(1),
    ]);

    $mail = new BookingConfirmation(
        booking: $booking,
        meetLink: null,
        staffName: 'Test Staff'
    );

    $envelope = $mail->envelope();

    expect($envelope->subject)->toContain('Appointment Confirmed');
    expect($envelope->subject)->toContain($booking->booking_date->format('M j, Y'));
});
