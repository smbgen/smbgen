@props(['bookingStats', 'googleConnected'])

<div id="bookings" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-calendar-alt"></i>
            Booking System
        </h3>
        <div class="flex items-center gap-2">
            @if($googleConnected)
                <span class="flex items-center gap-2 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-100 px-3 py-1 rounded-full text-xs font-medium border border-green-300 dark:border-green-400/30">
                    <span class="flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                    </span>
                    Google Calendar Connected
                </span>
            @else
                <span class="flex items-center gap-2 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-100 px-3 py-1 rounded-full text-xs font-medium border border-red-300 dark:border-red-400/30">
                    <i class="fas fa-exclamation-circle"></i>
                    Not Connected
                </span>
            @endif
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-50 dark:bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-gray-200 dark:border-white/20">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-clock text-yellow-500 dark:text-yellow-300 text-lg"></i>
                <span class="text-gray-600 dark:text-white/80 text-xs uppercase tracking-wider">Pending</span>
            </div>
            <div class="text-gray-900 dark:text-white text-3xl font-bold">{{ $bookingStats['pending'] }}</div>
        </div>

        <div class="bg-gray-50 dark:bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-gray-200 dark:border-white/20">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-check-circle text-green-500 dark:text-green-300 text-lg"></i>
                <span class="text-gray-600 dark:text-white/80 text-xs uppercase tracking-wider">Upcoming</span>
            </div>
            <div class="text-gray-900 dark:text-white text-3xl font-bold">{{ $bookingStats['upcoming'] }}</div>
        </div>

        <div class="bg-gray-50 dark:bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-gray-200 dark:border-white/20">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-calendar-day text-gray-500 dark:text-gray-300 text-lg"></i>
                <span class="text-gray-600 dark:text-white/80 text-xs uppercase tracking-wider">This Week</span>
            </div>
            <div class="text-gray-900 dark:text-white text-3xl font-bold">{{ $bookingStats['thisWeek'] }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Google Calendar Connection -->
        @if($googleConnected)
            <form action="{{ route('admin.calendar.disconnect') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full bg-gray-50 dark:bg-white/10 hover:bg-gray-100 dark:hover:bg-white/20 backdrop-blur-sm text-gray-900 dark:text-white font-medium py-3 px-4 rounded-xl transition-all border border-gray-200 dark:border-white/20 flex items-center justify-center gap-2">
                    <i class="fab fa-google"></i>
                    Disconnect Calendar
                </button>
            </form>
        @else
            <a href="{{ route('admin.calendar.redirect') }}" class="w-full bg-gray-700 dark:bg-gray-700 hover:bg-gray-600 dark:hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                <i class="fab fa-google"></i>
                Connect Google Calendar
            </a>
        @endif

        <!-- Form Settings -->
        <a href="{{ route('admin.booking-fields.edit') }}" class="w-full bg-gray-50 dark:bg-white/10 hover:bg-gray-100 dark:hover:bg-white/20 backdrop-blur-sm text-gray-900 dark:text-white font-medium py-3 px-4 rounded-xl transition-all border border-gray-200 dark:border-white/20 flex items-center justify-center gap-2">
            <i class="fas fa-sliders-h"></i>
            Form Settings
        </a>

        <!-- Availability Settings -->
        <a href="{{ route('admin.availability.index') }}" class="w-full bg-gray-50 dark:bg-white/10 hover:bg-gray-100 dark:hover:bg-white/20 backdrop-blur-sm text-gray-900 dark:text-white font-medium py-3 px-4 rounded-xl transition-all border border-gray-200 dark:border-white/20 flex items-center justify-center gap-2">
            <i class="fas fa-calendar-check"></i>
            Availability Settings
        </a>

        <!-- View All Bookings -->
        <a href="{{ route('admin.bookings.index') }}" class="w-full bg-gray-50 dark:bg-white/10 hover:bg-gray-100 dark:hover:bg-white/20 backdrop-blur-sm text-gray-900 dark:text-white font-medium py-3 px-4 rounded-xl transition-all border border-gray-200 dark:border-white/20 flex items-center justify-center gap-2">
            <i class="fas fa-list"></i>
            All Bookings
        </a>

        <!-- Booking Dashboard -->
        <a href="{{ route('admin.bookings.dashboard') }}" class="w-full bg-gray-50 dark:bg-white/10 hover:bg-gray-100 dark:hover:bg-white/20 backdrop-blur-sm text-gray-900 dark:text-white font-medium py-3 px-4 rounded-xl transition-all border border-gray-200 dark:border-white/20 flex items-center justify-center gap-2">
            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>

        <!-- Public Booking Page -->
        <a href="{{ route('booking.wizard') }}" target="_blank" class="w-full bg-gray-700 dark:bg-gray-700 hover:bg-gray-600 dark:hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-external-link-alt"></i>
            Public Booking Page
        </a>
    </div>

    <!-- Copy Booking URL -->
    <div class="mt-3 flex gap-2">
        <input type="text" readonly value="{{ url(route('booking.wizard')) }}" id="bookingUrl" class="flex-1 bg-gray-50 dark:bg-white/10 text-gray-900 dark:text-white text-sm px-3 py-2 rounded-lg border border-gray-200 dark:border-white/20 focus:outline-none focus:border-gray-300 dark:focus:border-white/40">
        <button onclick="copyBookingUrl()" class="bg-gray-50 dark:bg-white/10 hover:bg-gray-100 dark:hover:bg-white/20 text-gray-900 dark:text-white px-4 py-2 rounded-lg transition-all border border-gray-200 dark:border-white/20 flex items-center gap-2">
            <i class="fas fa-copy"></i>
            <span class="hidden sm:inline">Copy</span>
        </button>
    </div>

    <script>
    function copyBookingUrl() {
        const input = document.getElementById('bookingUrl');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            // Show toast notification
            const button = event.currentTarget;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> <span class="hidden sm:inline">Copied!</span>';
            button.classList.add('bg-green-500/30');
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-500/30');
            }, 2000);
        });
    }
    </script>

    <!-- Calendar Status Info -->
    @if($googleConnected)
        <div class="mt-4 p-3 bg-green-100 dark:bg-green-500/10 rounded-xl border border-green-300 dark:border-green-400/20">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-green-600 dark:text-green-300 mt-0.5"></i>
                <div class="text-green-700 dark:text-green-100 text-xs">
                    <strong>Calendar Sync Active:</strong> New bookings will automatically sync to your Google Calendar.
                    <a href="{{ route('admin.calendar.index') }}" class="underline hover:text-green-800 dark:hover:text-green-200">Manage calendar settings</a>
                </div>
            </div>
        </div>
    @else
        <div class="mt-4 p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-xl border border-yellow-300 dark:border-yellow-400/20">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-300 mt-0.5"></i>
                <div class="text-yellow-700 dark:text-yellow-100 text-xs">
                    <strong>Calendar Not Connected:</strong> Connect your Google Calendar to automatically sync bookings and prevent double-bookings.
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Activity -->
    @if(isset($bookingStats['recentActivity']) && count($bookingStats['recentActivity']) > 0)
    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-white/20">
        <h4 class="text-gray-900 dark:text-white font-semibold text-sm mb-3 flex items-center gap-2">
            <i class="fas fa-history"></i>
            Recent Activity
        </h4>
        <div class="space-y-2">
            @foreach($bookingStats['recentActivity'] as $activity)
            <div class="bg-gray-50 dark:bg-white/5 rounded-lg p-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 dark:text-white/90">{{ $activity['message'] }}</span>
                    <span class="text-gray-500 dark:text-white/60">{{ $activity['time'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @elseif($bookingStats['pending'] == 0 && $bookingStats['upcoming'] == 0)
    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-white/20">
        <div class="text-center py-6">
            <i class="fas fa-calendar-plus text-gray-300 dark:text-white/30 text-4xl mb-3"></i>
            <p class="text-gray-600 dark:text-white/60 text-sm">No bookings yet. Share your booking page to get started!</p>
        </div>
    </div>
    @endif
</div>
