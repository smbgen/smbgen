<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Database\Seeder;

class AvailabilityForUserSeeder extends Seeder
{
    /**
     * Seed availability for a specific user by email.
     *
     * Usage:
     *   php artisan db:seed --class=AvailabilityForUserSeeder
     *
     * Or specify a custom email:
     *   EMAIL=user@example.com php artisan db:seed --class=AvailabilityForUserSeeder
     */
    public function run(): void
    {
        // Get email from environment variable or use default
        $email = env('SEED_USER_EMAIL', 'alexramsey92@gmail.com');

        // Find the user by email
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->command->error("User with email '{$email}' not found.");
            $this->command->info('Available users:');
            User::all()->each(function ($u) {
                $this->command->line("  - {$u->email} (ID: {$u->id}, Role: {$u->role})");
            });

            return;
        }

        // Check if user is an admin
        if (! $user->isAdministrator()) {
            $this->command->warn("User {$user->email} is not a company_administrator (current role: {$user->role})");
            $this->command->ask('Do you want to continue anyway? (yes/no)', 'no');

            return;
        }

        $this->command->info("Setting up availability for: {$user->name} ({$user->email}, ID: {$user->id})");

        // Clear existing availability for this user
        $existingCount = Availability::where('user_id', $user->id)->count();
        if ($existingCount > 0) {
            Availability::where('user_id', $user->id)->delete();
            $this->command->info("Cleared {$existingCount} existing availability records");
        }

        // Monday through Thursday: 9:00 AM - 5:00 PM (90-minute slots)
        $days = [
            ['day' => 1, 'name' => 'Monday'],
            ['day' => 2, 'name' => 'Tuesday'],
            ['day' => 3, 'name' => 'Wednesday'],
            ['day' => 4, 'name' => 'Thursday'],
        ];

        foreach ($days as $dayInfo) {
            Availability::create([
                'user_id' => $user->id,
                'day_of_week' => $dayInfo['day'],
                'start_time' => '09:00',
                'end_time' => '17:00',
                'duration' => 90,
                'is_active' => true,
                'timezone' => 'America/New_York',
                'minimum_booking_notice_hours' => 24,
                'maximum_booking_days_ahead' => 28,
            ]);

            $this->command->info("  ✓ {$dayInfo['name']} (9:00 AM - 5:00 PM ET, 90-min slots)");
        }

        $this->command->info('');
        $this->command->info('✅ Availability seeded successfully!');
        $this->command->info("User {$user->email} now has 4 days of availability");
    }
}
