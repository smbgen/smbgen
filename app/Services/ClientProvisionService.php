<?php

namespace App\Services;

use App\Mail\ClientPortalAccessMail;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ClientProvisionService
{
    /**
     * Ensure a User exists for the given Client and send initial portal credentials when appropriate.
     */
    public static function provision(Client $client): void
    {
        $user = User::where('email', $client->email)->first();

        if (! $user) {
            // Create user with a random password that must be reset
            $user = User::create([
                'name' => $client->name,
                'email' => $client->email,
                'password' => bin2hex(random_bytes(16)), // Random password they won't use
                'role' => User::ROLE_CLIENT,
                'email_verified_at' => now(), // Auto-verify email on provisioning
            ]);

            $user->must_reset_password = true;
            $user->initial_password_sent_at = now();
            $user->save();
        } elseif (! empty($user->initial_password_sent_at)) {
            // User already provisioned
            return;
        } else {
            // Mark existing user as provisioned
            $user->must_reset_password = true;
            $user->initial_password_sent_at = now();
            $user->email_verified_at = now(); // Auto-verify email on provisioning
            $user->save();
        }

        // Send password reset link
        try {
            $token = Password::createToken($user);
            $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));

            Mail::to($user->email)->send(new ClientPortalAccessMail($user->name, $user->email, $resetUrl));

            // Mark client as provisioned
            $client->user_provisioned_at = now();
            $client->save();

            // Log account provisioning activity
            \App\Services\ActivityLogger::log(
                action: 'account_provisioned',
                description: "Client account provisioned for {$user->name}",
                subject: $client,
                properties: ['user_id' => $user->id, 'email' => $user->email],
                userId: auth()->id() // Admin who triggered provisioning
            );

            \Log::info('Client portal access email sent and account provisioned', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'client_id' => $client->id,
                'provisioned_at' => $client->user_provisioned_at,
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to send client portal access email: '.$e->getMessage(), [
                'email' => $user->email,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e; // Re-throw so controller can handle it
        }
    }
}
