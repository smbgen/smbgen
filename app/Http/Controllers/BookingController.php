<?php

namespace App\Http\Controllers;

use App\Mail\BookingAdminNotification;
use App\Mail\BookingConfirmation;
use App\Models\Availability;
use App\Models\BlackoutDate;
use App\Models\Booking;
use App\Models\BookingFieldConfig;
use App\Models\LeadForm;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function showWizard()
    {
        // Get all administrators with availability AND active Google Calendar credentials
        $availableStaff = \App\Models\User::administrators()
            ->whereHas('googleCredential', function ($q) {
                $q->whereNotNull('refresh_token');
            })
            ->whereHas('availabilities', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['availabilities', 'googleCredential'])
            ->get();

        // Get field configuration
        $fieldConfig = BookingFieldConfig::getConfig();
        $formFields = $fieldConfig->getAllFields();

        return view('book.wizard', compact('availableStaff', 'formFields'));
    }

    public function availability(Request $request)
    {
        // Get selected staff member or default to first admin
        $staffId = $request->input('staff_id');

        if ($staffId) {
            $admin = \App\Models\User::administrators()
                ->where('id', $staffId)
                ->whereHas('googleCredential', function ($q) {
                    $q->whereNotNull('refresh_token');
                })
                ->with('googleCredential')
                ->first();
        } else {
            $admin = \App\Models\User::administrators()
                ->whereHas('googleCredential', function ($q) {
                    $q->whereNotNull('refresh_token');
                })
                ->with('googleCredential')
                ->first();
        }

        if (! $admin) {
            return response()->json(['weeks' => []]);
        }

        $availabilityRules = Availability::where('user_id', $admin->id)
            ->where('is_active', true)
            ->get();

        if ($availabilityRules->isEmpty()) {
            return response()->json(['weeks' => []]);
        }

        // Get user's timezone preference from request, default to first rule's timezone
        // Handle empty string from query parameter (?timezone=)
        $timezone = $request->input('timezone');
        $userTimezone = ! empty($timezone) ? $timezone : ($availabilityRules->first()->timezone ?? 'UTC');

        // Get the maximum days ahead from the first availability rule (they should all be the same per user)
        $maxDaysAhead = $availabilityRules->first()->maximum_booking_days_ahead ?? 28;
        $minNoticeHours = $availabilityRules->first()->minimum_booking_notice_hours ?? 24;

        // Calculate how many weeks to show based on maxDaysAhead
        $numWeeks = (int) ceil($maxDaysAhead / 7);

        // Generate slots
        $weeks = [];
        $now = Carbon::now($userTimezone);
        $startDate = Carbon::today($userTimezone);

        for ($weekNum = 0; $weekNum < $numWeeks; $weekNum++) {
            $weekStart = $startDate->copy()->addWeeks($weekNum)->startOfWeek(Carbon::SUNDAY);
            $weekData = [
                'weekLabel' => $weekStart->format('M j').' - '.$weekStart->copy()->endOfWeek(Carbon::SATURDAY)->format('M j, Y'),
                'days' => [],
            ];

            // Generate 7 days for this week (Sunday to Saturday)
            for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                $currentDate = $weekStart->copy()->addDays($dayOffset);

                // Skip past dates (before today)
                if ($currentDate->lessThan($startDate)) {
                    continue;
                }

                // Skip dates beyond max booking window
                if ($currentDate->diffInDays($startDate, false) > $maxDaysAhead) {
                    continue;
                }

                // Skip blackout dates
                if (BlackoutDate::whereDate('date', $currentDate->toDateString())->exists()) {
                    continue;
                }

                $dayOfWeek = (int) $currentDate->dayOfWeek;

                // Find availability rules for this day of week
                $dayRules = $availabilityRules->where('day_of_week', $dayOfWeek);

                $daySlots = [];
                foreach ($dayRules as $rule) {
                    // Parse times in the admin's timezone
                    $adminTimezone = $rule->timezone;
                    $slotTime = Carbon::parse($currentDate->toDateString().' '.$rule->start_time, $adminTimezone);
                    $endTime = Carbon::parse($currentDate->toDateString().' '.$rule->end_time, $adminTimezone);

                    while ($slotTime->lessThan($endTime)) {
                        // Convert to user's timezone for display
                        $slotTimeInUserTz = $slotTime->copy()->setTimezone($userTimezone);

                        // Check if slot is far enough in the future (minimum notice)
                        // Use a cleaner approach: check if slot is less than (now + minimum hours)
                        $earliestAllowed = $now->copy()->addHours($minNoticeHours);
                        $isTooSoon = $slotTime->lessThan($earliestAllowed);

                        // Check if slot is already booked
                        $checkDate = $slotTime->toDateString();
                        $checkTime = $slotTime->format('H:i:s');

                        // SQLite stores dates as datetime strings, so we need to use whereDate
                        $isBooked = Booking::whereDate('booking_date', $checkDate)
                            ->where('booking_time', $checkTime)
                            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PENDING])
                            ->exists();

                        // Show all slots, but mark unavailable ones
                        $daySlots[] = [
                            'time' => $slotTimeInUserTz->format('g:i A'),
                            'value' => $slotTime->toAtomString(),
                            'available' => ! $isBooked && ! $isTooSoon,
                            'debug' => [ // Temporary
                                'checking_date' => $checkDate,
                                'checking_time' => $checkTime,
                                'is_booked' => $isBooked,
                                'is_too_soon' => $isTooSoon,
                            ],
                        ];

                        // Add appointment duration + break period for next slot
                        $slotTime->addMinutes($rule->duration + ($rule->break_period_minutes ?? 0));
                    }
                }

                if (! empty($daySlots)) {
                    $weekData['days'][] = [
                        'date' => $currentDate->toDateString(),
                        'dayName' => $currentDate->format('D'),
                        'dayNumber' => $currentDate->format('j'),
                        'slots' => $daySlots,
                    ];
                }
            }

            // Only add weeks that have at least one day with slots
            if (! empty($weekData['days'])) {
                $weeks[] = $weekData;
            }
        }

        return response()->json([
            'weeks' => $weeks,
            'timezone' => $userTimezone,
            'minNoticeHours' => $minNoticeHours,
            'maxDaysAhead' => $maxDaysAhead,
        ]);
    }

    public function book(Request $request)
    {
        try {
            // Get field configuration
            $fieldConfig = BookingFieldConfig::getConfig();
            $allFields = $fieldConfig->getAllFields();

            // Build validation rules dynamically from configuration
            $rules = [
                'slot' => 'required|date_format:Y-m-d\TH:i:sP',
                'staff_id' => 'nullable|exists:users,id',
            ];

            // Built-in and custom fields
            foreach ($allFields as $field) {
                $fieldName = $field['name'];
                $fieldRules = [];

                // Required or optional
                if ($field['required']) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                // Type-specific validation
                switch ($field['type']) {
                    case 'email':
                        $fieldRules[] = 'email';
                        $fieldRules[] = 'max:255';
                        break;
                    case 'tel':
                        $fieldRules[] = 'string';
                        $fieldRules[] = 'max:20';
                        break;
                    case 'textarea':
                        $fieldRules[] = 'string';
                        $fieldRules[] = $fieldName === 'notes' ? 'max:2000' : 'max:500';
                        break;
                    case 'number':
                        $fieldRules[] = 'numeric';
                        break;
                    case 'date':
                        $fieldRules[] = 'date';
                        break;
                    default:
                        $fieldRules[] = 'string';
                        $fieldRules[] = 'max:255';
                }

                $rules[$fieldName] = implode('|', $fieldRules);
            }

            $data = $request->validate($rules);

            $startsAt = Carbon::parse($data['slot']);
            $bookingDate = $startsAt->copy()->startOfDay();
            $bookingTime = $startsAt->format('H:i:s');

            \Log::info('[Booking] Processing new booking request', [
                'slot_input' => $data['slot'],
                'parsed_starts_at' => $startsAt->toIso8601String(),
                'booking_date' => $bookingDate->toIso8601String(),
                'booking_time' => $bookingTime,
                'staff_id' => $data['staff_id'] ?? null,
                'customer_name' => $data['name'] ?? null,
                'customer_email' => $data['email'] ?? null,
                'customer_phone' => $data['phone'] ?? null,
                'has_notes' => ! empty($data['notes']),
                'current_server_time' => now()->toIso8601String(),
                'app_timezone' => config('app.timezone'),
                'request_ip' => $request->ip(),
            ]);

            // Check if slot is already booked (prevent race condition)
            // Use whereDate for SQLite compatibility
            $alreadyBooked = Booking::whereDate('booking_date', $startsAt->toDateString())
                ->where('booking_time', $startsAt->format('H:i:s'))
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PENDING])
                ->exists();

            if ($alreadyBooked) {
                return back()->withErrors([
                    'slot' => 'This time slot has already been booked. Please select another time.',
                ])->withInput();
            }

            // Find the staff member's availability rule to get the correct duration
            $staffId = $data['staff_id'] ?? null;

            \Log::info('[Booking] Finding staff availability and duration', [
                'staff_id' => $staffId,
                'slot' => $startsAt->toIso8601String(),
            ]);
            if ($staffId) {
                $staffUser = \App\Models\User::find($staffId);
            } else {
                $staffUser = \App\Models\User::administrators()
                    ->whereHas('googleCredential')
                    ->first();
            }

            // Get the availability rule for this booking's day and time
            $bookingDuration = 30; // Default fallback
            if ($staffUser) {
                $dayOfWeek = $startsAt->dayOfWeek;
                $availabilityRule = Availability::where('user_id', $staffUser->id)
                    ->where('is_active', true)
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                if ($availabilityRule) {
                    // Use the full meeting duration for the booking
                    // Break period is time AFTER the meeting, not subtracted from it
                    $bookingDuration = $availabilityRule->duration;
                }
            }

            // Separate built-in fields from custom fields
            $builtInFields = ['name', 'email', 'phone', 'property_address', 'notes', 'slot', 'staff_id'];
            $customFormData = [];

            foreach ($data as $key => $value) {
                if (! in_array($key, $builtInFields) && ! empty($value)) {
                    $customFormData[$key] = $value;
                }
            }

            // Create booking record
            $booking = Booking::create([
                'customer_name' => $data['name'] ?? null,
                'customer_email' => $data['email'] ?? null,
                'customer_phone' => $data['phone'] ?? null,
                'booking_date' => $bookingDate->toDateString(),
                'booking_time' => $bookingTime,
                'notes' => $data['notes'] ?? null,
                'property_address' => $data['property_address'] ?? null,
                'status' => Booking::STATUS_PENDING,
                'staff_id' => $data['staff_id'] ?? null,
                'duration' => $bookingDuration,
                'custom_form_data' => ! empty($customFormData) ? $customFormData : null,
            ]);

            // Create a lead form entry if configured
            if (config('business.booking.create_lead', true)) {
                try {
                    $leadFormData = [];

                    // Add phone to form_data if provided
                    if (! empty($data['phone'])) {
                        $leadFormData['phone'] = $data['phone'];
                    }

                    // Add property address to form_data if provided
                    if (! empty($data['property_address'])) {
                        $leadFormData['property_address'] = $data['property_address'];
                    }

                    // Add custom form data
                    if (! empty($customFormData)) {
                        $leadFormData = array_merge($leadFormData, $customFormData);
                    }

                    // Add booking details to form_data
                    $leadFormData['booking_id'] = $booking->id;
                    $leadFormData['booking_date'] = $bookingDate->toDateString();
                    $leadFormData['booking_time'] = $startsAt->format('g:i A');
                    $leadFormData['source_type'] = 'booking';

                    LeadForm::create([
                        'name' => $data['name'] ?? 'Unknown',
                        'email' => $data['email'] ?? '',
                        'message' => $data['notes'] ?? 'Booking request for '.$startsAt->format('M j, Y \a\t g:i A'),
                        'source_site' => 'booking_system',
                        'notification_email' => config('business.contact.email'),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'referer' => $request->header('referer'),
                        'form_data' => $leadFormData,
                    ]);

                    \Log::info('Lead form created from booking', [
                        'booking_id' => $booking->id,
                        'customer_name' => $data['name'],
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create lead from booking', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Don't fail the booking if lead creation fails
                }
            }

            // Find the booked staff member who has Google Calendar connected
            $admin = null;
            if (! empty($data['staff_id'])) {
                $admin = \App\Models\User::where('id', $data['staff_id'])
                    ->whereHas('googleCredential', function ($q) {
                        $q->whereNotNull('refresh_token');
                    })
                    ->with('googleCredential')
                    ->first();
            }

            // Fallback to any admin with Google Calendar if booked staff doesn't have it
            if (! $admin) {
                $admin = \App\Models\User::administrators()
                    ->whereHas('googleCredential', function ($q) {
                        $q->whereNotNull('refresh_token');
                    })
                    ->with('googleCredential')
                    ->first();
            }

            if ($admin) {
                \Log::info('[Booking] Admin with Google Calendar found', [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
                    'admin_name' => $admin->name,
                    'has_google_credential' => $admin->googleCredential !== null,
                    'credential_id' => $admin->googleCredential->id ?? null,
                    'calendar_id' => $admin->googleCredential->calendar_id ?? 'primary',
                    'external_account_email' => $admin->googleCredential->external_account_email ?? null,
                    'credential_expires_at' => $admin->googleCredential->expires_at ?? null,
                    'credential_needs_refresh' => $admin->googleCredential ? $admin->googleCredential->needsRefresh() : null,
                ]);

                try {
                    // Create the Google Calendar service
                    $gc = app(GoogleCalendarService::class);

                    \Log::info('[Booking] Creating Google Calendar event for booking', [
                        'booking_id' => $booking->id,
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'admin_name' => $admin->name,
                        'calendar_id' => $admin->googleCredential->calendar_id ?? 'primary',
                        'calendar_email' => $admin->googleCredential->external_account_email ?? null,
                        'start_time' => $startsAt->toIso8601String(),
                        'duration_minutes' => $booking->duration,
                        'customer_name' => $booking->customer_name,
                        'customer_email' => $booking->customer_email,
                        'has_attendee_email' => ! empty($booking->customer_email),
                    ]);

                    \Log::info('[Booking] Calling GoogleCalendarService->createEventForUser', [
                        'booking_id' => $booking->id,
                        'starts_at' => $startsAt->toIso8601String(),
                        'duration' => $booking->duration,
                        'admin_id' => $admin->id,
                    ]);

                    $res = $gc->createEventForUser(
                        $admin,
                        $startsAt,
                        $booking->duration,
                        'Booking: '.$booking->customer_name,
                        $booking->notes,
                        $booking->customer_email
                    );

                    \Log::info('[Booking] Google Calendar service returned', [
                        'booking_id' => $booking->id,
                        'response_event_id' => $res['event_id'] ?? null,
                        'response_meet_link' => $res['meet_link'] ?? null,
                        'response_keys' => array_keys($res),
                    ]);

                    $booking->google_calendar_event_id = $res['event_id'] ?? null;
                    $booking->google_meet_link = $res['meet_link'] ?? null;
                    $booking->status = Booking::STATUS_CONFIRMED;
                    $booking->save();

                    \Log::info('[Booking] Google Calendar event created successfully', [
                        'booking_id' => $booking->id,
                        'event_id' => $booking->google_calendar_event_id,
                        'meet_link' => $booking->google_meet_link,
                        'has_meet_link' => ! empty($booking->google_meet_link),
                        'attendee_email' => $booking->customer_email,
                        'staff_id' => $admin->id,
                        'staff_name' => $admin->name,
                        'google_should_send_invite' => ! empty($booking->customer_email),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('[Booking] Failed to create Google Calendar event', [
                        'booking_id' => $booking->id,
                        'exception_class' => get_class($e),
                        'error_message' => $e->getMessage(),
                        'error_code' => $e->getCode(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'starts_at' => $startsAt->toIso8601String(),
                        'duration' => $booking->duration,
                        'customer_email' => $booking->customer_email,
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Booking remains pending but is still saved
                    $booking->status = Booking::STATUS_CONFIRMED; // Still confirm it even if calendar fails
                    $booking->save();
                }
            } else {
                \Log::warning('No admin with Google Calendar connected', [
                    'booking_id' => $booking->id,
                    'customer_email' => $booking->customer_email,
                    'start_time' => $startsAt->toIso8601String(),
                    'requested_staff_id' => $data['staff_id'] ?? null,
                    'fallback_attempted' => true,
                ]);
                // Still confirm the booking even without calendar
                $booking->status = Booking::STATUS_CONFIRMED;
                $booking->save();
            }

            // Send confirmation email to customer with appointment details and Meet link
            try {
                $staffName = $staffUser ? $staffUser->name : config('business.business_name', 'Our Team');

                $mailable = new BookingConfirmation(
                    booking: $booking,
                    meetLink: $booking->google_meet_link,
                    staffName: $staffName
                );

                // Use email tracking service for deliverability monitoring
                $trackingService = app(\App\Services\EmailTrackingService::class);
                $emailLog = $trackingService->createLog([
                    'user_id' => $staffUser->id ?? null,
                    'booking_id' => $booking->id,
                    'to_email' => $booking->customer_email,
                    'subject' => 'Appointment Confirmation',
                    'body' => $mailable->render(),
                ]);

                if ($emailLog) {
                    // Add tracking to email body
                    $trackedBody = $trackingService->addTrackingPixel($emailLog->body, $emailLog->tracking_id);
                    $trackedBody = $trackingService->addLinkTracking($trackedBody, $emailLog->tracking_id);

                    // Send email with tracking
                    Mail::to($booking->customer_email)
                        ->send($mailable);

                    $trackingService->markAsSent($emailLog->tracking_id);

                    \Log::info('Booking confirmation email sent with tracking', [
                        'booking_id' => $booking->id,
                        'customer_email' => $booking->customer_email,
                        'tracking_id' => $emailLog->tracking_id,
                        'has_meet_link' => ! empty($booking->google_meet_link),
                    ]);
                } else {
                    // Fallback: send without tracking
                    Mail::to($booking->customer_email)->send($mailable);

                    \Log::info('Booking confirmation email sent (no tracking)', [
                        'booking_id' => $booking->id,
                        'customer_email' => $booking->customer_email,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send booking confirmation email', [
                    'booking_id' => $booking->id,
                    'customer_email' => $booking->customer_email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Don't fail the booking if email fails
            }

            // Send notification to admins if enabled in booking settings
            try {
                $bookingConfig = \App\Models\BookingFieldConfig::getConfig();

                if ($bookingConfig->send_admin_notifications) {
                    $staffNameForAdmin = $staffUser ? $staffUser->name : config('business.business_name', 'Unassigned');

                    // Use custom admin notification email if set, otherwise notify admin users
                    if ($bookingConfig->admin_notification_email) {
                        Mail::to($bookingConfig->admin_notification_email)->send(new BookingAdminNotification(
                            booking: $booking,
                            meetLink: $booking->google_meet_link,
                            staffName: $staffNameForAdmin
                        ));

                        \Log::info('Booking admin notification sent to custom email', [
                            'booking_id' => $booking->id,
                            'admin_email' => $bookingConfig->admin_notification_email,
                        ]);
                    } else {
                        // Fallback to notifying admin users
                        $notifyAdmins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMINISTRATOR)
                            ->where('notify_on_new_bookings', true)
                            ->get();

                        foreach ($notifyAdmins as $admin) {
                            Mail::to($admin->email)->send(new BookingAdminNotification(
                                booking: $booking,
                                meetLink: $booking->google_meet_link,
                                staffName: $staffNameForAdmin
                            ));

                            \Log::info('Booking admin notification sent', [
                                'booking_id' => $booking->id,
                                'admin_email' => $admin->email,
                                'admin_name' => $admin->name,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send booking admin notification', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Don't fail the booking if admin notification fails
            }

            return redirect()->route('booking.confirmation')->with('booking_id', $booking->id);

        } catch (\Exception $e) {
            \Log::error('Booking form submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
            ]);

            // Return error to user
            return back()->with('error', 'Booking failed: '.$e->getMessage())->withInput();
        }
    }

    public function confirmation(Request $request)
    {
        $booking = null;
        if ($id = session('booking_id')) {
            $booking = Booking::find($id);
        }

        return view('book.confirmation', ['booking' => $booking]);
    }
}
