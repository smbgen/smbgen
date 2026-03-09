<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingMcpController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with('staff:id,name,email');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->query('from')) {
            $query->whereDate('booking_date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->whereDate('booking_date', '<=', $to);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->limit($request->query('limit', 50))
            ->get();

        return response()->json([
            'count' => $bookings->count(),
            'bookings' => $bookings,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $booking = Booking::with(['staff:id,name,email', 'user:id,name,email'])->findOrFail($id);

        return response()->json(['booking' => $booking]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name'    => ['required', 'string', 'max:255'],
            'customer_email'   => ['required', 'email', 'max:255'],
            'customer_phone'   => ['nullable', 'string', 'max:50'],
            'booking_date'     => ['required', 'date'],
            'booking_time'     => ['required', 'date_format:H:i'],
            'duration'         => ['nullable', 'integer', 'min:15', 'max:480'],
            'property_address' => ['nullable', 'string', 'max:500'],
            'notes'            => ['nullable', 'string', 'max:5000'],
            'staff_id'         => ['nullable', 'exists:users,id'],
        ]);

        $booking = Booking::create($validated);

        return response()->json(['booking' => $booking->fresh(['staff'])], 201);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled'],
            'notes'  => ['nullable', 'string', 'max:5000'],
        ]);

        $booking->update($validated);

        return response()->json(['booking' => $booking->fresh()]);
    }
}
