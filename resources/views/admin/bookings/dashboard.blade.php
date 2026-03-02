@extends('layouts.admin')

@section('content')
@php
    $bookingData = $bookingData ?? app(\App\Services\DashboardWidgetService::class)->getBookingManagerData();
@endphp

<div class="min-h-screen">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Booking Management</h1>
            <p class="admin-page-subtitle">Manage bookings and calendar integrations</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">
                <i class="fas fa-list mr-2"></i>All Bookings
            </a>
            <a href="{{ route('admin.booking-fields.edit') }}" class="btn-secondary">
                <i class="fas fa-cog mr-2"></i>Form Settings
            </a>
            <a href="{{ route('admin.availability.index') }}" class="btn-secondary">
                <i class="fas fa-calendar-alt mr-2"></i>Manage Availability
            </a>
            @if(Route::has('booking.wizard'))
            <a href="{{ route('booking.wizard') }}" target="_blank" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>New Booking
            </a>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @foreach (['success', 'info', 'warning', 'error'] as $msg)
        @if(session($msg))
            <div class="mb-6 p-4 rounded-xl {{ $msg === 'error' ? 'bg-red-500/20 border-2 border-red-500 text-red-100' : 
                ($msg === 'warning' ? 'bg-yellow-500/20 border-2 border-yellow-500 text-yellow-100' : 
                ($msg === 'info' ? 'bg-gray-500/20 border-2 border-gray-500 text-gray-100' : 
                'bg-green-500/20 border-2 border-green-500 text-green-100')) }} backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-{{ $msg === 'error' ? 'exclamation-circle' : ($msg === 'warning' ? 'exclamation-triangle' : ($msg === 'info' ? 'info-circle' : 'check-circle')) }} text-xl"></i>
                    <span class="font-medium">{{ session($msg) }}</span>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Calendar Connection Status -->
    <x-calendar-status-alert :user="auth()->user()" />

    <!-- All Bookings Table -->
    @if(config('business.features.booking'))
    <div id="bookings" class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <div class="bg-gray-600 rounded-xl p-2">
                    <i class="fas fa-list text-white"></i>
                </div>
                All Bookings
            </h2>
            @if(Route::has('booking.wizard'))
            <a href="{{ route('booking.wizard') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>New Booking
            </a>
            @endif
        </div>

        @if($bookings->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-4 px-4 text-gray-400 font-semibold text-sm uppercase tracking-wider">Customer</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-semibold text-sm uppercase tracking-wider">Date & Time</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-semibold text-sm uppercase tracking-wider">Staff</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-semibold text-sm uppercase tracking-wider">Property</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-semibold text-sm uppercase tracking-wider">Calendar</th>
                        <th class="text-left py-4 px-4 text-gray-400 font-semibold text-sm uppercase tracking-wider">Status</th>
                        <th class="text-right py-4 px-4 text-gray-400 font-semibold text-sm uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($bookings as $booking)
                    <tr class="hover:bg-gray-700/30 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
                        <td class="py-4 px-4">
                            <div class="text-white font-medium">{{ $booking->customer_name }}</div>
                            <div class="text-gray-400 text-sm">{{ $booking->customer_email }}</div>
                            @if($booking->customer_phone)
                            <div class="text-gray-400 text-sm">
                                <i class="fas fa-phone text-xs mr-1"></i>{{ $booking->customer_phone }}
                            </div>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-white">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</div>
                            <div class="text-gray-400 text-sm">{{ $booking->booking_time }}</div>
                            <div class="text-gray-500 text-xs mt-1">{{ $booking->duration }} min</div>
                        </td>
                        <td class="py-4 px-4">
                            @if($booking->staff)
                                <div class="text-white text-sm font-medium">{{ $booking->staff->name }}</div>
                                <div class="text-gray-400 text-xs">{{ $booking->staff->email }}</div>
                            @else
                                <span class="text-gray-500 text-sm italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($booking->property_address)
                                <div class="text-gray-300 text-sm max-w-xs" title="{{ $booking->property_address }}">
                                    <i class="fas fa-map-marker-alt text-xs mr-1 text-gray-400"></i>
                                    <span class="line-clamp-2">{{ $booking->property_address }}</span>
                                </div>
                            @else
                                <span class="text-gray-500 text-sm italic">No address</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($booking->google_calendar_event_id)
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-calendar-check text-green-400 text-xs"></i>
                                        <span class="text-green-400 text-xs font-medium">Synced</span>
                                    </div>
                                    @if($booking->google_meet_link)
                                        <a href="{{ $booking->google_meet_link }}" target="_blank" class="flex items-center gap-1.5 text-gray-300 hover:text-gray-100 text-xs transition-colors">
                                            <i class="fas fa-video text-xs"></i>
                                            <span>Meet Link</span>
                                        </a>
                                    @else
                                        <span class="text-yellow-400 text-xs">No meet link</span>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-calendar-times text-gray-500 text-xs"></i>
                                    <span class="text-gray-500 text-xs">No calendar</span>
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($booking->status === 'confirmed')
                                <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-medium">Confirmed</span>
                            @elseif($booking->status === 'pending')
                                <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-sm font-medium">Pending</span>
                            @elseif($booking->status === 'cancelled')
                                <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-sm font-medium">Cancelled</span>
                            @else
                                <span class="px-3 py-1 bg-gray-500/20 text-gray-400 rounded-full text-sm font-medium">{{ ucfirst($booking->status) }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-2">
                                @php
                                    $clientExists = \App\Models\Client::where('email', $booking->customer_email)->exists();
                                @endphp
                                @if(!$clientExists && Route::has('admin.bookings.convert-to-client'))
                                <form action="{{ route('admin.bookings.convert-to-client', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-3 py-2 text-sm transition-colors" title="Convert to Client">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </form>
                                @elseif($clientExists)
                                <span class="text-green-400 text-xs px-2" title="Already a client">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                @endif
                                @if(Route::has('admin.bookings.send-reminder'))
                                <form action="{{ route('admin.bookings.send-reminder', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-gray-600 hover:bg-gray-500 text-white rounded-lg px-3 py-2 text-sm transition-colors" title="Send Reminder">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-2 text-sm transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="text-sm text-gray-400">
                Showing <span class="text-white font-semibold">{{ $bookings->firstItem() }}</span>
                -
                <span class="text-white font-semibold">{{ $bookings->lastItem() }}</span>
                of
                <span class="text-white font-semibold">{{ $bookings->total() }}</span>
                bookings
            </div>
            <div class="bg-gray-900/50 rounded-xl p-2 border border-gray-700">
                {{ $bookings->withQueryString()->links() }}
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-calendar-plus text-gray-600 text-6xl mb-4"></i>
            <p class="text-gray-400 text-lg mb-4">No bookings yet</p>
            <p class="text-gray-500 mb-6">Share your booking page with customers to start receiving bookings</p>
            @if(Route::has('booking.wizard'))
            <a href="{{ route('booking.wizard') }}" target="_blank" class="btn-primary">
                <i class="fas fa-external-link-alt mr-2"></i>Open Booking Page
            </a>
            @endif
        </div>
        @endif
    </div>
    @endif


    <!-- Booking Manager Widget -->
    @if($bookingData['enabled'])
    <div class="mb-8 mt-6">
        <x-dashboard.booking-manager 
            :bookingStats="$bookingData['stats']" 
            :googleConnected="$bookingData['googleConnected']" 
        />
    </div>
    @endif

    <!-- Google OAuth Troubleshooting -->
    <div class="mt-8 bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-700 shadow-xl overflow-hidden">
        <details class="group">
            <summary class="cursor-pointer p-6 flex items-center justify-between hover:bg-gray-700/30 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="bg-yellow-500/20 rounded-xl p-2">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Google Calendar Connection Issues?</h3>
                        <p class="text-gray-400 text-sm">Click to view troubleshooting steps</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-gray-400 transition-transform group-open:rotate-180"></i>
            </summary>
            
            <div class="px-6 pb-6 border-t border-gray-700">
                <div class="mt-4 space-y-4">
                    <!-- Common Error -->
                    <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4">
                        <p class="text-red-400 font-semibold mb-2">
                            <i class="fas fa-times-circle mr-2"></i>Error 400: redirect_uri_mismatch
                        </p>
                        <p class="text-gray-300 text-sm">
                            This means Google doesn't recognize your redirect URI. Follow the steps below to fix it.
                        </p>
                    </div>

                    <!-- Fix Steps -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <h4 class="text-white font-bold mb-3 flex items-center gap-2">
                            <i class="fas fa-wrench text-gray-400"></i>
                            Quick Fix Steps
                        </h4>
                        <ol class="space-y-3 text-gray-300">
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">1</span>
                                <div>
                                    <p class="font-medium text-white">Open Google Cloud Console</p>
                                    <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-gray-300 hover:text-gray-100 text-sm inline-flex items-center gap-1 mt-1">
                                        console.cloud.google.com/apis/credentials
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">2</span>
                                <div>
                                    <p class="font-medium text-white">Find your OAuth 2.0 Client</p>
                                    <p class="text-sm text-gray-400 mt-1">Click on "OAuth 2.0 Client IDs" and select your web client</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">3</span>
                                <div>
                                    <p class="font-medium text-white">Add Authorized Redirect URIs</p>
                                    <p class="text-sm text-gray-400 mt-1">Click "+ ADD URI" and add both of these:</p>
                                    <div class="mt-2 space-y-2">
                                        <div class="bg-gray-900/50 rounded-lg p-3 border border-gray-600">
                                            <code class="text-green-400 text-sm">https://{{ request()->getHost() }}/auth/google/callback</code>
                                            <button onclick="navigator.clipboard.writeText('https://{{ request()->getHost() }}/auth/google/callback')" class="ml-2 text-gray-400 hover:text-white" title="Copy to clipboard">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                        <div class="bg-gray-900/50 rounded-lg p-3 border border-gray-600">
                                            <code class="text-green-400 text-sm">https://{{ request()->getHost() }}/admin/calendar/callback</code>
                                            <button onclick="navigator.clipboard.writeText('https://{{ request()->getHost() }}/admin/calendar/callback')" class="ml-2 text-gray-400 hover:text-white" title="Copy to clipboard">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">4</span>
                                <div>
                                    <p class="font-medium text-white">Save Changes</p>
                                    <p class="text-sm text-gray-400 mt-1">Click "SAVE" at the bottom. Changes take effect immediately.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">5</span>
                                <div>
                                    <p class="font-medium text-white">Test the Connection</p>
                                    <p class="text-sm text-gray-400 mt-1">Return to this page and try connecting your calendar again.</p>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <!-- Additional Resources -->
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-700">
                        <a href="{{ asset('app/docs/GOOGLE_OAUTH_SETUP.md') }}" target="_blank" class="text-gray-300 hover:text-gray-100 text-sm inline-flex items-center gap-2">
                            <i class="fas fa-book"></i>
                            Full Documentation
                        </a>
                        <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-gray-300 hover:text-gray-100 text-sm inline-flex items-center gap-2">
                            <i class="fas fa-external-link-alt"></i>
                            Open Google Cloud Console
                        </a>
                    </div>
                </div>
            </div>
        </details>
    </div>

</div>
@endsection
