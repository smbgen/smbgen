<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create a super admin user for central/landlord database.
     *
     * This seeder creates a super admin user who can manage all tenants
     * in the system. Super admins are NOT tenant users - they exist in
     * the central database only.
     *
     * Run: php artisan db:seed --class=SuperAdminSeeder
     *
     * IMPORTANT: This seeder should ONLY be run on the central database,
     * not on tenant databases. Super admins manage tenants from the
     * central application context.
     */
    public function run(): void
    {
        // Generate a secure random password
        $password = Str::password(32);

        // Default super admin email (can be overridden via environment)
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@clientbridge.app');
        $name = env('SUPER_ADMIN_NAME', 'Super Admin');

        // Create or update the super admin user
        $superAdmin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'role' => 'super_admin',
                'tenant_id' => null, // Super admins don't belong to any tenant
            ]
        );

        // Output credentials
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  SUPER ADMIN CREATED');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->warn('Super Admin Credentials:');
        $this->command->line("  Email:    {$superAdmin->email}");
        $this->command->newLine();
        $this->command->warn('  PASSWORD: ' . $password);
        $this->command->newLine();
        $this->command->line("  Role:     {$superAdmin->role}");
        $this->command->newLine();
        $this->command->info('Access URL: ' . config('app.url') . '/super-admin');
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->warn('⚠️  SAVE THESE CREDENTIALS NOW! They will not be shown again.');
        $this->command->newLine();
        $this->command->comment('💡 You can reset password via: /forgot-password');
        $this->command->comment('🔒 This account can manage ALL tenants in the system.');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();

        // Additional setup instructions
        if ($superAdmin->wasRecentlyCreated) {
            $this->command->info('Next Steps:');
            $this->command->line('  1. Save the password securely');
            $this->command->line('  2. Log in at /login');
            $this->command->line('  3. Visit /super-admin to manage tenants');
            $this->command->line('  4. Change password after first login (recommended)');
            $this->command->newLine();
        } else {
            $this->command->warn('Note: Super admin already existed. Password has been RESET.');
            $this->command->newLine();
        }
    }
}
