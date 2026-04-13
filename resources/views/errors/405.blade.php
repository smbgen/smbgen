@extends('layouts.guest')

@section('title', 'Method Not Allowed')

@section('content')
<x-error-page
    code="405"
    title="Method Not Allowed"
    message="The request method is not allowed for this resource."
    icon="🚫"
    color="red"
    :suggestions="[
        'Check if a form is submitted incorrectly',
        'Verify the API endpoint method',
        'Return to the previous page and try again'
    ]"
/>
@endsection
