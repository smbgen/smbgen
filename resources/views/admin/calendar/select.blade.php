@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('admin.calendar.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Calendar Settings
            </a>
        </div>

        <h1 class="text-2xl font-bold mb-4">Select Calendar</h1>

        <div class="p-6 bg-white shadow rounded text-gray-900">
            <p class="text-sm text-gray-600 mb-4">
                Choose which Google Calendar should receive booking events. This is typically your primary calendar, but you can create a separate "Bookings" calendar if you prefer.
            </p>

            <form method="POST" action="{{ route('admin.calendar.update') }}">
                @csrf
                
                <div class="space-y-3 mb-6">
                    @foreach($calendars as $calendar)
                        <label class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer {{ $calendar['id'] === $currentCalendarId ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                            <input 
                                type="radio" 
                                name="calendar_id" 
                                value="{{ $calendar['id'] }}" 
                                class="mr-3"
                                {{ $calendar['id'] === $currentCalendarId ? 'checked' : '' }}
                            >
                            <div class="flex-1">
                                <div class="font-medium">
                                    {{ $calendar['summary'] }}
                                    @if($calendar['primary'])
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">Primary</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">{{ $calendar['id'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>

                @if(count($calendars) === 0)
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded text-yellow-800 mb-4">
                        <p class="font-medium">No calendars found</p>
                        <p class="text-sm mt-1">Make sure you have at least one calendar in your Google account.</p>
                    </div>
                @endif

                <div class="flex gap-3">
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        {{ count($calendars) === 0 ? 'disabled' : '' }}
                    >
                        Save Selection
                    </button>
                    <a href="{{ route('admin.calendar.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded text-blue-800">
            <p class="font-medium mb-2">💡 Tip: Create a Separate "Bookings" Calendar</p>
            <p class="text-sm">
                You can create a dedicated calendar in Google Calendar called "smbgen Bookings" to keep your booking events separate from your personal calendar. 
                This makes it easier to share your booking calendar with team members or toggle visibility.
            </p>
        </div>
    </div>
@endsection
