<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccountSecurityNoticeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * Three cases — all return the same neutral success message to prevent
     * user enumeration (attacker cannot tell which case applied):
     *
     *   1. Email not registered       → no email sent, silent success
     *   2. Registered, no Google      → send password reset link + security notice
     *   3. Registered, Google-linked  → send "use Google" security notice, no reset link
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Case 3: Google-linked account — send notice, no reset link.
        if ($user && $user->google_id) {
            try {
                Mail::to($user->email)->send(new AccountSecurityNoticeMail($user, 'google_login_required'));
            } catch (\Exception $e) {
                \Log::warning('Failed to send Google login security notice', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Same response as a normal reset — don't reveal the account exists
            // or that it is Google-linked.
            return back()->with('status', __('passwords.sent'));
        }

        // Case 1 (email not registered) is handled transparently by sendResetLink
        // which returns INVALID_USER — we still return success below to prevent
        // enumeration. Case 2 (registered, no Google) sends the reset link.
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            // Case 2: also send a security notice alongside the reset link.
            try {
                Mail::to($user->email)->send(new AccountSecurityNoticeMail($user, 'password_reset_requested'));
            } catch (\Exception $e) {
                \Log::warning('Failed to send password reset security notice', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return back()->with('status', __($status));
        }

        // Case 1: email not found — return neutral success (no error shown).
        return back()->with('status', __('passwords.sent'));
    }
}
