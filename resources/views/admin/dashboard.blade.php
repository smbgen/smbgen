@extends('layouts.admin')

@section('content')
@php
    $widgetService = app(\App\Services\DashboardWidgetService::class);
    $widgets = $widgetService->getWidgets();
@endphp

<!-- Ultra-compact header with quick actions -->
<div class="mb-3">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-2">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-xs text-gray-600 dark:text-gray-400">Welcome, {{ auth()->user()->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 rounded-lg text-white text-xs font-medium transition-all">
                <i class="fas fa-user-plus text-[10px]"></i>
                New Client
            </a>
            <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 rounded-lg text-white text-xs font-medium transition-all">
                <i class="fas fa-inbox text-[10px]"></i>
                Leads
            </a>
        </div>
    </div>
    
    <!-- Flash Messages (ultra-compact) -->
    @foreach (['success', 'info', 'warning', 'error'] as $msg)
        @if(session($msg))
            <div class="mt-2 p-2 rounded-lg {{ $msg === 'error' ? 'bg-red-500/10 border border-red-500/30 text-red-300' : 
                ($msg === 'warning' ? 'bg-yellow-500/10 border border-yellow-500/30 text-yellow-300' : 
                ($msg === 'info' ? 'bg-blue-500/10 border border-blue-500/30 text-blue-300' : 
                'bg-green-500/10 border border-green-500/30 text-green-300')) }} text-xs">
                <div class="flex items-center gap-2">
                    <i class="fas fa-{{ $msg === 'error' ? 'exclamation-circle' : ($msg === 'warning' ? 'exclamation-triangle' : ($msg === 'info' ? 'info-circle' : 'check-circle')) }} text-sm"></i>
                    <span>{{ session($msg) }}</span>
                </div>
            </div>
        @endif
    @endforeach
</div>

<!-- Primary Activity - Leads & Bookings -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
    <div>
        @if(!empty($widgets['recentActivity']['leads']) && count($widgets['recentActivity']['leads']))
            <x-dashboard.recent-leads :leads="$widgets['recentActivity']['leads']" />
        @else
            <div class="bg-white dark:bg-gray-800/50 rounded-lg p-3 border border-gray-200 dark:border-gray-700/50">
                <h3 class="text-xs font-semibold text-gray-900 dark:text-white mb-1">Recent Leads</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400">No recent leads found.</p>
            </div>
        @endif
    </div>

    @if(config('business.features.booking'))
    <div>
        <x-dashboard.recent-bookings :bookings="$widgets['recentActivity']['bookings']" />
    </div>
    @endif
</div>

<!-- Additional Tools (Collapsed by default) -->
<details class="group bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700/50 mb-3">
    <summary class="cursor-pointer px-3 py-2 flex items-center justify-between text-xs font-semibold text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-t-lg">
        <span class="flex items-center gap-2">
            <i class="fas fa-ellipsis-h text-gray-500"></i>
            Additional Tools & Settings
        </span>
        <i class="fas fa-chevron-down text-xs text-gray-600 dark:text-gray-400 group-open:rotate-180 transition-transform"></i>
    </summary>
    <div class="px-3 py-3 border-t border-gray-200 dark:border-gray-700/50 space-y-3">
        
        <!-- Booking Manager -->
        @php
            $bookingData = $widgetService->getBookingManagerData();
            $connectedCalendarUser = null;
            if ($bookingData['googleConnected']) {
                if (auth()->user()->googleCredential?->refresh_token) {
                    $connectedCalendarUser = auth()->user();
                } else {
                    $connectedCalendarUser = \App\Models\User::whereHas('googleCredential', function ($q) {
                        $q->whereNotNull('refresh_token');
                    })->with('googleCredential')->first();
                }
            }
        @endphp
        @if($bookingData['enabled'])
        <div>
            @if(!$bookingData['googleConnected'] || $bookingData['hasExpiredTokens'])
                <x-calendar-status-alert :user="$connectedCalendarUser ?? auth()->user()" />
            @endif
            <x-dashboard.booking-manager 
                :bookingStats="$bookingData['stats']" 
                :googleConnected="$bookingData['googleConnected']" 
            />
        </div>
        @endif

        <!-- Quick Links Grid -->
        <div class="grid grid-cols-2 gap-2">
            @foreach($widgets['systemTools'] as $tool)
                <x-dashboard.system-tool :tool="$tool" />
            @endforeach
        </div>
        
        <x-dashboard.management-links :links="$widgetService->getQuickLinks()" />

        @php
            $emailData = $widgetService->getEmailAnalytics();
            $cmsData = $widgetService->getCmsManagementData();
        @endphp
        
        @if($emailData['enabled'])
            <x-dashboard.email-analytics :emailData="$emailData" />
        @endif

        @if(config('business.features.billing'))
            <x-dashboard.pending-invoices :invoices="$widgetService->getPendingInvoices()" />
        @endif

        @if($cmsData['enabled'])
            <x-dashboard.cms-management 
                :formSubmissionsCount="$cmsData['formSubmissionsCount']"
                :pagesCount="$cmsData['pagesCount']"
            />
        @endif

        <x-dashboard.user-administration />

        @if(config('app.debug'))
            <x-dashboard.debug-tools />
        @endif
    </div>
</details>

<script>
function updateDashboardDateTime() {
    const now = new Date();
    const timeEl = document.getElementById('dashboardTime');
    if (timeEl) {
        timeEl.textContent = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
}
document.addEventListener('DOMContentLoaded', function() {
    updateDashboardDateTime();
    setInterval(updateDashboardDateTime, 30000);
});
</script>
@endsection
