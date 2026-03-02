@extends('layouts.guest')

@section('title', 'Unauthorized')

@section('content')
<x-error-page
    code="401"
    title="Unauthorized"
    message="You do not have permission to access this page."
    icon="🔒"
    color="red"
    :suggestions="[
        'Verify you are logged in',
        'Check if you have the necessary permissions',
        'Contact support for assistance'
    ]"
/>
@endsection
