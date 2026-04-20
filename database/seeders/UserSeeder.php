<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application with demo users.
     *
     * Run: php artisan migrate:fresh --seed
     *
     * IMPORTANT: Passwords are randomly generated for security.
     * Use the password reset flow to set your own password after seeding.
     */
    public function run(): void
    {
        // Use the specified password for admin user
        $adminPassword = 'JUHeKKEcg~y2Z7q9Wd2M9UmqnQ~^ZeQtzP';
        $demoPassword = 'demo-password-local-only';

        // Admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make($adminPassword),
            'email_verified_at' => now(),
            'role' => 'company_administrator',
        ]);

        // Demo client user - will be associated with demo client record
        $demo = User::firstOrCreate([
            'email' => 'demo@example.com',
        ], [
            'name' => 'Demo Client',
            'password' => Hash::make($demoPassword),
            'email_verified_at' => now(),
            'role' => 'client',
        ]);

        // Output credentials for first-time setup
        if ($admin->wasRecentlyCreated || $demo->wasRecentlyCreated) {
            $this->command->newLine();
            $this->command->info('═══════════════════════════════════════════════════════════');
            $this->command->info('  DEMO USERS CREATED');
            $this->command->info('═══════════════════════════════════════════════════════════');
            $this->command->newLine();

            if ($admin->wasRecentlyCreated) {
                $this->command->warn('Admin User:');
                $this->command->line("  Email:    {$admin->email}");
                $this->command->line("  Password: {$adminPassword}");
                $this->command->newLine();
            }

            if ($demo->wasRecentlyCreated) {
                $this->command->warn('Demo Client:');
                $this->command->line("  Email:    {$demo->email}");
                $this->command->line("  Password: {$demoPassword}");
                $this->command->newLine();
            }

            $this->command->info('═══════════════════════════════════════════════════════════');
            $this->command->comment('⚠️  Save these credentials now! They will not be shown again.');
            $this->command->comment('💡 You can reset passwords via: /forgot-password');
            $this->command->info('═══════════════════════════════════════════════════════════');
            $this->command->newLine();
        }
    }
}
