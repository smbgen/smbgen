@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; color: #1e40af;">🔔 Appointment Reminder</h2>
    
    <div style="display: inline-block; background-color: #fef3c7; color: #92400e; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-bottom: 20px;">
        ⏰ Upcoming Appointment
    </div>

    <p>Hello {{ $booking->customer_name ?? 'Valued Customer' }},</p>

    <p>This is a friendly reminder about your upcoming appointment with {{ $staffName ?? 'our team' }}.</p>

    <div style="background-color: #f8fafc; border-left: 4px solid #3b82f6; padding: 24px; margin: 24px 0; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h3 style="margin-top: 0; color: #1e40af; font-size: 18px; margin-bottom: 20px;">📅 Appointment Details</h3>
        
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 100px;">Date:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->booking_date ? $booking->booking_date->format('l, F j, Y') : 'TBD' }}</span>
        </div>
        
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 100px;">Time:</span>
            <span style="color: #1f2937; font-weight: 500;">
                {{ $booking->booking_time ?? 'TBD' }}
                @if($booking->booking_date && $booking->booking_time)
                    <br>
                    <span style="color: #6b7280; font-size: 13px; font-weight: 400;">
                        {{ $booking->timezone ?? config('app.timezone', 'UTC') }} 
                        ({{ \Carbon\Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->booking_time, $booking->timezone ?? config('app.timezone', 'UTC'))->format('T P') }})
                    </span>
                @endif
            </span>
        </div>
        
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 100px;">Duration:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->duration ?? 'TBD' }} minutes</span>
        </div>
        
        @if($booking->customer_phone)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 120px;">Your Phone:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->customer_phone }}</span>
        </div>
        @endif
        
        @if($booking->property_address)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 120px; vertical-align: top;">Property Address:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->property_address }}</span>
        </div>
        @endif

        @if($booking->notes)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 120px; vertical-align: top;">Notes:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->notes }}</span>
        </div>
        @endif
        
        @if($booking->custom_form_data && count($booking->custom_form_data) > 0)
            @foreach($booking->custom_form_data as $fieldName => $fieldValue)
                @if($fieldValue)
                <div style="margin: 14px 0; padding: 10px 0; {{ !$loop->last ? 'border-bottom: 1px solid #e5e7eb;' : '' }}">
                    <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 120px; vertical-align: top;">{{ ucwords(str_replace('_', ' ', $fieldName)) }}:</span>
                    <span style="color: #1f2937; font-weight: 500;">
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

    @if($meetLink)
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $meetLink }}" style="display: inline-block; background-color: #3b82f6; color: #ffffff !important; padding: 14px 36px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);">
            🎥 Join Google Meet
        </a>
    </div>
    
    <!-- <div style="background-color: #fef9e7; border: 1px solid #f7dc6f; padding: 15px; border-radius: 6px; margin: 20px 0;">
        <h4 style="margin-top: 0; color: #856404; font-size: 16px;">📋 Before Your Appointment:</h4>
        <ul style="margin: 10px 0; padding-left: 20px; color: #856404;">
            <li style="margin: 8px 0;">Join the meeting 2-3 minutes early</li>
            <li style="margin: 8px 0;">Ensure you have a stable internet connection</li>
            <li style="margin: 8px 0;">Test your camera and microphone beforehand</li>
            <li style="margin: 8px 0;">Have any relevant documents or questions ready</li>
        </ul>
    </div> -->
    @else
    <p style="text-align: center; padding: 20px; background-color: #fef3c7; border-radius: 8px; margin: 20px 0;">
        <strong>Meeting details will be shared closer to the appointment time.</strong>
    </p>
    @endif

    @php
        $contactEmail = config('business.contact.email', config('mail.from.address'));
        $contactPhone = config('business.contact.phone', '');
    @endphp

    <div style="background-color: #f0f9ff; border-left: 4px solid #3b82f6; padding: 20px 24px; margin: 24px 0; border-radius: 8px; border: 1px solid #bfdbfe;">
        <h4 style="margin-top: 0; margin-bottom: 12px; color: #1e40af; font-size: 16px;">📞 Need to Reschedule or Have Questions?</h4>
        <p style="margin: 0; color: #1f2937; line-height: 1.7;">
            @if($contactEmail)
                <strong style="color: #374151;">Email:</strong> <a href="mailto:{{ $contactEmail }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">{{ $contactEmail }}</a><br>
            @endif
            @if($contactPhone)
                <strong style="color: #374151;">Phone:</strong> <a href="tel:{{ $contactPhone }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">{{ $contactPhone }}</a>
            @endif
        </p>
    </div>

    <p style="margin-top: 30px;">
        Looking forward to speaking with you!<br>
        <strong>{{ $staffName ?? 'The Team' }}</strong>
    </p>
@endsection
