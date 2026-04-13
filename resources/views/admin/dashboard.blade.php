@extends('layouts.admin')

@section('content')
@php
    $recentLeads = collect($widgets['recentActivity']['leads'] ?? [])->take(5);
    $recentBookings = collect($widgets['recentActivity']['bookings'] ?? [])->take(5);
    $recentPayments = $invoices->take(5);
@endphp

<div class="space-y-6">
    <section class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-blue-50/60 dark:from-slate-900/95 dark:via-slate-900/85 dark:to-gray-900/70 shadow-sm dark:shadow-black/30 p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="h-11 w-11 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 flex items-center justify-center shrink-0">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome, {{ auth()->user()->name }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Latest leads, bookings, and payments at a glance. Use the quick actions to jump into CRM and CMS work.</p>
                </div>
            </div>
            <!-- <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-400">
                    <i class="fas fa-inbox"></i>
                    Leads
                </a>
                @if(
                    \Route::has('admin.bookings.dashboard')
                )
                    <a href="{{ route('admin.bookings.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                        <i class="fas fa-calendar-check"></i>
                        Bookings
                    </a>
                @endif
                @if(
                    \Route::has('admin.billing.index')
                )
                    <a href="{{ route('admin.billing.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-400">
                        <i class="fas fa-credit-card"></i>
                        Payments
                    </a>
                @endif
            </div> -->
        </div>
    </section>

    @if($domainOnboarding)
        @php
            $statusLabels = [
                'not_started' => 'Not Started',
                'pending_dns' => 'Pending DNS',
                'verified' => 'Verified',
                'using_subdomain' => 'Using Platform Subdomain',
            ];

            $statusBadgeClasses = [
                'not_started' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                'pending_dns' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                'verified' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'using_subdomain' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            ];

            $status = $domainOnboarding['status'] ?? 'not_started';
        @endphp

        <section class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/70 p-5 shadow-sm dark:shadow-black/20">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Domain Setup Status</h2>
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusBadgeClasses[$status] ?? $statusBadgeClasses['not_started'] }}">
                            {{ $statusLabels[$status] ?? $statusLabels['not_started'] }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Workspace: <span class="font-mono text-xs">{{ $domainOnboarding['subdomain'] }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</span>
                        @if(!empty($domainOnboarding['customDomain']))
                            • Custom: <span class="font-mono text-xs">{{ $domainOnboarding['customDomain'] }}</span>
                        @endif
                    </p>
                </div>

                <a href="{{ route('admin.domain-onboarding.show') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    Manage Domain Setup
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </section>
    @endif

    <section>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Recent Activity</h2>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <article class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/70 p-4 shadow-sm dark:shadow-black/20">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Latest Leads</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $leads->total() }} total</span>
                </div>
                <div class="space-y-2 min-h-[140px]">
                    @forelse($recentLeads as $lead)
                        <a href="{{ route('admin.leads.show', $lead) }}" class="block rounded-lg border border-transparent bg-gray-50 px-3 py-2 transition-colors hover:bg-blue-50 dark:border-gray-800 dark:bg-gray-800/80 dark:hover:bg-gray-800">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lead->name ?: 'Unknown Lead' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $lead->email ?: 'No email' }} • {{ $lead->created_at?->diffForHumans() }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No recent leads yet.</p>
                    @endforelse
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        View all leads
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </article>

            <article class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/70 p-4 shadow-sm dark:shadow-black/20">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Latest Bookings</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $bookingCount }} total</span>
                </div>
                <div class="space-y-2 min-h-[140px]">
                    @if(\Route::has('admin.bookings.dashboard'))
                        @forelse($recentBookings as $booking)
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="block rounded-lg border border-transparent bg-gray-50 px-3 py-2 transition-colors hover:bg-indigo-50 dark:border-gray-800 dark:bg-gray-800/80 dark:hover:bg-gray-800">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $booking->customer_name ?? $booking->name ?? 'Booking' }}
                                    <span class="ml-2 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $booking->booking_date?->format('M j, Y') }} {{ $booking->starts_at?->format('g:i A') }}</span>
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $booking->email ?? $booking->customer_email ?? 'No email' }} • {{ $booking->created_at?->diffForHumans() }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent bookings yet.</p>
                        @endforelse
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Bookings are not enabled.</p>
                    @endif
                </div>
                <div class="mt-3">
                    @if(\Route::has('admin.bookings.dashboard'))
                        <a href="{{ route('admin.bookings.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                            View all bookings
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    @endif
                </div>
            </article>

            <article class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/70 p-4 shadow-sm dark:shadow-black/20">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Latest Payments</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $invoiceCount }} total</span>
                </div>
                <div class="space-y-2 min-h-[140px]">
                    @if(\Route::has('admin.billing.index'))
                        @forelse($recentPayments as $invoice)
                            @php
                                $paymentHref = \Route::has('admin.billing.show') && $invoice->user
                                    ? route('admin.billing.show', $invoice->user)
                                    : route('admin.billing.index');
                            @endphp
                            <a href="{{ $paymentHref }}" class="block rounded-lg border border-transparent bg-gray-50 px-3 py-2 transition-colors hover:bg-emerald-50 dark:border-gray-800 dark:bg-gray-800/80 dark:hover:bg-gray-800">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                    {{ $invoice->user?->name ?? 'Invoice' }}
                                    <span class="ml-2 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $invoice->created_at?->format('M j, Y g:i A') }}</span>
                                    <span class="ml-auto text-xs font-semibold text-emerald-700 dark:text-emerald-300">{{ $invoice->formatted_total }}</span>
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">#{{ $invoice->id }} • {{ $invoice->created_at?->diffForHumans() }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent payments yet.</p>
                        @endforelse
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Payments are not enabled.</p>
                    @endif
                </div>
                <div class="mt-3">
                    @if(\Route::has('admin.billing.index'))
                        <a href="{{ route('admin.billing.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
                            View all payments
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    @endif
                </div>
            </article>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Core Modules</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <article class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/70 p-5 shadow-sm dark:shadow-black/20">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">CRM</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Manage clients, leads, and communication from one place.</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 flex items-center justify-center">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                    <div class="rounded-lg border border-transparent dark:border-gray-800 bg-gray-50 dark:bg-gray-800/80 p-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Clients</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $clients->count() }}</p>
                    </div>
                    <div class="rounded-lg border border-transparent dark:border-gray-800 bg-gray-50 dark:bg-gray-800/80 p-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Leads</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $leads->total() }}</p>
                    </div>
                    <div class="rounded-lg border border-transparent dark:border-gray-800 bg-gray-50 dark:bg-gray-800/80 p-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Quick Links</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">3</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800/90 dark:hover:bg-gray-700 text-sm font-medium text-gray-900 dark:text-white transition-colors">
                        <i class="fas fa-users"></i>
                        Clients
                    </a>
                    <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800/90 dark:hover:bg-gray-700 text-sm font-medium text-gray-900 dark:text-white transition-colors">
                        <i class="fas fa-inbox"></i>
                        Leads
                    </a>
                    <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800/90 dark:hover:bg-gray-700 text-sm font-medium text-gray-900 dark:text-white transition-colors">
                        <i class="fas fa-comments"></i>
                        Messages
                    </a>
                </div>
            </article>

            <article class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/70 p-5 shadow-sm dark:shadow-black/20">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">CMS</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Manage pages, assets, and content publishing tools.</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 flex items-center justify-center">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                    <div class="rounded-lg border border-transparent dark:border-gray-800 bg-gray-50 dark:bg-gray-800/80 p-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pages</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $cmsData['enabled'] ? $cmsData['pagesCount'] : 0 }}</p>
                    </div>
                    <div class="rounded-lg border border-transparent dark:border-gray-800 bg-gray-50 dark:bg-gray-800/80 p-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Submissions</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $cmsData['enabled'] ? $cmsData['formSubmissionsCount'] : 0 }}</p>
                    </div>
                    <div class="rounded-lg border border-transparent dark:border-gray-800 bg-gray-50 dark:bg-gray-800/80 p-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Quick Links</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $cmsData['enabled'] ? 3 : 0 }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if(
                        \Route::has('admin.cms.index')
                    )
                        <a href="{{ route('admin.cms.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800/90 dark:hover:bg-gray-700 text-sm font-medium text-gray-900 dark:text-white transition-colors">
                            <i class="fas fa-pen"></i>
                            CMS Editor
                        </a>
                    @endif
                    @if(
                        \Route::has('admin.cms.images.index')
                    )
                        <a href="{{ route('admin.cms.images.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800/90 dark:hover:bg-gray-700 text-sm font-medium text-gray-900 dark:text-white transition-colors">
                            <i class="fas fa-images"></i>
                            Media Library
                        </a>
                    @endif
                    @if(
                        \Route::has('admin.ai.settings.index')
                    )
                        <a href="{{ route('admin.ai.settings.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800/90 dark:hover:bg-gray-700 text-sm font-medium text-gray-900 dark:text-white transition-colors">
                            <i class="fas fa-robot"></i>
                            AI Settings
                        </a>
                    @endif
                </div>
            </article>
        </div>
    </section>
</div>
@endsection
