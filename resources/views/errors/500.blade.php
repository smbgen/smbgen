@extends('layouts.guest')

@section('title', 'Server Error')

@section('content')
<x-error-page
    code="500"
    title="Server Error"
    message="Something went wrong on our end. We're working to fix it."
    icon="🔧"
    color="red"
    :debug="config('app.debug')"
    :exception="$exception ?? null"
    :suggestions="[
        'Try refreshing the page',
        'Go back and try again',
        'Contact support if the problem persists'
    ]"
/>
@endsection
