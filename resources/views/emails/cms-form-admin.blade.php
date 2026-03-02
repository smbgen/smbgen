@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; color: #1e40af;">🔔 New Form Submission</h2>
    <p style="color: #6b7280; font-size: 16px;">{{ $page->title ?? 'Form Submission' }}</p>

    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 4px; margin: 20px 0;">
        <strong>📧 New Lead Alert!</strong><br>
        You've received a new form submission from your website.
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563eb;">
        <h3 style="margin-top: 0; color: #1e40af;">Contact Information</h3>
        
        @if($lead->name)
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">👤 Name:</span>
            <span style="color: #333;">{{ $lead->name }}</span>
        </div>
        @endif

        @if($lead->email)
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">📧 Email:</span>
            <span style="color: #333;"><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></span>
        </div>
        @endif

        @if(isset($formData['phone']))
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">📞 Phone:</span>
            <span style="color: #333;"><a href="tel:{{ $formData['phone'] }}">{{ $formData['phone'] }}</a></span>
        </div>
        @endif

        @if($lead->message)
        <div style="margin: 15px 0; padding: 10px 0;">
            <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">💬 Message:</span>
            <span style="color: #333;">{{ $lead->message }}</span>
        </div>
        @endif
    </div>

    @if(count($formData) > 0)
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563eb;">
        <h3 style="margin-top: 0; color: #1e40af;">Additional Information</h3>
        
        @foreach($formData as $key => $value)
            @if(!in_array($key, ['name', 'email', 'phone', 'message']))
            <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                <span style="color: #333;">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563eb;">
        <h3 style="margin-top: 0; color: #1e40af;">Submission Details</h3>
        
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">📅 Submitted:</span>
            <span style="color: #333;">{{ optional($lead->created_at)->format('F j, Y g:i A') ?? 'N/A' }}</span>
        </div>

        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">🌐 Page:</span>
            <span style="color: #333;">{{ $page->title ?? 'N/A' }} ({{ $page->slug ?? '' }})</span>
        </div>

        @if($lead->ip_address)
        <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">🖥️ IP Address:</span>
            <span style="color: #333;">{{ $lead->ip_address }}</span>
        </div>
        @endif

        @if($lead->referer)
        <div style="margin: 15px 0; padding: 10px 0;">
            <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">🔗 Referrer:</span>
            <span style="color: #333;">{{ $lead->referer }}</span>
        </div>
        @endif
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard" class="btn">
            View in Dashboard
        </a>
    </div>

    <p style="margin-top: 30px; color: #6b7280; font-size: 13px; text-align: center;">
        This is an automated notification from your CMS form builder.
    </p>
@endsection
