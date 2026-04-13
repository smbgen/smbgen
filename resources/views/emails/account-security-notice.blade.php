@extends('layouts.email')

@section('content')

@if ($type === 'google_login_required')

    {{-- ── Google account: no password reset sent ─────────────────────────── --}}
    <h2 style="margin-top: 0; margin-bottom: 8px; color: #1e40af;">Account access notice</h2>

    <p>Hi {{ $user->name }},</p>

    <p>We received a request to reset the password for your {{ config('app.name') }} account.</p>

    <p>Your account is set up to sign in with <strong>Google</strong> — you don't use a separate password to log in, so no password reset email was sent.</p>

    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 24px; margin: 28px 0; text-align: center;">
        <p style="margin: 0 0 18px 0; color: #1e3a8a; font-weight: 600;">To access your account, use the button below on the login page:</p>
        <a href="{{ config('app.url') }}/login" class="btn" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff !important; text-decoration: none !important; border-radius: 8px; font-weight: 700; font-size: 16px;">
            Go to Login →
        </a>
        <p style="margin: 16px 0 0 0; font-size: 13px; color: #6b7280;">Click <strong>Continue with Google</strong> once you arrive.</p>
    </div>

    <p style="font-size: 14px; color: #6b7280;">If you didn't make this request, your account is secure and no action is needed. Nobody can access your account without signing in through Google.</p>

@else

    {{-- ── Password reset requested ────────────────────────────────────────── --}}
    <h2 style="margin-top: 0; margin-bottom: 8px; color: #1e40af;">Password reset requested</h2>

    <p>Hi {{ $user->name }},</p>

    <p>We received a request to reset the password for your {{ config('app.name') }} account. A separate email with a reset link has been sent to this address.</p>

    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #22c55e; border-radius: 8px; padding: 16px 20px; margin: 24px 0;">
        <p style="margin: 0; color: #166534; font-size: 14px; line-height: 1.6;">
            <strong>This was you?</strong> Check your inbox for the reset link — it expires in 60 minutes.
        </p>
    </div>

    <div style="background: #fff7ed; border: 1px solid #fed7aa; border-left: 4px solid #f97316; border-radius: 8px; padding: 16px 20px; margin: 24px 0;">
        <p style="margin: 0; color: #9a3412; font-size: 14px; line-height: 1.6;">
            <strong>Wasn't you?</strong> Your account is still secure — no changes have been made. You can safely ignore both emails. If you're concerned, <a href="{{ config('app.url') }}/login" style="color: #c2410c; font-weight: 600;">log in</a> and change your password now.
        </p>
    </div>

    <p style="font-size: 13px; color: #9ca3af; margin-top: 28px; border-top: 1px solid #f3f4f6; padding-top: 16px;">
        This notice was sent because a password reset was requested for <strong>{{ $user->email }}</strong>. If you did not make this request, no further action is required.
    </p>

@endif

@endsection
