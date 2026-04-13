@extends('layouts.admin')

@section('content')
    <div class="py-6">
        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-title">Global Search</h1>
                <p class="admin-page-subtitle">Search clients, bookings, leads, invoices, and users from one place</p>
            </div>
        </div>

        <x-dashboard.search-widget />
    </div>
@endsection
