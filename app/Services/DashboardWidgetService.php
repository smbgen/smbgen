<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Client;
use App\Models\ClientFile;
use App\Models\CmsPage;
use App\Models\EmailLog;
use App\Models\Invoice;
use App\Models\LeadForm;
use App\Models\Message;

class DashboardWidgetService
{
    public function getWidgets(): array
    {
        return [
            'stats' => $this->getStatCards(),
            'quickActions' => $this->getQuickActions(),
            'recentActivity' => $this->getRecentActivity(),
            'systemTools' => $this->getSystemTools(),
            'businessMetrics' => $this->getBusinessMetrics(),
        ];
    }

    private function getStatCards(): array
    {
        $cards = [
            [
                'title' => 'Clients',
                'value' => Client::count(),
                'subtitle' => 'Total',
                'icon' => 'fa-users',
                'gradient' => 'from-blue-500 to-blue-700',
                'route' => 'clients.index',
                'linkText' => 'View all',
            ],
            [
                'title' => 'Leads',
                'value' => LeadForm::count(),
                'subtitle' => 'New',
                'icon' => 'fa-inbox',
                'gradient' => 'from-purple-500 to-purple-700',
                'route' => 'admin.leads.index',
                'linkText' => 'View all',
            ],
        ];

        if (config('business.features.booking')) {
            $cards[] = [
                'title' => 'Bookings',
                'value' => Booking::where('booking_date', '>=', now())->count(),
                'subtitle' => 'Upcoming',
                'icon' => 'fa-calendar-check',
                'gradient' => 'from-green-500 to-green-700',
                'route' => null,
                'anchor' => '#bookings',
                'linkText' => 'View all',
            ];
        }

        if (config('business.features.cms')) {
            $cards[] = [
                'title' => 'CMS Pages',
                'value' => CmsPage::where('is_published', true)->count(),
                'subtitle' => 'Live',
                'icon' => 'fa-edit',
                'gradient' => 'from-orange-500 to-orange-700',
                'route' => 'admin.cms.index',
                'linkText' => 'Manage',
            ];
        }

        if (config('business.features.file_management') && \Schema::hasColumn('client_files', 'file_size')) {
            try {
                $fileCount = ClientFile::count();
                $totalSize = ClientFile::sum('file_size') ?? 0;
            } catch (\Exception $e) {
                $fileCount = 0;
                $totalSize = 0;
            }

            $cards[] = [
                'title' => 'Files',
                'value' => $fileCount,
                'subtitle' => $totalSize > 0 ? $this->formatBytes($totalSize) : '0 B',
                'icon' => 'fa-folder',
                'gradient' => 'from-cyan-500 to-blue-700',
                'route' => 'admin.clients.files.overview',
                'linkText' => 'Manage',
            ];
        }

        if (\Route::has('admin.inspection-reports.index') && \Schema::hasTable('inspection_reports')) {
            try {
                $reportCount = \App\Models\InspectionReport::count();
            } catch (\Exception $e) {
                // If there is any DB error, default to 0 to avoid breaking the dashboard
                $reportCount = 0;
            }

            $cards[] = [
                'title' => 'Inspection Reports',
                'value' => $reportCount,
                'subtitle' => 'Inspection',
                'icon' => 'fa-file-alt',
                'gradient' => 'from-indigo-500 to-indigo-700',
                'route' => 'admin.inspection-reports.index',
                'linkText' => 'View reports',
            ];
        }

        return $cards;
    }

    private function getQuickActions(): array
    {
        $actions = [
            [
                'title' => 'New Client',
                'description' => 'Create a new client record',
                'icon' => 'fa-user-plus',
                'gradient' => 'from-blue-600 to-blue-700',
                'route' => 'clients.create',
            ],
            [
                'title' => 'View Leads',
                'description' => 'Manage lead submissions',
                'icon' => 'fa-inbox',
                'gradient' => 'from-purple-600 to-purple-700',
                'route' => 'admin.leads.index',
            ],
        ];

        if (config('business.features.cms')) {
            $actions[] = [
                'title' => 'New Page',
                'description' => 'Create CMS page',
                'icon' => 'fa-plus-circle',
                'gradient' => 'from-green-600 to-green-700',
                'route' => 'admin.cms.create',
            ];
        }

        $actions[] = [
            'title' => 'Messages',
            'description' => 'View conversations',
            'icon' => 'fa-comments',
            'gradient' => 'from-pink-600 to-pink-700',
            'route' => 'messages.index',
        ];

        return $actions;
    }

    private function getRecentActivity(): array
    {
        return [
            'leads' => LeadForm::latest()->take(5)->get(),
            'bookings' => config('business.features.booking')
                ? Booking::with('staff')->latest()->take(10)->get()
                : collect(),
        ];
    }

    private function getSystemTools(): array
    {
        $tools = [];

        if (config('business.features.email_composer') && \Route::has('admin.email.index')) {
            $tools[] = [
                'title' => 'Email Composer',
                'icon' => 'fa-envelope',
                'color' => 'cyan',
                'route' => 'admin.email.index',
            ];
        }

        if (\Route::has('admin.email-logs.index')) {
            $tools[] = [
                'title' => 'Email Logs',
                'icon' => 'fa-chart-line',
                'color' => 'blue',
                'route' => 'admin.email-logs.index',
            ];
        }

        if (config('business.features.billing') && \Route::has('admin.billing.index')) {
            $tools[] = [
                'title' => 'Billing',
                'icon' => 'fa-file-invoice-dollar',
                'color' => 'green',
                'route' => 'admin.billing.index',
            ];
        }

        if (auth()->user()->isAdministrator()) {
            $tools[] = [
                'title' => 'Settings',
                'icon' => 'fa-cog',
                'color' => 'yellow',
                'route' => 'admin.environment_settings.index',
            ];
        }

        if (\Route::has('admin.inspection-reports.index')) {
            $tools[] = [
                'title' => 'Reports',
                'icon' => 'fa-file-alt',
                'color' => 'indigo',
                'route' => 'admin.inspection-reports.index',
            ];
        }

        return $tools;
    }

    public function getTodayStats(): array
    {
        $stats = [
            [
                'label' => 'New Clients',
                'value' => Client::whereDate('created_at', today())->count(),
                'icon' => 'fa-user-plus',
                'color' => 'green',
            ],
            [
                'label' => 'New Leads',
                'value' => LeadForm::whereDate('created_at', today())->count(),
                'icon' => 'fa-inbox',
                'color' => 'purple',
            ],
        ];

        if (config('business.features.booking')) {
            $stats[] = [
                'label' => "Today's Bookings",
                'value' => Booking::whereDate('booking_date', today())->count(),
                'icon' => 'fa-calendar',
                'color' => 'blue',
            ];
        }

        return $stats;
    }

    public function getQuickLinks(): array
    {
        $links = [];

        // Only add extra links beyond the standard ones in management-links widget
        if (\Route::has('profile.edit')) {
            $links[] = ['title' => 'My Profile', 'route' => 'profile.edit'];
        }

        return $links;
    }

    public function getCmsManagementData(): array
    {
        if (! config('business.features.cms')) {
            return [
                'enabled' => false,
            ];
        }

        return [
            'enabled' => true,
            'formSubmissionsCount' => LeadForm::whereNotNull('cms_page_id')
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->count(),
            'pagesCount' => CmsPage::where('is_published', true)->count(),
        ];
    }

    public function getBusinessMetrics(): array
    {
        $metrics = [];

        // Files & Documents
        if (\Route::has('admin.clients.files.overview')) {
            $metrics[] = [
                'label' => 'Client Files',
                'value' => ClientFile::count(),
                'icon' => 'fa-file-alt',
                'color' => 'indigo',
                'route' => 'admin.clients.files.overview',
                'change' => ClientFile::whereDate('created_at', '>=', now()->subDays(7))->count(),
                'changeLabel' => 'this week',
            ];
        }

        // Messages
        if (\Route::has('messages.index')) {
            $unreadMessages = Message::where('is_read', false)->count();
            $metrics[] = [
                'label' => 'Unread Messages',
                'value' => $unreadMessages,
                'icon' => 'fa-envelope',
                'color' => 'red',
                'route' => 'messages.index',
                'highlight' => $unreadMessages > 0,
            ];
        }

        // Invoices
        if (config('business.features.billing') && \Route::has('admin.billing.index')) {
            $unpaidInvoices = Invoice::where('status', 'unpaid')->count();
            $metrics[] = [
                'label' => 'Unpaid Invoices',
                'value' => $unpaidInvoices,
                'icon' => 'fa-file-invoice-dollar',
                'color' => 'yellow',
                'route' => 'admin.billing.index',
                'highlight' => $unpaidInvoices > 0,
            ];
        }

        // Email Logs
        if (\Route::has('admin.email-logs.index')) {
            $recentEmails = EmailLog::whereDate('created_at', today())->count();
            $metrics[] = [
                'label' => 'Emails Sent Today',
                'value' => $recentEmails,
                'icon' => 'fa-paper-plane',
                'color' => 'cyan',
                'route' => 'admin.email-logs.index',
            ];
        }

        // CMS Form Submissions
        if (config('business.features.cms')) {
            $formSubmissions = LeadForm::whereNotNull('cms_page_id')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count();
            if ($formSubmissions > 0) {
                $metrics[] = [
                    'label' => 'Form Submissions',
                    'value' => $formSubmissions,
                    'icon' => 'fa-clipboard-list',
                    'color' => 'green',
                    'route' => 'admin.cms.index',
                    'changeLabel' => 'this week',
                ];
            }
        }

        return $metrics;
    }

    public function getRecentMessages(): \Illuminate\Support\Collection
    {
        if (! \Route::has('messages.index')) {
            return collect();
        }

        return Message::latest()
            ->take(5)
            ->get();
    }

    public function getPendingInvoices(): \Illuminate\Support\Collection
    {
        if (! config('business.features.billing') || ! \Route::has('admin.billing.index')) {
            return collect();
        }

        return Invoice::where('status', 'unpaid')
            ->latest()
            ->take(5)
            ->get();
    }

    public function getSystemHealth(): array
    {
        $health = [];

        // Check if Google Calendar is connected
        if (config('business.features.booking')) {
            // Check for valid Google Calendar connections in google_credentials table
            $googleConnected = \App\Models\User::whereHas('googleCredential', function ($q) {
                $q->whereNotNull('refresh_token');
            })->exists();

            // Check for expired tokens
            $hasExpiredTokens = \App\Models\GoogleCredential::whereNotNull('refresh_token')
                ->where('expires_at', '<', now()->addMinutes(5))
                ->exists();

            $status = $googleConnected ? ($hasExpiredTokens ? 'warning' : 'connected') : 'disconnected';
            $message = $hasExpiredTokens ? 'Token refresh needed' : null;

            $health[] = [
                'label' => 'Google Calendar',
                'status' => $status,
                'icon' => 'fa-google',
                'route' => 'admin.calendar.index',
                'message' => $message,
            ];
        }

        // Check recent email activity
        $recentEmailFails = EmailLog::where('status', 'failed')
            ->whereDate('created_at', '>=', now()->subDay())
            ->count();

        $health[] = [
            'label' => 'Email System',
            'status' => $recentEmailFails > 0 ? 'warning' : 'healthy',
            'icon' => 'fa-envelope-open-text',
            'route' => 'admin.email-logs.index',
            'message' => $recentEmailFails > 0 ? "$recentEmailFails failed in 24h" : 'All systems operational',
        ];

        // Check for pending bookings
        if (config('business.features.booking')) {
            $pendingBookings = Booking::where('status', 'pending')->count();
            $health[] = [
                'label' => 'Pending Bookings',
                'status' => $pendingBookings > 0 ? 'attention' : 'clear',
                'icon' => 'fa-calendar-check',
                'message' => $pendingBookings > 0 ? "$pendingBookings need review" : 'All bookings processed',
            ];
        }

        return $health;
    }

    public function getBookingManagerData(): array
    {
        if (! config('business.features.booking')) {
            return [
                'enabled' => false,
            ];
        }

        // Check if any user has Google Calendar connected via GoogleCredential with valid refresh token
        $googleConnected = \App\Models\User::whereHas('googleCredential', function ($q) {
            $q->whereNotNull('refresh_token');
        })->exists();

        // Check for expired or soon-to-expire tokens
        $hasExpiredTokens = \App\Models\GoogleCredential::whereNotNull('refresh_token')
            ->where('expires_at', '<', now()->addMinutes(5))
            ->exists();

        $now = now();

        // Get booking stats
        $pendingCount = Booking::where('status', 'pending')->count();
        $upcomingCount = Booking::where('booking_date', '>=', $now)
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();
        $thisWeekCount = Booking::whereBetween('booking_date', [
            $now->startOfWeek(),
            $now->copy()->endOfWeek(),
        ])->count();

        // Get recent activity
        $recentBookings = Booking::latest()->take(3)->get();
        $recentActivity = [];

        foreach ($recentBookings as $booking) {
            $recentActivity[] = [
                'message' => "New booking from {$booking->customer_name}",
                'time' => $booking->created_at->diffForHumans(),
            ];
        }

        return [
            'enabled' => true,
            'googleConnected' => $googleConnected,
            'hasExpiredTokens' => $hasExpiredTokens,
            'stats' => [
                'pending' => $pendingCount,
                'upcoming' => $upcomingCount,
                'thisWeek' => $thisWeekCount,
                'recentActivity' => $recentActivity,
            ],
        ];
    }

    public function getEmailAnalytics(): array
    {
        if (! \Route::has('admin.email-logs.index')) {
            return [
                'enabled' => false,
            ];
        }

        try {
            $sentToday = EmailLog::whereDate('created_at', today())->count();
            $sentThisWeek = EmailLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count();

            $failedToday = EmailLog::whereDate('created_at', today())
                ->whereIn('status', ['failed', 'bounced'])
                ->count();

            // Calculate delivery rate for last 24 hours
            $deliveryRate = EmailLog::getDeliveryRate(24);

            // Get recent emails
            $recentEmails = EmailLog::latest()
                ->take(5)
                ->get();

            return [
                'enabled' => true,
                'sentToday' => $sentToday,
                'sentThisWeek' => $sentThisWeek,
                'failedToday' => $failedToday,
                'deliveryRate' => $deliveryRate,
                'recentEmails' => $recentEmails,
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to get email analytics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'enabled' => false,
            ];
        }
    }

    public function getQuickBooksData(): array
    {
        if (! config('business.features.billing')) {
            return [
                'enabled' => false,
                'connected' => false,
                'companyInfo' => null,
                'hasConnectRoute' => false,
                'hasTestRoute' => false,
                'hasDisconnectRoute' => false,
            ];
        }

        try {
            $qbService = app(\App\Services\QuickBooksService::class);
            $qbConnected = $qbService->isConnected();
            $qbCompanyInfo = $qbConnected ? $qbService->getCompanyInfo() : null;

            return [
                'enabled' => true,
                'connected' => $qbConnected,
                'companyInfo' => $qbCompanyInfo,
                'hasConnectRoute' => \Route::has('admin.quickbooks.connect'),
                'hasTestRoute' => \Route::has('admin.quickbooks.test'),
                'hasDisconnectRoute' => \Route::has('admin.quickbooks.disconnect'),
            ];
        } catch (\Exception $e) {
            \Log::error('QuickBooks Dashboard Widget Error: '.$e->getMessage());

            return [
                'enabled' => false,
                'connected' => false,
                'companyInfo' => null,
                'hasConnectRoute' => false,
                'hasTestRoute' => false,
                'hasDisconnectRoute' => false,
            ];
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
