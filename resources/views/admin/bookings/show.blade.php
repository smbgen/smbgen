@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="text-purple-400 hover:text-purple-300">
            <i class="fas fa-arrow-left mr-2"></i>Back to All Bookings
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-900/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-900/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Booking Details</h2>
                @php
                    $statusColors = [
                        'confirmed' => 'bg-green-900/30 text-green-300',
                        'pending' => 'bg-yellow-900/30 text-yellow-300',
                        'cancelled' => 'bg-red-900/30 text-red-300',
                    ];
                    $statusClass = $statusColors[$booking->status] ?? 'bg-gray-700 text-gray-300';
                @endphp
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusClass }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
            <div class="text-right text-sm text-gray-600 dark:text-gray-400">
                <div>Created: {{ $booking->created_at->format('M j, Y g:i A') }}</div>
                @if($booking->updated_at != $booking->created_at)
                    <div>Updated: {{ $booking->updated_at->format('M j, Y g:i A') }}</div>
                @endif
            </div>
        </div>

        <!-- Customer Information -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                <i class="fas fa-user text-blue-400 mr-2"></i>
                Customer Information
            </h3>
            <div class="bg-gray-100 dark:bg-gray-900/50 rounded-lg p-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $booking->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Email:</span>
                    <a href="mailto:{{ $booking->customer_email }}" class="text-blue-400 hover:text-blue-300">
                        {{ $booking->customer_email }}
                    </a>
                </div>
                @if($booking->customer_phone)
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                        <a href="tel:{{ $booking->customer_phone }}" class="text-blue-400 hover:text-blue-300">
                            {{ $booking->customer_phone }}
                        </a>
                    </div>
                @endif
                @if($booking->property_address)
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Property Address:</span>
                        <span class="text-gray-900 dark:text-gray-100 text-right max-w-md">{{ $booking->property_address }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Appointment Details -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                <i class="fas fa-calendar-alt text-green-400 mr-2"></i>
                Appointment Details
            </h3>
            <div class="bg-gray-100 dark:bg-gray-900/50 rounded-lg p-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Date:</span>
                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $booking->booking_date->format('l, F j, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Time:</span>
                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $booking->starts_at->format('g:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Duration:</span>
                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $booking->duration }} minutes</span>
                </div>
                @if($booking->break_period_minutes)
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Break Period:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $booking->break_period_minutes }} minutes</span>
                    </div>
                @endif
                @if($booking->staff)
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Assigned Staff:</span>
                        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $booking->staff->name }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Google Calendar Integration -->
        @if($booking->google_calendar_event_id || $booking->google_meet_link)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-100 mb-3 flex items-center">
                    <i class="fab fa-google text-red-400 mr-2"></i>
                    Google Calendar
                </h3>
                <div class="bg-gray-900/50 rounded-lg p-4 space-y-2">
                    @if($booking->google_calendar_event_id)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Calendar Event ID:</span>
                            <span class="text-gray-100 font-mono text-sm">{{ $booking->google_calendar_event_id }}</span>
                        </div>
                    @endif
                    @if($booking->google_meet_link)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Meet Link:</span>
                            <a href="{{ $booking->google_meet_link }}" target="_blank" class="text-blue-400 hover:text-blue-300 flex items-center">
                                Join Meeting <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Notes -->
        @if($booking->notes)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-100 mb-3 flex items-center">
                    <i class="fas fa-sticky-note text-yellow-400 mr-2"></i>
                    Notes
                </h3>
                <div class="bg-gray-900/50 rounded-lg p-4">
                    <p class="text-gray-100 whitespace-pre-wrap">{{ $booking->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Custom Form Data -->
        @if($booking->custom_form_data && count($booking->custom_form_data) > 0)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-100 mb-3 flex items-center">
                    <i class="fas fa-list-ul text-purple-400 mr-2"></i>
                    Additional Form Responses
                </h3>
                <div class="bg-gray-900/50 rounded-lg p-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($booking->custom_form_data as $fieldName => $fieldValue)
                            @if($fieldValue)
                                <div>
                                    <dt class="text-sm font-medium text-gray-400 mb-1">
                                        {{ ucwords(str_replace('_', ' ', $fieldName)) }}
                                    </dt>
                                    <dd class="text-gray-100">
                                        @if(is_array($fieldValue))
                                            {{ implode(', ', $fieldValue) }}
                                        @else
                                            {{ $fieldValue }}
                                        @endif
                                    </dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="border-t border-gray-700 pt-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Actions</h3>
            <div class="flex flex-wrap gap-3">
                <!-- Send Reminder -->
                <form action="{{ route('admin.bookings.send-reminder', $booking) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-envelope mr-2"></i>Send Reminder
                    </button>
                </form>

                <!-- Convert to Client -->
                <form action="{{ route('admin.bookings.convert-to-client', $booking) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        <i class="fas fa-user-plus mr-2"></i>Convert to Client
                    </button>
                </form>

                <!-- Delete Booking -->
                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this booking? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash mr-2"></i>Delete Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
