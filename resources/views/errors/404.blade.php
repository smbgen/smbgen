@extends('layouts.guest')

@section('title', 'Page Not Found')

@section('content')
<x-error-page
    code="404"
    title="Page Not Found"
    message="The page you're looking for doesn't exist or has been moved."
    icon="🔍"
    color="yellow"
    :suggestions="[
        'Check the URL for typos',
        'Use the navigation menu to find what you need',
        'Go back to the previous page',
        'Visit our homepage'
    ]"
/>
@endsection
