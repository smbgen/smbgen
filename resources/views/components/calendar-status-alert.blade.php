@props(['user' => null])

@php
    $user = $user ?? auth()->user();
    $isConnected = $user->googleCredential && $user->googleCredential->refresh_token;
@endphp

@if(!$isConnected)
    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded text-yellow-800 shadow-sm">
        <div class="flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <p class="font-semibold">Google Calendar Not Connected For This User</p>
                <p class="mt-1 text-sm">
                    Connect your Google Calendar to automatically create calendar events and Google Meet links for bookings.
                </p>
                @if(Route::has('admin.calendar.index'))
                    <a href="{{ route('admin.calendar.index') }}" class="mt-3 inline-block px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded transition">
                        Connect Calendar
                    </a>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded text-green-800 shadow-sm">
        <div class="flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <p class="font-semibold">Google Calendar Connected</p>
                <p class="mt-1 text-sm">
                    Calendar: <strong>{{ $user->googleCredential->calendar_id ?? 'primary' }}</strong>
                    @if($user->googleCredential->external_account_email)
                        ({{ $user->googleCredential->external_account_email }})
                    @endif
                </p>
            </div>
        </div>
    </div>
@endif
