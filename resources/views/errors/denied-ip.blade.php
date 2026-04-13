@extends('layouts.guest')

@section('title', 'Access Denied')

@section('content')
<x-error-page
    code="403"
    title="IP Access Denied"
    message="Your IP address is not recognized."
    icon="🚫"
    color="red"
    :suggestions="[
        'Contact support to request access',
        'Include your IP address when contacting support: ' . ($ip ?? 'Unknown'),
        'Verify you are using an approved network connection'
    ]"
>
    <div class="bg-red-800/30 rounded-lg p-4 mb-6 text-center">
        <p class="text-sm font-medium mb-2">Detected IP Address:</p>
        <code class="bg-black bg-opacity-40 px-4 py-2 rounded text-lg">{{ $ip ?? 'Unknown' }}</code>
    </div>
    
    <div class="text-center mb-6">
        <a href="mailto:{{ config('business.contact.email', config('mail.admin_address')) }}" class="btn-primary">
            <i class="fas fa-envelope mr-2"></i>Request Access
        </a>
    </div>
</x-error-page>
@endsection
