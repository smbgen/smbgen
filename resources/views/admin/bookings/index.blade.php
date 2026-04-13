@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">All Bookings</h1>
            <p class="admin-page-subtitle">View and manage all booking appointments</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.booking-fields.edit') }}" class="btn-secondary">
                <i class="fas fa-cog mr-2"></i>Form Settings
            </a>
            <a href="{{ route('admin.bookings.dashboard') }}" class="btn-secondary">
                <i class="fas fa-chart-line mr-2"></i>Dashboard
            </a>
            @if(Route::has('booking.wizard'))
            <a href="{{ route('booking.wizard') }}" target="_blank" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>New Booking
            </a>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($bookings->count() > 0)
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/40">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-700">
                    @foreach($bookings as $booking)
                        <tr class="cursor-pointer transition-colors hover:bg-blue-50 dark:hover:bg-gray-700" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->customer_name }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->customer_email }}</div>
                                @if($booking->customer_phone)
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->customer_phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-gray-900 dark:text-gray-100">{{ $booking->booking_date->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->starts_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-gray-900 dark:text-gray-100">{{ $booking->duration }} min</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($booking->staff)
                                    <div class="text-gray-900 dark:text-gray-100">{{ $booking->staff->name }}</div>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'confirmed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                    ];
                                    $statusClass = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-secondary text-xs">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @endif
    @else
        <div class="py-12 text-center">
            <div class="mb-6 text-gray-600 dark:text-gray-400">
                <i class="fas fa-calendar text-6xl mb-4"></i>
                <h4 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">No bookings found</h4>
                <p>Bookings will appear here once customers schedule appointments.</p>
            </div>
        </div>
    @endif
</div>
@endsection
