@extends('layouts.admin')

@section('title', 'Add Availability')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.availability.index', ['user_id' => $selectedUser->id]) }}" class="text-blue-400 hover:text-blue-300">
                ← Back to Availability
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                Add Availability for {{ $selectedUser->name }}
            </h1>

            <form action="{{ route('admin.availability.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}" />

                <div class="mb-4">
                    <label for="day_of_week" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Day of Week</label>
                    <select name="day_of_week" id="day_of_week" required class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a day...</option>
                        <option value="0" {{ old('day_of_week') == '0' ? 'selected' : '' }}>Sunday</option>
                        <option value="1" {{ old('day_of_week') == '1' ? 'selected' : '' }}>Monday</option>
                        <option value="2" {{ old('day_of_week') == '2' ? 'selected' : '' }}>Tuesday</option>
                        <option value="3" {{ old('day_of_week') == '3' ? 'selected' : '' }}>Wednesday</option>
                        <option value="4" {{ old('day_of_week') == '4' ? 'selected' : '' }}>Thursday</option>
                        <option value="5" {{ old('day_of_week') == '5' ? 'selected' : '' }}>Friday</option>
                        <option value="6" {{ old('day_of_week') == '6' ? 'selected' : '' }}>Saturday</option>
                    </select>
                    @error('day_of_week')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Time</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '09:00') }}" required class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Time</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '17:00') }}" required class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Appointment Duration (minutes)</label>
                        <select name="duration" id="duration" required class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="15" {{ old('duration') == '15' ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('duration', '30') == '30' ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ old('duration', '45') == '45' ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ old('duration') == '60' ? 'selected' : '' }}>1 hour</option>
                            <option value="90" {{ old('duration') == '90' ? 'selected' : '' }}>1.5 hours</option>
                            <option value="120" {{ old('duration') == '120' ? 'selected' : '' }}>2 hours</option>
                        </select>
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="break_period_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Break Period (minutes)</label>
                        <select name="break_period_minutes" id="break_period_minutes" required class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="0" {{ old('break_period_minutes', '0') == '0' ? 'selected' : '' }}>No break</option>
                            <option value="5" {{ old('break_period_minutes') == '5' ? 'selected' : '' }}>5 minutes</option>
                            <option value="10" {{ old('break_period_minutes') == '10' ? 'selected' : '' }}>10 minutes</option>
                            <option value="15" {{ old('break_period_minutes', '15') == '15' ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('break_period_minutes') == '30' ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ old('break_period_minutes') == '45' ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ old('break_period_minutes') == '60' ? 'selected' : '' }}>1 hour</option>
                        </select>
                        @error('break_period_minutes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Buffer time between appointments</p>
                    </div>
                </div>

                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label for="minimum_booking_notice_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Booking Notice</label>
                        <select name="minimum_booking_notice_hours" id="minimum_booking_notice_hours" required class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="1" {{ old('minimum_booking_notice_hours') == '1' ? 'selected' : '' }}>1 hour</option>
                            <option value="2" {{ old('minimum_booking_notice_hours') == '2' ? 'selected' : '' }}>2 hours</option>
                            <option value="4" {{ old('minimum_booking_notice_hours') == '4' ? 'selected' : '' }}>4 hours</option>
                            <option value="12" {{ old('minimum_booking_notice_hours') == '12' ? 'selected' : '' }}>12 hours</option>
                            <option value="24" {{ old('minimum_booking_notice_hours', '24') == '24' ? 'selected' : '' }}>24 hours (1 day)</option>
                            <option value="48" {{ old('minimum_booking_notice_hours') == '48' ? 'selected' : '' }}>48 hours (2 days)</option>
                            <option value="72" {{ old('minimum_booking_notice_hours') == '72' ? 'selected' : '' }}>72 hours (3 days)</option>
                        </select>
                        @error('minimum_booking_notice_hours')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="maximum_booking_days_ahead" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maximum Days Ahead</label>
                        <select name="maximum_booking_days_ahead" id="maximum_booking_days_ahead" required class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="7" {{ old('maximum_booking_days_ahead') == '7' ? 'selected' : '' }}>1 week</option>
                            <option value="14" {{ old('maximum_booking_days_ahead') == '14' ? 'selected' : '' }}>2 weeks</option>
                            <option value="21" {{ old('maximum_booking_days_ahead') == '21' ? 'selected' : '' }}>3 weeks</option>
                            <option value="28" {{ old('maximum_booking_days_ahead', '28') == '28' ? 'selected' : '' }}>4 weeks</option>
                            <option value="60" {{ old('maximum_booking_days_ahead') == '60' ? 'selected' : '' }}>2 months</option>
                            <option value="90" {{ old('maximum_booking_days_ahead') == '90' ? 'selected' : '' }}>3 months</option>
                        </select>
                        @error('maximum_booking_days_ahead')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                    <select name="timezone" id="timezone" required class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="America/New_York" {{ old('timezone', 'America/New_York') == 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                        <option value="America/Chicago" {{ old('timezone') == 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                        <option value="America/Denver" {{ old('timezone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time (MT)</option>
                        <option value="America/Los_Angeles" {{ old('timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                        <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                    </select>
                    @error('timezone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Your availability times will be stored in this timezone</p>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active (available for booking)</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.availability.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Add Availability
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-yellow-900/30 border border-yellow-700 rounded-lg p-4">
            <p class="text-sm text-yellow-200">
                <strong>Tip:</strong> You can create multiple availability blocks for the same day (e.g., morning and afternoon sessions).
            </p>
        </div>
    </div>
</div>
@endsection
