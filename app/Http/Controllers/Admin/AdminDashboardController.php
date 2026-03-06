<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LeadForm;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        // Show the latest 5 clients for a dashboard summary
        $clients = Client::latest()->take(5)->get();
        $leads = LeadForm::latest()->paginate(10);

        // Recent invoices
        $invoices = \App\Models\Invoice::with('user')->latest()->take(10)->get();
        $invoiceCount = \App\Models\Invoice::count();

        // Recent bookings
        $bookings = \App\Models\Booking::with('staff')->latest()->take(10)->get();
        $bookingCount = \App\Models\Booking::count();

        // Stripe balance
        $stripeService = app(\App\Services\StripeService::class);
        $stripeBalance = $stripeService->testConnection();

        return view('admin.dashboard', compact('clients', 'leads', 'invoices', 'invoiceCount', 'bookings', 'bookingCount', 'stripeBalance'));
    }

    public function googleOAuth()
    {
        $users = User::with('googleCredential')->get();
        $recentLoginAttempts = LoginAttempt::with('user')->where('provider', 'google')->latest()->limit(15)->get();

        return view('admin.google-oauth', compact('users', 'recentLoginAttempts'));
    }

    public function sendTestEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw($validated['message'], function ($msg) use ($validated) {
                $msg->to($validated['email'])
                    ->subject($validated['subject']);
            });

            \Log::info('Test email sent successfully', [
                'to' => $validated['email'],
                'subject' => $validated['subject'],
                'from' => config('mail.from.address'),
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', '✅ Test email sent successfully to '.$validated['email'].'! Check your inbox (and spam folder).');

        } catch (\Exception $e) {
            \Log::error('Failed to send test email', [
                'to' => $validated['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.dashboard')
                ->with('error', '❌ Failed to send test email: '.$e->getMessage());
        }
    }
}
