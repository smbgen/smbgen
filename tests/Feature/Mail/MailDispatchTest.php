<?php

use App\Mail\BookingCancellation;
use App\Mail\BookingConfirmation;
use App\Mail\NewLeadNotification;
use App\Models\Booking;
use App\Models\LeadForm;
use Illuminate\Support\Facades\Mail;

test('booking confirmation email is queued when sent', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'customer_email' => 'customer@example.com',
    ]);

    Mail::to($booking->customer_email)
        ->send(new BookingConfirmation(booking: $booking));

    Mail::assertSent(BookingConfirmation::class);
    Mail::assertSentCount(1);
});

test('booking confirmation email is addressed to the customer', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'customer_email' => 'jane@example.com',
    ]);

    Mail::to($booking->customer_email)
        ->send(new BookingConfirmation(booking: $booking));

    Mail::assertSent(BookingConfirmation::class, fn ($mail) => $mail->hasTo('jane@example.com'));
});

test('booking cancellation email contains the booking details', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'customer_email' => 'cancel@example.com',
        'booking_date' => now()->addDays(2),
        'booking_time' => '14:00:00',
    ]);

    Mail::to($booking->customer_email)
        ->send(new BookingCancellation(booking: $booking, recipientType: 'customer'));

    Mail::assertSent(BookingCancellation::class, function ($mail) use ($booking) {
        return $mail->hasTo($booking->customer_email)
            && $mail->booking->id === $booking->id
            && $mail->recipientType === 'customer';
    });
});

test('booking cancellation staff email is addressed to staff recipient type', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'customer_email' => 'customer@example.com',
        'booking_date' => now()->addDays(3),
        'booking_time' => '09:00:00',
    ]);

    Mail::to('admin@example.com')
        ->send(new BookingCancellation(booking: $booking, recipientType: 'staff'));

    Mail::assertSent(BookingCancellation::class, fn ($mail) => $mail->recipientType === 'staff');
});

test('lead notification email is sent with correct subject', function () {
    Mail::fake();

    $lead = LeadForm::factory()->create([
        'name' => 'Bob Smith',
        'email' => 'bob@example.com',
    ]);

    Mail::to(config('business.contact.email', 'admin@example.com'))
        ->send(new NewLeadNotification(lead: $lead));

    Mail::assertSent(NewLeadNotification::class, fn ($mail) => $mail->envelope()->subject === 'New Lead: Bob Smith');
});

test('no emails are sent without explicit dispatch', function () {
    Mail::fake();

    Booking::factory()->create();

    Mail::assertNothingSent();
});

test('mail mailer is set to array driver in test environment', function () {
    expect(config('mail.default'))->toBe('array');
});
