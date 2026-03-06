<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\BlackoutDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        // Allow switching between staff members
        $selectedUserId = $request->input('user_id', Auth::id());

        // Get all company administrators for the dropdown
        $staffMembers = \App\Models\User::where('role', 'company_administrator')
            ->orderBy('name')
            ->get();

        // Get availabilities for selected user
        $availabilities = Availability::where('user_id', $selectedUserId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $blackoutDates = BlackoutDate::where('user_id', $selectedUserId)
            ->orderBy('date', 'asc')
            ->get();

        $selectedUser = \App\Models\User::find($selectedUserId);

        return view('admin.availability.index', compact('availabilities', 'blackoutDates', 'staffMembers', 'selectedUser'));
    }

    public function create(Request $request)
    {
        $selectedUserId = $request->input('user_id', Auth::id());
        $selectedUser = \App\Models\User::find($selectedUserId);

        return view('admin.availability.create', compact('selectedUser'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration' => 'required|integer|min:15|max:240',
            'break_period_minutes' => 'required|integer|min:0|max:120',
            'minimum_booking_notice_hours' => 'required|integer|min:1|max:168',
            'maximum_booking_days_ahead' => 'required|integer|min:1|max:365',
            'timezone' => 'required|string|timezone',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Availability::create($validated);

        return redirect()->route('admin.availability.index', ['user_id' => $validated['user_id']])
            ->with('success', 'Availability added successfully.');
    }

    public function edit(Availability $availability)
    {
        // Allow editing any staff member's availability
        return view('admin.availability.edit', compact('availability'));
    }

    public function update(Request $request, Availability $availability)
    {
        // Allow updating any staff member's availability
        $validated = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration' => 'required|integer|min:15|max:240',
            'break_period_minutes' => 'required|integer|min:0|max:120',
            'minimum_booking_notice_hours' => 'required|integer|min:1|max:168',
            'maximum_booking_days_ahead' => 'required|integer|min:1|max:365',
            'timezone' => 'required|string|timezone',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $availability->update($validated);

        return redirect()->route('admin.availability.index', ['user_id' => $availability->user_id])
            ->with('success', 'Availability updated successfully.');
    }

    public function destroy(Availability $availability)
    {
        // Allow deleting any staff member's availability
        $userId = $availability->user_id;
        $availability->delete();

        return redirect()->route('admin.availability.index', ['user_id' => $userId])
            ->with('success', 'Availability deleted successfully.');
    }

    public function storeBlackout(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            BlackoutDate::create([
                'date' => $data['date'],
                'reason' => $data['reason'] ?? null,
                'user_id' => $data['user_id'],
            ]);

            return back()->with('success', 'Blackout date added successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['date' => 'This date is already blocked.']);
        }
    }

    public function destroyBlackout(BlackoutDate $blackoutDate)
    {
        $blackoutDate->delete();

        return back()->with('success', 'Blackout date removed successfully.');
    }
}
