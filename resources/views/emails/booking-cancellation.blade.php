@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; color: #dc2626;">❌ Appointment Cancelled</h2>
    
    <div style="display: inline-block; background-color: #fee2e2; color: #991b1b; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-bottom: 20px;">
        🚫 Cancellation Notice
    </div>

    @if($recipientType === 'customer')
        <p>Hello {{ $booking->customer_name ?? 'Valued Customer' }},</p>

        <p>This email confirms that your appointment has been <strong>cancelled</strong>.</p>
    @else
        <p>Hello {{ $booking->staff ? $booking->staff->name : 'Team Member' }},</p>

        <p>This email confirms that a booking appointment has been <strong>cancelled</strong>.</p>
    @endif

    <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 24px; margin: 24px 0; border-radius: 8px; border: 1px solid #fecaca;">
        <h3 style="margin-top: 0; color: #991b1b; font-size: 18px; margin-bottom: 20px;">📅 Cancelled Appointment Details</h3>
        
        @if($recipientType === 'staff')
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #fecaca;">
            <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px;">Customer:</span>
            <span style="color: #991b1b; font-weight: 500;">{{ $booking->customer_name ?? 'N/A' }}</span>
        </div>
        
        @if($booking->customer_email)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #fecaca;">
            <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px;">Email:</span>
            <span style="color: #991b1b; font-weight: 500;">{{ $booking->customer_email }}</span>
        </div>
        @endif
        
        @if($booking->customer_phone)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #fecaca;">
            <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px;">Phone:</span>
            <span style="color: #991b1b; font-weight: 500;">{{ $booking->customer_phone }}</span>
        </div>
        @endif
        @endif
        
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #fecaca;">
            <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px;">Date:</span>
            <span style="color: #991b1b; font-weight: 500;">{{ $booking->booking_date ? $booking->booking_date->format('l, F j, Y') : 'TBD' }}</span>
        </div>
        
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #fecaca;">
            <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px;">Time:</span>
            <span style="color: #991b1b; font-weight: 500;">
                @if($booking->booking_time)
                    @if(is_string($booking->booking_time))
                        {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}
                    @else
                        {{ $booking->booking_time->format('g:i A') }}
                    @endif
                @else
                    TBD
                @endif
            </span>
        </div>
        
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #fecaca;">
            <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px;">Duration:</span>
            <span style="color: #991b1b; font-weight: 500;">{{ $booking->duration ?? 'TBD' }} minutes</span>
        </div>
        
        @if($booking->property_address)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #fecaca;">
            <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px; vertical-align: top;">Location:</span>
            <span style="color: #991b1b; font-weight: 500;">{{ $booking->property_address }}</span>
        </div>
        @endif

        @if($booking->notes)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #fecaca;">
            <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px; vertical-align: top;">Notes:</span>
            <span style="color: #991b1b; font-weight: 500;">{{ $booking->notes }}</span>
        </div>
        @endif
        
        @if($booking->custom_form_data && count($booking->custom_form_data) > 0)
            @foreach($booking->custom_form_data as $fieldName => $fieldValue)
                @if($fieldValue)
                <div style="margin: 14px 0; padding: 10px 0; {{ !$loop->last ? 'border-bottom: 1px solid #fecaca;' : '' }}">
                    <span style="font-weight: 600; color: #7f1d1d; display: inline-block; width: 120px; vertical-align: top;">{{ ucwords(str_replace('_', ' ', $fieldName)) }}:</span>
                    <span style="color: #991b1b; font-weight: 500;">
                        @if(is_array($fieldValue))
                            {{ implode(', ', $fieldValue) }}
                        @else
                            {{ $fieldValue }}
                        @endif
                    </span>
                </div>
                @endif
            @endforeach
        @endif
    </div>

    @if($recipientType === 'customer')
        @php
            $contactEmail = config('business.contact.email', config('mail.from.address'));
            $contactPhone = config('business.contact.phone', '');
        @endphp

        <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 20px 24px; margin: 24px 0; border-radius: 8px; border: 1px solid #bbf7d0;">
            <h4 style="margin-top: 0; margin-bottom: 12px; color: #15803d; font-size: 16px;">📅 Need to Reschedule?</h4>
            <p style="margin: 0; color: #1f2937; line-height: 1.7;">
                We'd be happy to help you book a new appointment at a time that works better for you.
            </p>
            <p style="margin: 12px 0 0 0; color: #1f2937; line-height: 1.7;">
                @if($contactEmail)
                    <strong style="color: #374151;">Email:</strong> <a href="mailto:{{ $contactEmail }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">{{ $contactEmail }}</a><br>
                @endif
                @if($contactPhone)
                    <strong style="color: #374151;">Phone:</strong> <a href="tel:{{ $contactPhone }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">{{ $contactPhone }}</a>
                @endif
            </p>
        </div>

        <p style="margin-top: 30px;">
            Thank you for your understanding.<br>
            <strong>{{ $booking->staff ? $booking->staff->name : 'The Team' }}</strong>
        </p>
    @else
        <p style="margin-top: 30px; color: #6b7280;">
            This is an automated notification. The calendar event has been removed from your Google Calendar if it was previously synced.
        </p>
    @endif
@endsection
