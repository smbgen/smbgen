<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\confirm;

class UserSeederWithPasswordPrompt extends Seeder
{
    /**
     * Seed the application with demo users using prompted passwords.
     *
     * Run: php artisan db:seed --class=UserSeederWithPasswordPrompt
     *
     * This seeder prompts for passwords interactively, providing better
     * security than hardcoded or randomly generated passwords.
     */
    public function run(): void
    {
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  USER SEEDER WITH PASSWORD PROMPT');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();

        // Admin User
        if (confirm('Create Admin User?', true)) {
            $this->command->info('Enter password for Admin User (admin@clientbridge.app):');
            $adminPassword = $this->command->secret('Password (min 8 characters)');

            if (empty($adminPassword)) {
                $this->command->error('Password cannot be empty. Skipping admin user.');
            } else {
                $admin = User::updateOrCreate(
                    ['email' => 'admin@clientbridge.app'],
                    [
                        'name' => 'Admin User',
                        'password' => Hash::make($adminPassword),
                        'email_verified_at' => now(),
                        'role' => 'company_administrator',
                    ]
                );

                $this->command->info("✓ Admin user created/updated: {$admin->email}");
            }
            $this->command->newLine();
        }

        // Demo Client User
        if (confirm('Create Demo Client User?', true)) {
            $this->command->info('Enter password for Demo Client (demo@clientbridge.app):');
            $demoPassword = $this->command->secret('Password (min 8 characters)');

            if (empty($demoPassword)) {
                $this->command->error('Password cannot be empty. Skipping demo client.');
            } else {
                $demo = User::updateOrCreate(
                    ['email' => 'demo@clientbridge.app'],
                    [
                        'name' => 'Demo Client',
                        'password' => Hash::make($demoPassword),
                        'email_verified_at' => now(),
                        'role' => 'client',
                    ]
                );

                $this->command->info("✓ Demo client created/updated: {$demo->email}");
            }
            $this->command->newLine();
        }

        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  SEEDING COMPLETE');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
    }
}
