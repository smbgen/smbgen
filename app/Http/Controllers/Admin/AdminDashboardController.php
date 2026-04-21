<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LeadForm;
use App\Models\LoginAttempt;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DashboardWidgetService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct(protected DashboardWidgetService $dashboardWidgetService) {}

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

        $widgets = $this->dashboardWidgetService->getWidgets();
        $bookingData = $this->dashboardWidgetService->getBookingManagerData();
        $emailData = $this->dashboardWidgetService->getEmailAnalytics();
        $cmsData = $this->dashboardWidgetService->getCmsManagementData();
        $quickLinks = $this->dashboardWidgetService->getQuickLinks();
        $authenticatedUser = auth()->user();
        $domainOnboarding = null;

        $connectedCalendarUser = null;

        if (
            config('app.tenancy_enabled', false)
            && $authenticatedUser
            && $authenticatedUser->role === User::ROLE_ADMINISTRATOR
            && ! empty($authenticatedUser->tenant_id)
        ) {
            $tenant = Tenant::query()->find((string) $authenticatedUser->tenant_id);

            if ($tenant) {
                $domainOnboarding = [
                    'tenantName' => $tenant->name,
                    'subdomain' => $tenant->subdomain,
                    'customDomain' => $tenant->custom_domain,
                    'status' => (string) ($tenant->getAttribute('custom_domain_status') ?? 'not_started'),
                    'completedAt' => $tenant->getAttribute('domain_onboarding_completed_at'),
                ];
            }
        }

        if ($bookingData['googleConnected']) {
            if ($authenticatedUser->googleCredential?->refresh_token) {
                $connectedCalendarUser = $authenticatedUser;
            } else {
                $connectedCalendarUser = User::whereHas('googleCredential', function ($query) {
                    $query->whereNotNull('refresh_token');
                })->with('googleCredential')->first();
            }
        }

        return view('admin.dashboard', compact(
            'clients',
            'leads',
            'invoices',
            'invoiceCount',
            'bookings',
            'bookingCount',
            'stripeBalance',
            'widgets',
            'bookingData',
            'emailData',
            'cmsData',
            'quickLinks',
            'domainOnboarding',
            'connectedCalendarUser',
        ));
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
