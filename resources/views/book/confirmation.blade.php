@extends('layouts.public')

@section('content')
@php
    $companyColors = \App\Models\CmsCompanyColors::getSettings();
@endphp
<div class="max-w-2xl mx-auto p-6">
    <div class="rounded-lg shadow-lg p-8 text-center" style="background-color: {{ $companyColors->body_background_color }}; border: 2px solid {{ $companyColors->accent_color }}40;">
        <div class="mb-6">
            <svg class="w-16 h-16 mx-auto mb-4" style="color: {{ $companyColors->accent_color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1 class="text-3xl font-bold mb-2" style="color: {{ $companyColors->text_color }};">Booking Confirmed!</h1>
        </div>

        @if($booking)
            <div class="rounded-lg p-6 mb-6 text-left" style="background-color: {{ $companyColors->body_background_color }}; border: 1px solid {{ $companyColors->text_color }}20;">
                <p class="mb-4" style="color: {{ $companyColors->text_color }};">
                    Thank you, <span class="font-semibold">{{ $booking->customer_name }}</span>!
                </p>
                <div class="space-y-2" style="color: {{ $companyColors->text_color }};">
                    <p class="flex items-center">
                        <svg class="w-5 h-5 mr-2" style="color: {{ $companyColors->primary_color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <strong>{{ $booking->starts_at->format('l, F j, Y \a\t g:i A') }}</strong>
                    </p>
                    <p class="text-sm ml-7" style="color: {{ $companyColors->text_color }}99;">Duration: {{ $booking->duration }} minutes</p>
                </div>
                @if($booking->google_meet_link)
                    <div class="mt-4 pt-4" style="border-top: 1px solid {{ $companyColors->text_color }}20;">
                        <a href="{{ $booking->google_meet_link }}" target="_blank" class="inline-flex items-center hover:opacity-80 transition-opacity" style="color: {{ $companyColors->primary_color }};">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Join Google Meet
                        </a>
                    </div>
                @endif
            </div>
            <p class="text-sm mb-6" style="color: {{ $companyColors->text_color }}99;">A confirmation email has been sent to {{ $booking->customer_email }}.</p>
        @else
            <p class="mb-6" style="color: {{ $companyColors->text_color }};">Your booking has been received. Please check your email for details.</p>
        @endif

        <div class="flex justify-center gap-4">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-lg transition-all hover:opacity-90 text-sm font-medium" style="background-color: {{ $companyColors->primary_color }}; color: #ffffff;">
                    Back to Dashboard
                </a>
            @else
                <a href="/" class="px-4 py-2 rounded-lg transition-all hover:opacity-90 text-sm font-medium" style="background-color: {{ $companyColors->primary_color }}; color: #ffffff;">
                    Back to Home Page
                </a>
            @endauth
        </div>
    </div>
</div>

@endsection
