<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

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
                \Log::warning('Failed to log email verification: ' . $e->getMessage());
            }
        }

        // Redirect based on role after verification
        if ($user->role === 'company_administrator') {
            return redirect()->route('admin.dashboard')->with('status', 'Email verified successfully!');
        }

        return redirect()->route('dashboard')->with('status', 'Email verified successfully!');
    }
}
