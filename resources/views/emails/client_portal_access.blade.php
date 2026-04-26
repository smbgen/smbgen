@extends('layouts.email')

@section('content')
    @php
        $brandName = config('business.name')
            ?: config('business.company_name')
            ?: config('app.company_name', config('app.name', 'smbgen'));
    @endphp

    <h2 style="margin-top: 0; color: #1e40af;">Hello {{ $clientName }},</h2>
    <p>Welcome to {{ $brandName }}! Your client portal account has been created.</p>

    <p style="margin: 30px 0;">
        <strong>Your Email:</strong> {{ $emailAddress }}
    </p>

    <p>To set your password and access your portal, please click the button below:</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $password }}" class="btn">
            Set Your Password & Login
        </a>
    </div>

    <p style="color: #666; font-size: 14px;">Or copy and paste this link into your browser:</p>
    <p style="color: #3b82f6; font-size: 14px; word-break: break-all;">{{ $password }}</p>

    <p style="margin-top: 30px; color: #666; font-size: 13px;">
        This link will expire in 60 minutes. If you didn't request this account or need assistance, please contact our support team.
    </p>

    <p>Thanks,<br/>The {{ $brandName }} Team</p>
@endsection

