<?php

use App\Mail\BookingAdminNotification;
use App\Models\Booking;
use App\Models\User;

uses()->group('booking', 'notifications');

test('admin notification mailable includes customer information', function () {
    $booking = Booking::factory()->create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'customer_phone' => '555-9876',
    ]);

    $mailable = new BookingAdminNotification(
        booking: $booking,
        meetLink: null,
        staffName: 'Test Staff'
    );

    $mailable->assertSeeInHtml('Jane Doe');
    $mailable->assertSeeInHtml('jane@example.com');
    $mailable->assertSeeInHtml('555-9876');
});

test('custom form data is included in admin notification email', function () {
    $booking = Booking::factory()->create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'custom_form_data' => [
            'company_name' => 'Acme Corp',
            'budget_range' => '$5000-$10000',
            'project_timeline' => 'Q1 2026',
        ],
    ]);

    $mailable = new BookingAdminNotification(
        booking: $booking,
        meetLink: null,
        staffName: 'Test Staff'
    );

    $mailable->assertSeeInHtml('Jane Doe');
    $mailable->assertSeeInHtml('jane@example.com');
    $mailable->assertSeeInHtml('Acme Corp');
    $mailable->assertSeeInHtml('$5000-$10000');
    $mailable->assertSeeInHtml('Q1 2026');
    $mailable->assertSeeInHtml('Additional Form Responses');
});

test('booking notification preferences can be updated', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMINISTRATOR,
        'notify_on_new_bookings' => false,
        'notify_on_new_leads' => false,
    ]);

    $this->actingAs($admin);

    $response = $this->patch(route('admin.business_settings.update'), [
        'app_name' => config('app.name'),
        'company_name' => config('business.company_name'),
        'admin_notifications' => [
            $admin->id => [
                'notify_on_new_bookings' => true,
                'notify_on_new_leads' => true,
            ],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $admin->refresh();

    expect($admin->notify_on_new_bookings)->toBeTrue();
    expect($admin->notify_on_new_leads)->toBeTrue();
});

test('admin notification includes meet link when available', function () {
    $booking = Booking::factory()->create([
        'google_meet_link' => 'https://meet.google.com/abc-defg-hij',
    ]);

    $mailable = new BookingAdminNotification(
        booking: $booking,
        meetLink: $booking->google_meet_link,
        staffName: 'Test Staff'
    );

    $mailable->assertSeeInHtml('https://meet.google.com/abc-defg-hij');
    $mailable->assertSeeInHtml('Join Google Meet');
});

test('admin notification email has correct subject', function () {
    $booking = Booking::factory()->create([
        'booking_date' => now()->addDays(5),
    ]);

    $mailable = new BookingAdminNotification(
        booking: $booking,
        meetLink: null,
        staffName: 'John Admin'
    );

    expect($mailable->envelope()->subject)->toContain('New Booking Received');
});
