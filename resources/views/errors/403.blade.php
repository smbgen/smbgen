@extends('layouts.guest')

@section('title', 'Forbidden')

@section('content')
<x-error-page
    code="403"
    title="Forbidden"
    message="You don't have permission to access this resource."
    icon="🚫"
    color="red"
    :suggestions="[
        'Verify you are logged in with the correct account',
        'Contact your administrator if you need access',
        'Return to the login page'
    ]"
/>
@endsection