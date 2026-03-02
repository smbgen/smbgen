<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    /**
     * Seed the availability settings for all admin users.
     *
     * This seeds the standard booking availability:
     * - Monday through Wednesday
     * - 10:00 AM - 4:00 PM Eastern Time
     * - 60-minute appointment slots (45min meeting + 15min buffer)
     * - 24-hour minimum booking notice
     * - 30-day maximum booking window
     *
     * Applies to all users with 'admin' role, or all users if no role field exists.
     */
    public function run(): void
    {
        // Get all admin users - if no role field exists, fall back to all users
        $adminUsers = User::where('role', 'admin')->get();

        // Fallback: if no admin role users found, use all users
        if ($adminUsers->isEmpty()) {
            $adminUsers = User::all();
        }

        if ($adminUsers->isEmpty()) {
            $this->command->error('No users found. Please ensure at least one user exists before seeding availability.');

            return;
        }

        $this->command->info("Setting up availability for {$adminUsers->count()} admin user(s)");

        // Monday through Wednesday: 10:00 AM - 4:00 PM (60-minute slots)
        $days = [
            ['day' => 1, 'name' => 'Monday'],
            ['day' => 2, 'name' => 'Tuesday'],
            ['day' => 3, 'name' => 'Wednesday'],
        ];

        foreach ($adminUsers as $adminUser) {
            $this->command->info("Processing: {$adminUser->name} ({$adminUser->email})");

            // Clear existing availability for this user
            Availability::where('user_id', $adminUser->id)->delete();

            foreach ($days as $dayInfo) {
                Availability::create([
                    'user_id' => $adminUser->id,
                    'day_of_week' => $dayInfo['day'],
                    'start_time' => '10:00',
                    'end_time' => '16:00',
                    'duration' => 60,
                    'is_active' => true,
                    'timezone' => 'America/New_York',
                    'minimum_booking_notice_hours' => 24,
                    'maximum_booking_days_ahead' => 30,
                ]);
            }

            $this->command->info("✅ Created Mon-Wed availability for {$adminUser->name} (18 slots per week)");
        }

        $this->command->info("✅ Availability seeded successfully for {$adminUsers->count()} admin user(s)!");
    }
}
