@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; margin-bottom: 8px; color: #1e40af;">💬 New Message</h2>
    
    <p style="color: #6b7280; margin-bottom: 24px;">You have received a message from <strong style="color: #1f2937;">{{ $senderName }}</strong></p>

    <div style="background: #ffffff; padding: 24px; border-radius: 8px; margin: 24px 0; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @if($subject)
            <h3 style="margin-top: 0; margin-bottom: 16px; color: #1e40af; font-size: 18px; padding-bottom: 12px; border-bottom: 2px solid #3b82f6;">{{ $subject }}</h3>
        @endif
        
        <div style="margin-bottom: 16px; padding: 12px; background: #f9fafb; border-radius: 6px;">
            <p style="margin: 0; color: #6b7280; font-size: 14px;">
                <strong style="color: #374151;">From:</strong> {{ $senderName }}
            </p>
            <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 13px;">
                <a href="mailto:{{ $senderEmail }}" style="color: #3b82f6; text-decoration: none;">{{ $senderEmail }}</a>
            </p>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <div style="white-space: pre-wrap; color: #1f2937; line-height: 1.7; font-size: 15px;">{!! $body !!}</div>
        </div>
    </div>

    @if($hasAccount ?? false)
        <div style="text-align: center; margin: 32px 0;">
            <a href="{{ $messageUrl ?? $messagesUrl ?? '#' }}" style="display: inline-block; background-color: #3b82f6; color: #ffffff !important; padding: 14px 36px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);">
                📨 View Message in {{ config('app.name') }}
            </a>
        </div>
        
        <div style="margin-top: 24px; padding: 16px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
            <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.6;">
                💡 <strong>Tip:</strong> Log in to your <a href="{{ $loginUrl ?? '#' }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">{{ config('app.name') }} account</a> to reply and view your complete message history.
            </p>
        </div>
    @else
        <div style="margin-top: 32px; padding: 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #6b7280;">
            <p style="margin: 0; color: #374151; font-size: 14px;">
                ↩️ <strong>Reply:</strong> Respond directly to <a href="mailto:{{ $senderEmail }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">{{ $senderEmail }}</a>
            </p>
        </div>
    @endif
@endsection
