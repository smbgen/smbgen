@props(['bookings'])

@if(config('business.features.booking') && $bookings->count() > 0)
<div id="bookings" class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
            <div class="bg-gradient-to-r from-blue-600 dark:from-blue-500 to-cyan-600 dark:to-cyan-500 rounded-xl p-2">
                <i class="fas fa-calendar-alt text-white"></i>
            </div>
            Recent Bookings
        </h2>
        @if(Route::has('booking.wizard'))
        <a href="{{ route('booking.wizard') }}" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-xl px-6 py-3 transition-colors font-medium">
            <i class="fas fa-plus mr-2"></i>New Booking
        </a>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-300 dark:border-gray-700">
                    <th class="text-left py-4 px-4 text-gray-600 dark:text-gray-400 font-semibold text-sm uppercase tracking-wider">Customer</th>
                    <th class="text-left py-4 px-4 text-gray-600 dark:text-gray-400 font-semibold text-sm uppercase tracking-wider">Date & Time</th>
                    <th class="text-left py-4 px-4 text-gray-600 dark:text-gray-400 font-semibold text-sm uppercase tracking-wider">Duration</th>
                    <th class="text-left py-4 px-4 text-gray-600 dark:text-gray-400 font-semibold text-sm uppercase tracking-wider">Status</th>
                    <th class="text-right py-4 px-4 text-gray-600 dark:text-gray-400 font-semibold text-sm uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-300 dark:divide-gray-700">
                @foreach($bookings as $booking)
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700/30 transition-colors cursor-pointer" 
                    onclick="window.location.href='{{ route('admin.bookings.show', $booking) }}'"
                    title="Click to view booking details">
                    <td class="py-4 px-4">
                        <div class="text-gray-900 dark:text-white font-medium">{{ $booking->customer_name }}</div>
                        <div class="text-gray-600 dark:text-gray-400 text-sm">{{ $booking->customer_email }}</div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</div>
                        <div class="text-gray-600 dark:text-gray-400 text-sm">{{ $booking->booking_time }}</div>
                    </td>
                    <td class="py-4 px-4">
                        <span class="text-gray-700 dark:text-gray-300">{{ $booking->duration }} min</span>
                    </td>
                    <td class="py-4 px-4">
                        @if($booking->status === 'confirmed')
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">Confirmed</span>
                        @elseif($booking->status === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 rounded-full text-sm font-medium">Pending</span>
                        @else
                            <span class="px-3 py-1 bg-gray-200 dark:bg-gray-500/20 text-gray-700 dark:text-gray-400 rounded-full text-sm font-medium">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-right" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                               class="bg-gray-600 dark:bg-gray-600 hover:bg-gray-700 dark:hover:bg-gray-700 text-white rounded-lg px-3 py-2 text-sm transition-colors" 
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(Route::has('admin.bookings.send-reminder'))
                            <form action="{{ route('admin.bookings.send-reminder', $booking) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-blue-600 dark:bg-blue-600 hover:bg-blue-700 dark:hover:bg-blue-700 text-white rounded-lg px-3 py-2 text-sm transition-colors" title="Send Reminder">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Delete this booking?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 dark:bg-red-600 hover:bg-red-700 dark:hover:bg-red-700 text-white rounded-lg px-3 py-2 text-sm transition-colors" title="Delete">
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
</div>
@endif
