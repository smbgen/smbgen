@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; color: #10b981;">✅ Thank You!</h2>
    <p style="color: #6b7280; font-size: 16px;">We've received your submission</p>

    <div style="background: #d1fae5; border-left: 4px solid #10b981; padding: 20px; border-radius: 4px; margin: 20px 0;">
        <strong>🎉 Submission Received Successfully!</strong><br>
        Thank you for contacting us. We've received your information and will be in touch with you shortly.
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;">
        <h3 style="margin-top: 0; color: #059669;">Your Submission Details</h3>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">
            Here's a copy of the information you submitted:
        </p>
        
        @if($lead->name)
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #059669; display: block; margin-bottom: 5px;">Name:</span>
            <span style="color: #333;">{{ $lead->name }}</span>
        </div>
        @endif

        @if($lead->email)
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #059669; display: block; margin-bottom: 5px;">Email:</span>
            <span style="color: #333;">{{ $lead->email }}</span>
        </div>
        @endif

        @if(isset($formData['phone']))
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #059669; display: block; margin-bottom: 5px;">Phone:</span>
            <span style="color: #333;">{{ $formData['phone'] }}</span>
        </div>
        @endif

        @if($lead->message)
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #059669; display: block; margin-bottom: 5px;">Message:</span>
            <span style="color: #333;">{{ $lead->message }}</span>
        </div>
        @endif

        @foreach($formData as $key => $value)
            @if(!in_array($key, ['name', 'email', 'phone', 'message']))
            <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <span style="font-weight: bold; color: #059669; display: block; margin-bottom: 5px;">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                <span style="color: #333;">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
            </div>
            @endif
        @endforeach

        <div style="margin: 15px 0; padding: 10px 0;">
            <span style="font-weight: bold; color: #059669; display: block; margin-bottom: 5px;">Submitted:</span>
            <span style="color: #333;">{{ optional($lead->created_at)->format('F j, Y g:i A') ?? 'N/A' }}</span>
        </div>
    </div>

    <div style="background: #eff6ff; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #1e40af;">📞 What Happens Next?</h3>
        <ul style="color: #4b5563; margin: 10px 0; padding-left: 20px;">
            <li>Our team will review your submission</li>
            <li>We'll get back to you as soon as possible</li>
            <li>Keep an eye on your email for our response</li>
        </ul>
    </div>

    @if($page->notification_email)
    <p style="margin-top: 30px; color: #6b7280; font-size: 13px;">
        Questions? Contact us at <a href="mailto:{{ $page->notification_email }}">{{ $page->notification_email }}</a>
    </p>
    @endif
@endsection
