<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\LeadForm;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all');

        if (strlen($query) < 2) {
            return response()->json([
                'clients' => [],
                'bookings' => [],
                'leads' => [],
                'invoices' => [],
                'users' => [],
            ]);
        }

        $results = [];

        if ($type === 'all' || $type === 'clients') {
            $results['clients'] = $this->searchClients($query);
        } else {
            $results['clients'] = [];
        }

        if ($type === 'all' || $type === 'bookings') {
            $results['bookings'] = $this->searchBookings($query);
        } else {
            $results['bookings'] = [];
        }

        if ($type === 'all' || $type === 'leads') {
            $results['leads'] = $this->searchLeads($query);
        } else {
            $results['leads'] = [];
        }

        if ($type === 'all' || $type === 'invoices') {
            $results['invoices'] = $this->searchInvoices($query);
        } else {
            $results['invoices'] = [];
        }

        if ($type === 'all' || $type === 'users') {
            $results['users'] = $this->searchUsers($query);
        } else {
            $results['users'] = [];
        }

        return response()->json($results);
    }

    public function stats()
    {
        return response()->json([
            'clients' => Client::count(),
            'bookings' => Booking::count(),
            'leads' => LeadForm::count(),
            'invoices' => Invoice::count(),
            'users' => User::count(),
            'total' => Client::count() + Booking::count() + LeadForm::count() + Invoice::count() + User::count(),
        ]);
    }

    protected function searchClients(string $query): array
    {
        return Client::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone'])
            ->toArray();
    }

    protected function searchBookings(string $query): array
    {
        return Booking::query()
            ->where('customer_name', 'like', "%{$query}%")
            ->orWhere('customer_email', 'like', "%{$query}%")
            ->orWhere('customer_phone', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client_name' => $booking->customer_name ?? 'Unknown',
                    'booking_time' => $booking->starts_at->format('M j, Y g:i A'),
                    'status' => $booking->status ?? 'pending',
                ];
            })
            ->toArray();
    }

    protected function searchLeads(string $query): array
    {
        return LeadForm::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('message', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email', 'message'])
            ->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => null, // lead_forms doesn't have phone field
                ];
            })
            ->toArray();
    }

    protected function searchInvoices(string $query): array
    {
        return Invoice::query()
            ->with('user:id,name')
            ->where('memo', 'like', "%{$query}%")
            ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => '#'.$invoice->id,
                    'client_name' => $invoice->user?->name ?? 'Unknown',
                    'total' => number_format(($invoice->total_amount ?? 0) / 100, 2),
                    'status' => $invoice->status ?? 'draft',
                ];
            })
            ->toArray();
    }

    protected function searchUsers(string $query): array
    {
        return User::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email', 'role'])
            ->toArray();
    }
}
