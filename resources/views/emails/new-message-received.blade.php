@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; margin-bottom: 8px; color: #1e40af;">💬 New Message Received</h2>
    
    <p style="color: #6b7280; margin-bottom: 24px;">You have received a new message from <strong style="color: #1f2937;">{{ $sender->name ?? 'Unknown' }}</strong></p>

    <div style="background: #ffffff; padding: 24px; border-radius: 8px; margin: 24px 0; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @if($message->subject ?? false)
            <h3 style="margin-top: 0; margin-bottom: 16px; color: #1e40af; font-size: 18px; padding-bottom: 12px; border-bottom: 2px solid #3b82f6;">{{ $message->subject }}</h3>
        @endif
        
        <div style="margin-bottom: 16px; padding: 12px; background: #f9fafb; border-radius: 6px;">
            <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 14px;">
                <strong style="color: #374151;">From:</strong> {{ $sender->name ?? 'Unknown' }}
            </p>
            <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 13px;">
                <a href="mailto:{{ $sender->email ?? 'N/A' }}" style="color: #3b82f6; text-decoration: none;">{{ $sender->email ?? 'N/A' }}</a>
            </p>
            @if($message->created_at ?? false)
            <p style="margin: 0; color: #6b7280; font-size: 13px;">
                <strong style="color: #374151;">Date:</strong> {{ $message->created_at->format('M j, Y \a\t g:i A') }}
                <span style="color: #9ca3af;">{{ $message->created_at->format('T (P)') }}</span>
            </p>
            @endif
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <div style="white-space: pre-wrap; color: #1f2937; line-height: 1.7; font-size: 15px;">{{ $message->body ?? 'No message content' }}</div>
        </div>
    </div>

    @if($message ?? false)
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ route('messages.show', $message) }}" style="display: inline-block; background-color: #3b82f6; color: #ffffff !important; padding: 14px 36px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);">
            📧 View Full Message
        </a>
    </div>
    @endif

    <div style="margin-top: 32px; padding: 16px; background: #f9fafb; border-radius: 8px; text-align: center;">
        <p style="margin: 0; color: #6b7280; font-size: 13px;">
            This message was sent from your {{ config('business.company_name') ?? config('app.name') }} portal.
        </p>
    </div>
@endsection
