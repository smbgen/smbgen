@extends('layouts.guest')

@section('title', 'Session Expired')

@section('content')
<x-error-page
    code="419"
    title="Session Expired"
    message="Your session has expired for security reasons. Please refresh and try again."
    icon="⏰"
    color="blue"
    :suggestions="[
        'Refresh your browser',
        'Clear your cookies and cache',
        'Log in again to start a fresh session'
    ]"
/>
@endsection
