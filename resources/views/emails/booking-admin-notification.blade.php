@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; color: #1e40af;">📅 New Booking Received</h2>
    
    <div style="display: inline-block; background-color: #dbeafe; color: #1e40af; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-bottom: 20px;">
        🔔 New Appointment
    </div>

    <p>A new booking has been submitted and confirmed.</p>

    <div style="background-color: #f8fafc; border-left: 4px solid #3b82f6; padding: 24px; margin: 24px 0; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h3 style="margin-top: 0; color: #1e40af; font-size: 18px; margin-bottom: 20px;">👤 Customer Information</h3>
        
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 100px;">Name:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->customer_name ?? 'Not provided' }}</span>
        </div>
        
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 100px;">Email:</span>
            <span style="color: #1f2937; font-weight: 500;">
                @if($booking->customer_email)
                    <a href="mailto:{{ $booking->customer_email }}" style="color: #2563eb; text-decoration: none;">{{ $booking->customer_email }}</a>
                @else
                    Not provided
                @endif
            </span>
        </div>
        
        @if($booking->customer_phone)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 100px;">Phone:</span>
            <span style="color: #1f2937; font-weight: 500;">
                <a href="tel:{{ $booking->customer_phone }}" style="color: #2563eb; text-decoration: none;">{{ $booking->customer_phone }}</a>
            </span>
        </div>
        @endif
        
        @if($booking->property_address)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 120px; vertical-align: top;">Property:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->property_address }}</span>
        </div>
        @endif
    </div>

    <div style="background-color: #f8fafc; border-left: 4px solid #10b981; padding: 24px; margin: 24px 0; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h3 style="margin-top: 0; color: #059669; font-size: 18px; margin-bottom: 20px;">📅 Appointment Details</h3>
        
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
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->duration ?? 30 }} minutes</span>
        </div>

        @if($staffName)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 100px;">Assigned to:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $staffName }}</span>
        </div>
        @endif
        
        @if($booking->notes)
        <div style="margin: 14px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: 600; color: #4b5563; display: inline-block; width: 120px; vertical-align: top;">Notes:</span>
            <span style="color: #1f2937; font-weight: 500;">{{ $booking->notes }}</span>
        </div>
        @endif
    </div>

    @if($booking->custom_form_data && count($booking->custom_form_data) > 0)
    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 24px; margin: 24px 0; border-radius: 8px; border: 1px solid #fde68a;">
        <h3 style="margin-top: 0; color: #92400e; font-size: 18px; margin-bottom: 20px;">📋 Additional Form Responses</h3>
        
        @foreach($booking->custom_form_data as $fieldName => $fieldValue)
            @if($fieldValue)
            <div style="margin: 14px 0; padding: 10px 0; {{ !$loop->last ? 'border-bottom: 1px solid #fde68a;' : '' }}">
                <span style="font-weight: 600; color: #78350f; display: inline-block; width: 150px; vertical-align: top;">{{ ucwords(str_replace('_', ' ', $fieldName)) }}:</span>
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
    </div>
    @endif

    @if($meetLink)
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $meetLink }}" style="display: inline-block; background-color: #3b82f6; color: #ffffff !important; padding: 14px 36px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);">
            🎥 Join Google Meet
        </a>
    </div>
    @endif

    @php
        $adminUrl = route('admin.bookings.index');
    @endphp

    <div style="background-color: #f0f9ff; border-left: 4px solid #3b82f6; padding: 20px 24px; margin: 24px 0; border-radius: 8px; border: 1px solid #bfdbfe;">
        <p style="margin: 0; color: #1f2937; line-height: 1.7;">
            <strong style="color: #374151;">View in Admin:</strong> 
            <a href="{{ $adminUrl }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">Manage Bookings</a>
        </p>
    </div>

    <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
        This is an automated notification from the booking system.
    </p>
@endsection
