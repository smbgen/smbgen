<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     * This route is guest-accessible to allow unauthenticated users to verify emails from links.
     */
    public function __invoke(Request $request, string $id, string $hash): RedirectResponse
    {
        $expires = $request->query('expires');
        $expiresTimestamp = is_numeric($expires) ? (int) $expires : null;

        Log::info('Email verification link opened', [
            'route_id' => $id,
            'is_authenticated' => $request->user() !== null,
            'request_path' => $request->path(),
            'expires_query' => $expires,
            'expires_iso' => $expiresTimestamp ? now()->setTimestamp($expiresTimestamp)->toIso8601String() : null,
            'now_iso' => now()->toIso8601String(),
            'seconds_past_expiry' => $expiresTimestamp ? (now()->timestamp - $expiresTimestamp) : null,
            'app_timezone' => config('app.timezone'),
        ]);

        $signatureValid = $request->hasValidSignature(absolute: false);

        if (! $signatureValid) {
            $signatureValid = $this->hasValidSignatureFromMalformedAmpKey($request);
        }

        if (! $signatureValid) {
            Log::info('Email verification signature invalid after normalization attempts', [
                'route_id' => $id,
                'url' => $request->fullUrl(),
                'is_authenticated' => $request->user() !== null,
            ]);

            if ($request->user()) {
                return redirect()->route('verification.notice')
                    ->with('error', 'Verification link is invalid or expired. Please request a new one.');
            }

            return redirect()->route('login')
                ->with('error', 'Verification link is invalid or expired. Please request a new one.');
        }

        // If user is authenticated, prefer to use EmailVerificationRequest for better validation
        if ($request->user()) {
            // Use the authenticated path
            $user = $request->user();

            // Verify the hash matches the user's email
            if (sha1($user->getEmailForVerification()) !== $hash) {
                Log::info('Email verification hash mismatch for authenticated user', [
                    'user_id' => $user->id,
                    'route_id' => $id,
                    'email' => $user->email,
                ]);

                return redirect()->route('dashboard')->with('error', 'Invalid verification link.');
            }

            if ($user->hasVerifiedEmail()) {
                // Redirect based on role
                if ($user->role === 'company_administrator') {
                    return redirect()->route('admin.dashboard')->with('status', 'Email already verified');
                }

                return redirect()->route('dashboard')->with('status', 'Email already verified');
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));

                // Log the verification (wrapped in try-catch to prevent errors)
                try {
                    \App\Services\ActivityLogger::log(
                        action: 'email_verified',
                        description: 'User verified their email address',
                        subject: null,
                        properties: [],
                        userId: $user->id
                    );
                } catch (\Exception $e) {
                    // Silently fail if activity logging doesn't work
                    \Log::warning('Failed to log email verification: '.$e->getMessage());
                }
            }

            // Redirect based on role after verification
            if ($user->role === 'company_administrator') {
                return redirect()->route('admin.dashboard')->with('status', 'Email verified successfully!');
            }

            return redirect()->route('dashboard')->with('status', 'Email verified successfully!');
        }

        // Unauthenticated user clicking the link - find the user and mark as verified
        $user = User::findOrFail($id);

        // Verify the hash matches the user's email
        if (sha1($user->getEmailForVerification()) !== $hash) {
            Log::info('Email verification hash mismatch for guest user', [
                'user_id' => $user->id,
                'route_id' => $id,
                'email' => $user->email,
            ]);

            return redirect()->route('login')->with('error', 'Invalid verification link. Please try again.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email already verified. You can now log in.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            // Log the verification (wrapped in try-catch to prevent errors)
            try {
                \App\Services\ActivityLogger::log(
                    action: 'email_verified',
                    description: 'User verified their email address',
                    subject: null,
                    properties: [],
                    userId: $user->id
                );
            } catch (\Exception $e) {
                // Silently fail if activity logging doesn't work
                \Log::warning('Failed to log email verification: '.$e->getMessage());
            }
        }

        return redirect()->route('login')->with('status', 'Email verified successfully! You can now log in.');
    }

    private function hasValidSignatureFromMalformedAmpKey(Request $request): bool
    {
        $query = $request->query();

        if (! isset($query['amp;signature']) || isset($query['signature'])) {
            return false;
        }

        $query['signature'] = $query['amp;signature'];
        unset($query['amp;signature']);

        $normalizedUrl = $request->url().'?'.http_build_query($query);
        $normalizedRequest = Request::create($normalizedUrl, 'GET');
        $hasValidSignature = URL::hasValidSignature($normalizedRequest, absolute: false);

        Log::info('Email verification malformed signature key detected', [
            'original_url' => $request->fullUrl(),
            'normalized_url' => $normalizedUrl,
            'normalized_valid' => $hasValidSignature,
        ]);

        return $hasValidSignature;
    }
}
