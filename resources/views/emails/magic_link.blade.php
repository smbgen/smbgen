@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; color: #1e40af;">Hello {{ $userName }},</h2>
    <p>You requested a magic login link. Click the button below to sign in.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $linkUrl }}" class="btn">
            Sign Me In
        </a>
    </div>

    <p style="color: #666; font-size: 14px;">This link will expire at <strong>{{ $expiresAt }}</strong> and can only be used once.</p>
    
    <p style="margin-top: 30px; color: #666; font-size: 13px;">
        If you didn't request this, please ignore this email or contact support if you're concerned about your account security.
    </p>

    <p>Thanks,<br/>The {{ config('app.name') }} Team</p>
@endsection

