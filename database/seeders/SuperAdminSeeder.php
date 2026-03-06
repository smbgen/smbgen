<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create the initial admin user for smbgen.
     *
     * Run: php artisan db:seed --class=SuperAdminSeeder
     */
    public function run(): void
    {
        // Generate a secure random password
        $password = Str::password(32);

        $email = env('ADMIN_EMAIL', 'admin@smbgen.com');
        $name = env('ADMIN_NAME', 'Admin');

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'role' => 'company_administrator',
            ]
        );

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  ADMIN USER CREATED');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->warn('Admin Credentials:');
        $this->command->line("  Email:    {$admin->email}");
        $this->command->newLine();
        $this->command->warn('  PASSWORD: ' . $password);
        $this->command->newLine();
        $this->command->info('Access URL: ' . config('app.url') . '/admin/dashboard');
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->warn('⚠️  SAVE THESE CREDENTIALS NOW! They will not be shown again.');
        $this->command->newLine();
        $this->command->comment('💡 You can reset password via: /forgot-password');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();

        if ($admin->wasRecentlyCreated) {
            $this->command->info('Next Steps:');
            $this->command->line('  1. Save the password securely');
            $this->command->line('  2. Log in at /login');
            $this->command->line('  3. Visit /admin/dashboard');
            $this->command->line('  4. Change password after first login (recommended)');
            $this->command->newLine();
        } else {
            $this->command->warn('Note: Admin already existed. Password has been RESET.');
            $this->command->newLine();
        }
    }
}
