<?php

namespace App\Listeners;

use App\Models\Client;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TrackClientLogin implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Only track client logins, not admin logins
        if ($user->role !== 'client') {
            return;
        }

        // Find client by email
        $client = Client::where('email', $user->email)->first();

        if (! $client) {
            return;
        }

        // Update last login
        $client->last_login_at = now();

        // If this is their first login, mark account as activated
        if (! $client->account_activated_at) {
            $client->account_activated_at = now();
        }

        $client->save();

        \Log::info('Client login tracked', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'client_id' => $client->id,
            'last_login_at' => $client->last_login_at,
            'account_activated_at' => $client->account_activated_at,
        ]);
    }
}
