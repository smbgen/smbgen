<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\DashboardWidgetService;
use App\Services\GoogleCalendarService;

class BookingController extends Controller
{
    public function __construct(
        protected GoogleCalendarService $googleCalendarService,
        protected DashboardWidgetService $dashboardWidgetService,
    ) {}

    /**
     * Display a listing of bookings
     */
    public function index()
    {
        $bookings = Booking::query()
            ->with(['user', 'staff'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display a single booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'staff']);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Display the booking dashboard
     */
    public function dashboard()
    {
        $bookings = Booking::query()
            ->with(['staff'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(15);

        $bookingData = $this->dashboardWidgetService->getBookingManagerData();

        return view('admin.bookings.dashboard', compact('bookings', 'bookingData'));
    }

    /**
     * Send a reminder email for a booking
     */
    public function sendReminder(Booking $booking)
    {
        try {
            $meetLink = $booking->google_meet_link;
            $staffName = $booking->staff ? $booking->staff->name : (auth()->user()->name ?? 'our team');

            $emailBody = view('emails.booking-reminder', [
                'booking' => $booking,
                'meetLink' => $meetLink,
                'staffName' => $staffName,
            ])->render();

            // Send email (listeners automatically handle tracking)
            \Mail::html($emailBody, function ($message) use ($booking) {
                $message->to($booking->customer_email, $booking->customer_name)
                    ->subject("Reminder: Your Upcoming Appointment on {$booking->booking_date->format('M j, Y')}");
            });

            \Log::info('Booking reminder sent', [
                'booking_id' => $booking->id,
                'customer_email' => $booking->customer_email,
            ]);

            return redirect()->back()->with('success', "Reminder email sent to {$booking->customer_name} successfully!");

        } catch (\Exception $e) {
            \Log::error('Failed to send booking reminder', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to send reminder email. Please check the logs.');
        }
    }

    /**
     * Convert a booking customer to a client
     */
    public function convertToClient(Booking $booking)
    {
        try {
            // Check if client already exists
            $existingClient = \App\Models\Client::where('email', $booking->customer_email)->first();

            if ($existingClient) {
                return redirect()->route('clients.show', $existingClient)
                    ->with('info', "Client '{$existingClient->name}' already exists with this email!");
            }

            // Create new client from booking data
            $client = \App\Models\Client::create([
                'name' => $booking->customer_name,
                'email' => $booking->customer_email,
                'phone' => $booking->customer_phone,
                'property_address' => $booking->property_address,
                'notes' => $booking->notes,
                'source_site' => 'Booking Conversion',
                'is_active' => true,
            ]);

            \Log::info('Booking converted to client', [
                'booking_id' => $booking->id,
                'client_id' => $client->id,
                'customer_email' => $booking->customer_email,
            ]);

            return redirect()->route('clients.show', $client)
                ->with('success', "Client '{$client->name}' created successfully from booking!");

        } catch (\Exception $e) {
            \Log::error('Failed to convert booking to client', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to convert booking to client. Please try again.');
        }
    }

    /**
     * Delete a booking and its associated Google Calendar event
     */
    public function destroy(Booking $booking)
    {
        $bookingId = $booking->id;
        $customerEmail = $booking->customer_email;
        $customerName = $booking->customer_name;
        $staffEmail = $booking->staff?->email;
        $staffName = $booking->staff?->name;
        $hasCalendarEvent = ! empty($booking->google_calendar_event_id);
        $calendarDeleted = false;

        try {
            // Attempt to delete Google Calendar event if it exists
            if ($hasCalendarEvent && $booking->staff) {
                try {
                    \Log::info('Attempting to delete Google Calendar event', [
                        'booking_id' => $bookingId,
                        'event_id' => $booking->google_calendar_event_id,
                        'staff_id' => $booking->staff->id,
                    ]);

                    // Use the staff member's calendar
                    $this->googleCalendarService->deleteEvent(
                        $booking->google_calendar_event_id,
                        $booking->staff->id
                    );

                    $calendarDeleted = true;
                } catch (\Exception $e) {
                    // Log but don't fail if calendar deletion fails
                    \Log::warning('Could not delete Google Calendar event, continuing with booking deletion', [
                        'booking_id' => $bookingId,
                        'event_id' => $booking->google_calendar_event_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            } elseif ($hasCalendarEvent && ! $booking->staff) {
                \Log::warning('Booking has calendar event but no staff member assigned', [
                    'booking_id' => $bookingId,
                    'event_id' => $booking->google_calendar_event_id,
                ]);
            }

            // Send cancellation emails to all involved parties before deleting
            try {
                // Send email to customer
                if ($customerEmail) {
                    \Mail::to($customerEmail, $customerName)
                        ->send(new \App\Mail\BookingCancellation($booking, 'customer'));

                    \Log::info('Cancellation email sent to customer', [
                        'booking_id' => $bookingId,
                        'customer_email' => $customerEmail,
                    ]);
                }

                // Send email to staff member if assigned
                if ($staffEmail) {
                    \Mail::to($staffEmail, $staffName)
                        ->send(new \App\Mail\BookingCancellation($booking, 'staff'));

                    \Log::info('Cancellation email sent to staff', [
                        'booking_id' => $bookingId,
                        'staff_email' => $staffEmail,
                    ]);
                }

                // Optionally send to admin/business contact email if different from staff
                $adminEmail = config('business.contact.email');
                if ($adminEmail && $adminEmail !== $staffEmail) {
                    \Mail::to($adminEmail)
                        ->send(new \App\Mail\BookingCancellation($booking, 'staff'));

                    \Log::info('Cancellation email sent to admin', [
                        'booking_id' => $bookingId,
                        'admin_email' => $adminEmail,
                    ]);
                }
            } catch (\Exception $e) {
                // Log email failure but continue with deletion
                \Log::warning('Failed to send cancellation emails, continuing with booking deletion', [
                    'booking_id' => $bookingId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            // Always delete the booking record
            $booking->delete();

            \Log::info('Booking deleted successfully', [
                'booking_id' => $bookingId,
                'had_calendar_event' => $hasCalendarEvent,
                'calendar_deleted' => $calendarDeleted,
            ]);

            $message = 'Booking deleted successfully and cancellation emails sent to all parties.';
            if ($hasCalendarEvent && ! $calendarDeleted) {
                $message .= ' (Note: Calendar event may not have been removed - please check Google Calendar)';
            }

            return redirect()->route('admin.bookings.dashboard')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Critical error while deleting booking', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.bookings.dashboard')
                ->with('error', 'Failed to delete booking. Please check the logs or contact support.');
        }
    }
}
