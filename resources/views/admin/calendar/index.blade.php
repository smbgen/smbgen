@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Google Calendar</h1>

        @if(session('status'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded text-green-800 shadow">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded text-red-800 shadow">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-medium">Connection Failed</p>
                        <p class="mt-1 text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="p-6 bg-white shadow rounded text-gray-900">
            @php
                $isConnected = $user->googleCredential && $user->googleCredential->refresh_token;
            @endphp
            
            @if($isConnected)
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-green-700">Google Calendar Connected</p>
                        <p class="text-sm text-gray-600">
                            {{ $user->googleCredential->external_account_email ?? $user->googleCredential->calendar_id }}
                        </p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="text-sm text-gray-600 mb-4">
                        Bookings will automatically create calendar events with Google Meet links in your calendar.
                    </p>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('admin.calendar.select') }}" class="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                            Change Calendar
                        </a>
                        <form method="POST" action="{{ route('admin.calendar.disconnect') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition" onclick="return confirm('Are you sure you want to disconnect Google Calendar?')">
                                Disconnect
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Connect Google Calendar</h3>
                    <p class="text-sm text-gray-600 mb-6 max-w-md mx-auto">
                        Automatically create calendar events with Google Meet links when clients book appointments.
                    </p>
                    <a href="{{ route('admin.calendar.redirect') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                        Connect Calendar
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
