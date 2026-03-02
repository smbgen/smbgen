@extends('layouts.guest')

@section('title', 'Service Unavailable')

@section('content')
<x-error-page
    code="503"
    title="Service Unavailable"
    message="The service is temporarily unavailable. Please try again later."
    icon="⚠️"
    color="purple"
    :suggestions="[
        'Wait a few minutes and try again',
        'We may be performing scheduled maintenance',
        'Contact support if this persists'
    ]"
>
    <div class="text-center mb-6">
        <button onclick="window.location.reload()" class="btn-primary">
            <i class="fas fa-redo mr-2"></i>Refresh Page
        </button>
    </div>
</x-error-page>
@endsection