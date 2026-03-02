<?php

use App\Models\Booking;
use App\Models\User;

beforeEach(function () {
    config(['business.features.booking' => true]);

    $this->admin = User::factory()->create([
        'role' => 'company_administrator',
    ]);
});

test('booking dashboard paginates bookings', function () {
    Booking::factory()
        ->count(16)
        ->sequence(fn ($sequence) => [
            'customer_name' => "Customer {$sequence->index}",
            'customer_email' => "customer{$sequence->index}@example.com",
            'booking_date' => now()->addDays($sequence->index + 1),
            'booking_time' => '09:00:00',
        ])
        ->create();

    $pageOne = $this->actingAs($this->admin)
        ->get(route('admin.bookings.dashboard'));

    $pageOne->assertOk();
    $pageOne->assertSee('customer15@example.com');
    $pageOne->assertDontSee('customer0@example.com');
    $pageOne->assertSee('bookings/dashboard?page=2');
    $pageOne->assertSee('bookings');

    $pageTwo = $this->actingAs($this->admin)
        ->get(route('admin.bookings.dashboard', ['page' => 2]));

    $pageTwo->assertOk();
    $pageTwo->assertSee('customer0@example.com');
    $pageTwo->assertSee('Previous');
});
