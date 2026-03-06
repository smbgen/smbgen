@extends('layouts.email')

@section('content')
    @php($user = $invoice->user ?? null)
    
    <h2 style="margin-top: 0; color: #1e40af; font-size: 28px;">Invoice #{{ $invoice->id ?? 'N/A' }}</h2>
    
    <p style="font-size: 16px;">Hi {{ optional($user)->name ?? 'Valued Customer' }},</p>

    <p style="font-size: 16px;">Thank you for your business! Please find your invoice details below:</p>

    <!-- Invoice Summary -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; margin: 25px 0; color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="margin: 0; font-size: 14px; opacity: 0.9;">Amount Due</p>
                <p style="margin: 8px 0 0 0; font-size: 36px; font-weight: 700;">${{ number_format(($invoice->total_amount ?? 0) / 100, 2) }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; font-size: 14px; opacity: 0.9;">Invoice Date</p>
                <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600;">{{ $invoice->created_at->format('M d, Y') }}</p>
                @if($invoice->due_date)
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">Due: {{ $invoice->due_date->format('M d, Y') }}</p>
                @endif
            </div>
        </div>
    </div>

    @if($invoice->memo)
    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3b82f6;">
        <p style="margin: 0; color: #4b5563; font-style: italic;">{{ $invoice->memo }}</p>
    </div>
    @endif

    <!-- Invoice Items -->
    @if($invoice->items && $invoice->items->count() > 0)
    <div style="margin: 25px 0;">
        <h3 style="color: #1f2937; font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">Invoice Items</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px; text-align: left; color: #6b7280; font-weight: 600; font-size: 13px; text-transform: uppercase;">Description</th>
                    <th style="padding: 12px; text-align: center; color: #6b7280; font-weight: 600; font-size: 13px; text-transform: uppercase;">Qty</th>
                    <th style="padding: 12px; text-align: right; color: #6b7280; font-weight: 600; font-size: 13px; text-transform: uppercase;">Price</th>
                    <th style="padding: 12px; text-align: right; color: #6b7280; font-weight: 600; font-size: 13px; text-transform: uppercase;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 12px; color: #1f2937;">{{ $item->description }}</td>
                    <td style="padding: 12px; text-align: center; color: #6b7280;">{{ $item->quantity }}</td>
                    <td style="padding: 12px; text-align: right; color: #6b7280;">${{ number_format($item->unit_amount / 100, 2) }}</td>
                    <td style="padding: 12px; text-align: right; color: #1f2937; font-weight: 600;">${{ number_format($item->total_amount / 100, 2) }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f9fafb; font-weight: 700;">
                    <td colspan="3" style="padding: 15px; text-align: right; color: #1f2937; font-size: 16px;">Total:</td>
                    <td style="padding: 15px; text-align: right; color: #1f2937; font-size: 18px;">${{ number_format($invoice->total_amount / 100, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    @if(($invoice->status ?? 'pending') !== 'paid')
    <!-- Payment Button -->
    <div style="text-align: center; margin: 35px 0;">
        @if($invoice->hasStripePaymentUrl())
            <p style="font-size: 16px; margin-bottom: 20px; color: #4b5563;">Click the button below to pay securely online:</p>
            <a href="{{ $invoice->stripe_payment_url }}" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 16px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 700; font-size: 18px; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.4); transition: all 0.3s;">
                💳 Pay ${{ number_format($invoice->total_amount / 100, 2) }} Now
            </a>
            <div style="margin-top: 15px;">
                <p style="font-size: 13px; color: #9ca3af; margin: 0;">
                    <svg style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                    </svg>
                    Secure payment powered by Stripe
                </p>
            </div>
        @else
            <a href="{{ route('billing.index') }}" style="background-color: #3b82f6; color: white; padding: 16px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 700; font-size: 18px;">
                View Invoice
            </a>
        @endif
    </div>

    <!-- Payment Instructions -->
    <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #92400e; font-size: 14px;">
            <strong>💡 Quick Payment:</strong> Click the payment button above to pay instantly with credit/debit card. No account required!
        </p>
    </div>
    @endif

    <div style="border-top: 2px solid #e5e7eb; margin-top: 30px; padding-top: 20px;">
        <p style="font-size: 15px; color: #4b5563;">If you have any questions about this invoice, please don't hesitate to contact us.</p>
        <p style="font-size: 15px; color: #1f2937; margin-top: 20px;">Thank you for your business!</p>
        <p style="font-size: 15px; color: #1f2937; margin-top: 5px;"><strong>The {{ config('business.company_name') ?? config('app.name') }} Team</strong></p>
    </div>
@endsection


