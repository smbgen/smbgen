@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; color: #1e40af;">🎯 New Lead Form Submission</h2>
    
    <p>You have received a new lead form submission with the following details:</p>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;">
        @foreach($formData as $key => $value)
            <div style="margin: 15px 0; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <span style="font-weight: bold; color: #1e40af; display: block; margin-bottom: 5px;">
                    {{ ucwords(str_replace('_', ' ', $key)) }}:
                </span>
                <span style="color: #333;">
                    @if(is_array($value))
                        {{ implode(', ', $value) }}
                    @else
                        {{ $value }}
                    @endif
                </span>
            </div>
        @endforeach
    </div>

    <p style="margin-top: 30px;">Please review this submission and follow up with the lead as appropriate.</p>

    <p>Thanks,<br/>The {{ config('app.name') }} Team</p>
@endsection