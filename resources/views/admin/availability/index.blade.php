@extends('layouts.admin')

@section('title', 'Availability Settings')

@section('content')
<div class="min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-600 rounded-2xl p-8 mb-8 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <i class="fas fa-calendar-alt"></i>
                    Booking Management
                </h1>
                <p class="text-blue-100 text-lg">Manage availability and calendar integrations</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.dashboard') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl px-6 py-3 transition-all font-medium border border-white/30">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('admin.bookings.dashboard') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl px-6 py-3 transition-all font-medium border border-white/30">
                    <i class="fas fa-list mr-2"></i>All Bookings
                </a>
                @if(Route::has('booking.wizard'))
                <a href="{{ route('booking.wizard') }}" target="_blank" class="bg-white hover:bg-white/90 text-blue-600 rounded-xl px-6 py-3 transition-all font-bold shadow-lg">
                    <i class="fas fa-external-link-alt mr-2"></i>Public Booking Page
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-500/20 border-2 border-green-500 text-green-800 dark:text-green-100 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Calendar Connection Status -->
    <x-calendar-status-alert :user="auth()->user()" />

<div class="container mx-auto px-4 py-6">
    <!-- DEBUG PANEL -->
    @if(config('app.debug'))
    <div class="bg-yellow-50 dark:bg-yellow-900/30 border-2 border-yellow-500 rounded-lg p-4 mb-6">
        <h3 class="text-yellow-700 dark:text-yellow-300 font-bold text-lg mb-3">🐛 DEBUG INFO</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-yellow-800 dark:text-yellow-200 font-semibold mb-2">Current User:</div>
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded p-3 space-y-1 font-mono text-xs">
                    <div class="text-gray-700 dark:text-gray-300">ID: <span class="text-gray-900 dark:text-white">{{ auth()->user()->id }}</span></div>
                    <div class="text-gray-700 dark:text-gray-300">Name: <span class="text-gray-900 dark:text-white">{{ auth()->user()->name }}</span></div>
                    <div class="text-gray-700 dark:text-gray-300">Email: <span class="text-gray-900 dark:text-white">{{ auth()->user()->email }}</span></div>
                    <div class="text-gray-700 dark:text-gray-300">Role: <span class="text-gray-900 dark:text-white">{{ auth()->user()->role }}</span></div>
                </div>
            </div>
            <div>
                <div class="text-yellow-800 dark:text-yellow-200 font-semibold mb-2">Google Calendar Status:</div>
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded p-3 space-y-1 font-mono text-xs">
                    <div class="text-gray-700 dark:text-gray-300">Connection: 
                        @if(auth()->user()->googleCredential)
                            <span class="text-green-400 font-bold">✓ CONNECTED</span>
                        @else
                            <span class="text-red-400 font-bold">✗ NOT CONNECTED</span>
                        @endif
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">Calendar ID: <span class="text-gray-900 dark:text-white">{{ auth()->user()->googleCredential->calendar_id ?? 'none' }}</span></div>
                    <div class="text-gray-700 dark:text-gray-300">Availabilities: <span class="text-gray-900 dark:text-white">{{ $availabilities->count() }}</span></div>
                </div>
            </div>
        </div>
        @if(!auth()->user()->googleCredential)
            <div class="mt-3 bg-red-50 dark:bg-red-900/30 border border-red-500 rounded p-3">
                <p class="text-red-700 dark:text-red-300 font-semibold">⚠️ Google Calendar NOT connected!</p>
                <p class="text-red-600 dark:text-red-200 text-sm mt-1">You won't appear in the booking form until you connect Google Calendar.</p>
                <a href="{{ route('admin.calendar.index') }}" class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    Connect Google Calendar
                </a>
            </div>
        @endif
        <div class="text-yellow-800 dark:text-yellow-200 text-xs">
            This debug panel is only visible when APP_DEBUG=true
        </div>
    </div>
    @endif
    <!-- END DEBUG PANEL -->

    <!-- Staff Member Selector -->
    @if(count($staffMembers) > 1)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 mb-6 border border-gray-200 dark:border-transparent">
        <form method="GET" action="{{ route('admin.availability.index') }}" class="flex items-center gap-4">
            <label class="text-gray-700 dark:text-gray-300 font-medium">Managing availability for:</label>
            <select name="user_id" onchange="this.form.submit()" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach($staffMembers as $staff)
                    <option value="{{ $staff->id }}" {{ $selectedUser->id == $staff->id ? 'selected' : '' }}>
                        {{ $staff->name }} ({{ $staff->email }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Booking Availability 
            @if(count($staffMembers) > 1)
                <span class="text-gray-500 dark:text-gray-400 text-lg">- {{ $selectedUser->name }}</span>
            @endif
        </h2>
        <a href="{{ route('admin.availability.create', ['user_id' => $selectedUser->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Add Availability
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        @if($availabilities->isEmpty())
            <div class="p-8 text-center text-gray-600 dark:text-gray-400">
                <p class="mb-4 text-lg">No availability set yet.</p>
                <p class="text-sm">Add your available days and times to allow clients to book appointments.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-400 uppercase tracking-wider">Day</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-400 uppercase tracking-wider">Start Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-400 uppercase tracking-wider">End Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-700">
                        @foreach($availabilities as $availability)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $availability->day_name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($availability->start_time)->format('g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($availability->end_time)->format('g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    {{ $availability->duration }} min
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($availability->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.availability.edit', $availability) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mr-3">Edit</a>
                                    <form action="{{ route('admin.availability.destroy', $availability) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300" onclick="return confirm('Are you sure you want to delete this availability?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Blackout Dates Section -->
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Blackout Dates</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-4">Block specific dates when you're unavailable for bookings (holidays, vacations, etc.)</p>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-500/20 border border-red-500 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Add Blackout Date Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Blackout Date</h3>
            
            <form method="POST" action="{{ route('admin.availability.blackout.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                        <input 
                            type="date" 
                            name="date" 
                            required
                            min="{{ date('Y-m-d') }}"
                            value="{{ old('date') }}"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason (optional)</label>
                        <input 
                            type="text" 
                            name="reason" 
                            value="{{ old('reason') }}"
                            placeholder="e.g., Holiday, Vacation, Conference"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Add Blackout Date
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Blackout Dates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Blackout Dates</h3>
            
            @if($blackoutDates->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">No blackout dates set. All days follow your availability schedule.</p>
            @else
                <div class="space-y-2">
                    @foreach($blackoutDates as $blackout)
                        <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="flex-1">
                                <div class="text-gray-900 dark:text-white font-semibold">
                                    {{ $blackout->date->format('l, F j, Y') }}
                                </div>
                                @if($blackout->reason)
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $blackout->reason }}
                                    </div>
                                @endif
                            </div>
                            
                            <form method="POST" action="{{ route('admin.availability.blackout.destroy', $blackout) }}" class="ml-4">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    onclick="return confirm('Remove this blackout date?')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors"
                                >
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-blue-50 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 rounded-lg p-4">
        <h3 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">How it works:</h3>
        <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
            <li>• Set your available days and time ranges</li>
            <li>• Duration determines the length of each booking slot</li>
            <li>• Break periods add buffer time between appointments</li>
            <li>• Slots will be generated within your start and end times</li>
            <li>• Toggle availability on/off without deleting</li>
            <li>• Blackout dates override your regular availability schedule</li>
        </ul>
    </div>
</div>
</div>
@endsection
