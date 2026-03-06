<?php

namespace App\Http\Controllers;

use App\Mail\MagicLinkMail;
use App\Models\MagicLink;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MagicLinkController extends Controller
{
    /**
     * Send a magic link to the given user (admin action).
     */
    public function send(User $user)
    {
        // ensure caller is an authenticated company administrator
        if (! Auth::check() || Auth::user()->role !== 'company_administrator') {
            abort(403);
        }

        $token = Str::random(64);
        $expiresAt = Carbon::now()->addMinutes(30);

        $magic = MagicLink::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => $expiresAt,
        ]);

        $linkUrl = route('magic.consume', ['token' => $token]);

        try {
            Mail::to($user->email)->send(new MagicLinkMail($user->name, $linkUrl, $expiresAt));

            return back()->with('success', 'Magic link sent to '.$user->email);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send magic link', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            // Return friendly error and go back to the client page
            return back()->with('error', 'Failed to send magic link. Please check mail configuration.');
        }
    }

    /**
     * Consume a magic link token and log the user in.
     */
    public function consume(Request $request, string $token)
    {
        $hashed = hash('sha256', $token);

        $magic = MagicLink::where('token', $hashed)->first();

        if (! $magic || ! hash_equals($magic->token, $hashed) || ! $magic->isValid()) {
            Log::warning('Magic link consumption failed', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('login')->withErrors(['magic' => 'This login link is invalid or expired.']);
        }

        // mark used
        $magic->used_at = Carbon::now();
        $magic->save();

        // login the user
        $user = $magic->user;
        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}
