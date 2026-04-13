@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">📧 Email Deliverability</h1>
            <p class="admin-page-subtitle">Monitor email delivery, opens, clicks, and failures</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <!-- Auto-refresh Toggle -->
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700">
                <label class="flex items-center cursor-pointer gap-2">
                    <input type="checkbox" id="autoRefreshToggle" class="form-checkbox h-4 w-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Auto-refresh</span>
                </label>
                <select id="autoRefreshInterval" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded px-2 py-1" disabled>
                    <option value="5">5s</option>
                    <option value="10" selected>10s</option>
                    <option value="30">30s</option>
                    <option value="60">1m</option>
                </select>
                <span id="refreshCountdown" class="text-xs text-gray-500 hidden"></span>
            </div>

            <!-- Export Button -->
            <button onclick="exportToCSV()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-file-csv"></i>
                Export CSV
            </button>

            <!-- Test SMTP Button -->
            <button onclick="testSMTPConnection()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-server"></i>
                Test SMTP
            </button>

            <!-- Bulk Actions Dropdown -->
            <div class="relative flex items-stretch">
                <button onclick="toggleBulkMenu()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-tasks"></i>
                    Bulk Actions
                </button>
                <div id="bulkActionsMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-10">
                    <button onclick="bulkResend()" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-t-lg">
                        <i class="fas fa-redo mr-2"></i>Resend Failed
                    </button>
                    <button onclick="bulkDelete()" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-trash mr-2"></i>Delete Selected
                    </button>
                    <button onclick="clearOldLogs()" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-b-lg">
                        <i class="fas fa-broom mr-2"></i>Clear Old Logs
                    </button>
                </div>
            </div>
            
            @if(\Route::has('admin.email.index'))
            <a href="{{ route('admin.email.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-pen"></i>
                Composer
            </a>
            @endif

        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <!-- Debug Panel -->
    <div class="admin-card border border-purple-500/30 mt-6">
        <div class="admin-card-header cursor-pointer" onclick="toggleDebugPanel()">
            <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <i class="fas fa-bug text-purple-400"></i>
                <h3 class="admin-card-title">Debug Panel</h3>
                <span class="text-xs px-2 py-1 bg-purple-100 dark:bg-purple-600/20 text-purple-700 dark:text-purple-300 rounded">DEV</span>
            </div>
            <i class="fas fa-chevron-down text-gray-400 transition-transform" id="debugToggleIcon"></i>
            </div>
        </div>
        <div id="debugPanelContent" class="hidden admin-card-body space-y-4">
            <!-- System Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Environment</div>
                    <div class="text-sm font-mono text-green-700 dark:text-green-400">{{ app()->environment() }}</div>
                </div>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Mailer</div>
                    <div class="text-sm font-mono text-blue-700 dark:text-blue-400">{{ config('mail.default') }}</div>
                </div>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">From Address</div>
                    <div class="text-sm font-mono text-purple-700 dark:text-purple-400">{{ config('mail.from.address') }}</div>
                </div>
            </div>

            <!-- Tracking Info -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-radar text-purple-400"></i>
                    Tracking Configuration
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Open Tracking URL</div>
                        <div class="text-xs font-mono text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-900 px-2 py-1 rounded mt-1 break-all">
                            {{ route('email.track.open', ['id' => 'TRACKING_ID']) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Click Tracking URL</div>
                        <div class="text-xs font-mono text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-900 px-2 py-1 rounded mt-1 break-all">
                            {{ route('email.track.click', ['id' => 'TRACKING_ID']) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Listeners -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-broadcast-tower text-blue-400"></i>
                    Active Event Listeners
                </h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span class="text-gray-700 dark:text-gray-300 font-mono">MessageSent → LogSentEmail</span>
                        <span class="text-gray-500">(Logs all emails)</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span class="text-gray-700 dark:text-gray-300 font-mono">MessageSending → Nightwatch</span>
                        <span class="text-gray-500">(Monitoring)</span>
                    </div>
                </div>
            </div>

            <!-- Database Stats -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-database text-yellow-400"></i>
                    Database Statistics
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $totalLogs = \App\Models\EmailLog::count();
                        $last24h = \App\Models\EmailLog::where('created_at', '>=', now()->subDay())->count();
                        $withTracking = \App\Models\EmailLog::whereNotNull('tracking_id')->count();
                        $withOpens = \App\Models\EmailLog::where('open_count', '>', 0)->count();
                    @endphp
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-400">{{ $totalLogs }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Total Logs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-400">{{ $last24h }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Last 24h</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-400">{{ $withTracking }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">With Tracking</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-400">{{ $withOpens }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">With Opens</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-clock text-indigo-400"></i>
                    Recent Activity (Last 5)
                </h4>
                <div class="space-y-2">
                    @php
                        $recentLogs = \App\Models\EmailLog::orderBy('created_at', 'desc')->limit(5)->get();
                    @endphp
                    @forelse($recentLogs as $log)
                        <div class="flex items-center justify-between text-xs bg-gray-50 dark:bg-gray-900 rounded px-3 py-2">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ 
                                    $log->status === 'opened' || $log->status === 'clicked' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                    ($log->status === 'sent' || $log->status === 'delivered' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                    ($log->status === 'failed' || $log->status === 'bounced' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'))
                                }}">
                                    {{ $log->status }}
                                </span>
                                <span class="text-gray-700 dark:text-gray-300 truncate flex-1">{{ $log->to_email }}</span>
                                <span class="text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-2 ml-3">
                                @if($log->open_count > 0)
                                    <span class="text-green-400" title="Opens">📨 {{ $log->open_count }}</span>
                                @endif
                                @if($log->click_count > 0)
                                    <span class="text-purple-400" title="Clicks">🖱️ {{ $log->click_count }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 text-xs py-4">No email logs found</div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex gap-2 flex-wrap">
                <button onclick="testTrackingPixel()" class="btn-secondary text-xs">
                    <i class="fas fa-vial mr-2"></i>Test Tracking Pixel
                </button>
                <button onclick="clearOldLogs()" class="btn-secondary text-xs">
                    <i class="fas fa-trash mr-2"></i>Clear Logs > 30 Days
                </button>
                <a href="{{ route('admin.email-logs.index') }}" class="btn-secondary text-xs">
                    <i class="fas fa-sync mr-2"></i>Refresh Data
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
        <!-- Total Sent -->
        <div class="admin-card bg-gradient-to-br from-blue-600 to-blue-800">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm font-medium">Total Sent</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $stats['sent'] ?? 0 }}</p>
                    </div>
                    <div class="text-4xl text-blue-200">📤</div>
                </div>
            </div>
        </div>

        <!-- Delivery Rate -->
        <div class="admin-card bg-gradient-to-br from-green-600 to-green-800">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-200 text-sm font-medium">Delivery Rate</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ number_format($stats['delivery_rate'], 1) }}%</p>
                    </div>
                    <div class="text-4xl text-green-200">✅</div>
                </div>
                <div class="mt-2 text-xs text-green-200">
                    {{ $stats['delivered'] }} delivered
                </div>
            </div>
        </div>

        <!-- Open Rate -->
        <div class="admin-card bg-gradient-to-br from-purple-600 to-purple-800">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-200 text-sm font-medium">Open Rate</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ number_format($stats['open_rate'], 1) }}%</p>
                    </div>
                    <div class="text-4xl text-purple-200">📨</div>
                </div>
                <div class="mt-2 text-xs text-purple-200">
                    {{ $stats['opened'] }} opened
                </div>
            </div>
        </div>

        <!-- Click Rate -->
        <div class="admin-card bg-gradient-to-br from-indigo-600 to-indigo-800">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-200 text-sm font-medium">Click Rate</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ number_format($stats['click_rate'], 1) }}%</p>
                </div>
                    <div class="text-4xl text-indigo-200">🖱️</div>
                </div>
                <div class="mt-2 text-xs text-indigo-200">
                    {{ $stats['clicked'] }} clicked
                </div>
            </div>
        </div>

        <!-- Bounce Rate -->
        <div class="admin-card bg-gradient-to-br from-red-600 to-red-800">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-200 text-sm font-medium">Bounce Rate</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ number_format($stats['bounce_rate'], 1) }}%</p>
                    </div>
                    <div class="text-4xl text-red-200">⚠️</div>
                </div>
                <div class="mt-2 text-xs text-red-200">
                    {{ $stats['bounced'] + $stats['failed'] }} bounced/failed
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="admin-card mt-6">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.email-logs.index') }}" class="space-y-4">
                <!-- First Row: Main Filters -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Time Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Range</label>
                        <select name="hours" class="form-select" onchange="this.form.submit()">
                            <option value="1" {{ $hours == 1 ? 'selected' : '' }}>Last Hour</option>
                            <option value="6" {{ $hours == 6 ? 'selected' : '' }}>Last 6 Hours</option>
                            <option value="24" {{ $hours == 24 ? 'selected' : '' }}>Last 24 Hours</option>
                            <option value="168" {{ $hours == 168 ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="720" {{ $hours == 720 ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="all" {{ $hours == 'all' ? 'selected' : '' }}>All Time</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $status == 'all' || !$status ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sent" {{ $status == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="opened" {{ $status == 'opened' ? 'selected' : '' }}>Opened</option>
                            <option value="clicked" {{ $status == 'clicked' ? 'selected' : '' }}>Clicked</option>
                            <option value="bounced" {{ $status == 'bounced' ? 'selected' : '' }}>Bounced</option>
                            <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Email, subject, tracking ID..." class="form-select w-full">
                    </div>

                    <!-- Submit -->
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </div>

                <!-- Second Row: Advanced Filters (Collapsible) -->
                <div id="advancedFilters" class="hidden">
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                            <i class="fas fa-sliders-h text-blue-400"></i>
                            Advanced Filters
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Engagement Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Engagement</label>
                                <select name="engagement" class="input w-full">
                                    <option value="">Any</option>
                                    <option value="opened">Has Opens</option>
                                    <option value="clicked">Has Clicks</option>
                                    <option value="none">No Engagement</option>
                                </select>
                            </div>

                            <!-- Has Booking Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Booking</label>
                                <select name="has_booking" class="input w-full">
                                    <option value="">Any</option>
                                    <option value="yes">Has Booking</option>
                                    <option value="no">No Booking</option>
                                </select>
                            </div>

                            <!-- Sent By User -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sent By</label>
                                <select name="user_id" class="input w-full">
                                    <option value="">All Users</option>
                                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toggle Advanced Filters Button -->
                <div class="flex justify-between items-center">
                    <button type="button" onclick="toggleAdvancedFilters()" class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-2">
                        <i class="fas fa-chevron-down transition-transform" id="advancedFiltersIcon"></i>
                        <span id="advancedFiltersText">Show Advanced Filters</span>
                    </button>
                    <a href="{{ route('admin.email-logs.index') }}" class="text-gray-400 hover:text-gray-300 text-sm">
                        <i class="fas fa-times mr-1"></i>Clear All Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Email Logs Table -->
    <div class="admin-card mt-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" 
                                   class="form-checkbox h-4 w-4 text-blue-600 rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Sent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Engagement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Booking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($emailLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer" onclick="handleRowClick(event, '{{ route('admin.email-logs.show', $log->id) }}')"  data-log-id="{{ $log->id }}">
                            <!-- Checkbox -->
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <input type="checkbox" class="email-checkbox form-checkbox h-4 w-4 text-blue-600 rounded" 
                                       value="{{ $log->id }}" onchange="updateBulkActionsCounter()">
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $log->status === 'opened' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-400 dark:border-green-700' :
                                    ($log->status === 'clicked' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-400 dark:border-purple-700' :
                                    ($log->status === 'delivered' || $log->status === 'sent' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-400 dark:border-blue-700' :
                                    ($log->status === 'bounced' || $log->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border border-red-400 dark:border-red-700' :
                                    'bg-gray-200 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300 border border-gray-300 dark:border-gray-600')))
                                }}">
                                    {{ $log->status_icon }} {{ ucfirst($log->status) }}
                                </span>
                            </td>

                            <!-- To Email -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $log->to_email }}</div>
                                @if($log->user && $log->user->name)
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Sent by: {{ $log->user->name }}</div>
                                @endif
                            </td>

                            <!-- Subject -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate" title="{{ $log->subject }}">
                                    {{ $log->subject }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    ID: {{ substr($log->tracking_id, 0, 8) }}...
                                </div>
                            </td>

                            <!-- Sent Time -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $log->formatTimestamp($log->sent_at) }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $log->created_at ? $log->created_at->diffForHumans() : 'Unknown' }}
                                </div>
                            </td>

                            <!-- Engagement -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3 text-sm">
                                    @if($log->open_count > 0)
                                        <span class="text-green-400" title="Opens">
                                            📨 {{ $log->open_count }}
                                        </span>
                                    @endif
                                    @if($log->click_count > 0)
                                        <span class="text-purple-400" title="Clicks">
                                            🖱️ {{ $log->click_count }}
                                        </span>
                                    @endif
                                    @if($log->open_count === 0 && $log->click_count === 0)
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </div>
                                @if($log->last_opened_at)
                                    <div class="text-xs text-gray-400">
                                        Last: {{ $log->last_opened_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>

                            <!-- Booking Link -->
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                @if($log->booking)
                                    <a href="{{ route('admin.bookings.show', $log->booking->id) }}" 
                                       class="text-blue-400 hover:text-blue-300 text-sm inline-flex items-center gap-1">
                                        <i class="fas fa-calendar-alt text-xs"></i>
                                        #{{ $log->booking->id }}
                                    </a>
                                @else
                                    <span class="text-gray-500 text-sm">—</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                    <button onclick="viewEmail({{ $log->id }}, {{ json_encode($log->subject) }}, {{ json_encode($log->body) }})" 
                                            class="text-purple-400 hover:text-purple-300 p-1 hover:bg-purple-500/10 rounded transition-colors" title="Preview Email">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </button>
                                    
                                    <a href="{{ route('admin.email-logs.show', $log->id) }}" 
                                       class="text-blue-400 hover:text-blue-300 p-1 hover:bg-blue-500/10 rounded transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(in_array($log->status, ['failed', 'bounced']))
                                        <form action="{{ route('admin.email-logs.resend', $log->id) }}" 
                                              method="POST" 
                                              class="inline-block"
                                              onsubmit="return confirm('Resend this email?')">
                                            @csrf
                                            <button type="submit" class="text-green-400 hover:text-green-300 p-1 hover:bg-green-500/10 rounded transition-colors" title="Resend">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.email-logs.destroy', $log->id) }}" 
                                          method="POST" 
                                          class="inline-block"
                                          onsubmit="return confirm('Delete this email log?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-1 hover:bg-red-500/10 rounded transition-colors" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-600 dark:text-gray-400">
                                <div class="text-4xl mb-4">📭</div>
                                <p class="text-lg">No email logs found</p>
                                <p class="text-sm mt-2">Emails sent through the tracking system will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($emailLogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $emailLogs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Email Preview Modal -->
<div id="emailPreviewModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gray-100 dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" id="modalSubject">Email Preview</h3>
            <button onclick="closeEmailPreview()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
            <div class="bg-white rounded-lg p-4">
                <iframe id="emailContentFrame" class="w-full min-h-[500px] border-0" sandbox="allow-same-origin"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle advanced filters
function toggleAdvancedFilters() {
    const filters = document.getElementById('advancedFilters');
    const icon = document.getElementById('advancedFiltersIcon');
    const text = document.getElementById('advancedFiltersText');
    
    filters.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
    text.textContent = filters.classList.contains('hidden') ? 'Show Advanced Filters' : 'Hide Advanced Filters';
}

// Bulk actions menu toggle
function toggleBulkMenu() {
    const menu = document.getElementById('bulkActionsMenu');
    menu.classList.toggle('hidden');
}

// Close bulk menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('bulkActionsMenu');
    const button = event.target.closest('button');
    
    if (!button || !button.textContent.includes('Bulk Actions')) {
        menu?.classList.add('hidden');
    }
});

// Select all checkboxes
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.email-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateBulkActionsCounter();
}

// Update bulk actions counter
function updateBulkActionsCounter() {
    const checked = document.querySelectorAll('.email-checkbox:checked').length;
    const selectAll = document.getElementById('selectAll');
    const total = document.querySelectorAll('.email-checkbox').length;
    
    selectAll.checked = checked === total && total > 0;
    selectAll.indeterminate = checked > 0 && checked < total;
}

// Get selected email IDs
function getSelectedEmailIds() {
    return Array.from(document.querySelectorAll('.email-checkbox:checked')).map(cb => cb.value);
}

// Handle row click without triggering for checkboxes
function handleRowClick(event, url) {
    if (!event.target.closest('input[type="checkbox"]') && !event.target.closest('.prevent-row-click')) {
        window.location = url;
    }
}

// Export to CSV
function exportToCSV() {
    const params = new URLSearchParams(window.location.search);
    const url = new URL(window.location.href);
    url.pathname = url.pathname + '/export';
    
    window.location = url.toString();
    
    // Show feedback
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center gap-2';
    notification.innerHTML = '<i class="fas fa-download"></i> Exporting email logs...';
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 3000);
}

// Test SMTP Connection
function testSMTPConnection() {
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
    
    fetch('{{ route("admin.email-logs.test-smtp") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        button.disabled = false;
        button.innerHTML = originalHTML;
        
        if (data.success) {
            const r = data.results;
            const connectivity = r.connectivity.status === 'success' ? '✅' : '❌';
            const ssl = r.ssl_handshake.status === 'success' ? '✅' : '❌';
            const auth = r.auth_test.status === 'success' ? '✅' : '❌';
            
            let message = '📧 SMTP Connection Test Results\n\n';
            message += '=== Configuration ===\n';
            message += `Host: ${r.config.host}\n`;
            message += `Port: ${r.config.port}\n`;
            message += `Encryption: ${r.config.encryption}\n`;
            message += `Username: ${r.config.username}\n`;
            message += `From: ${r.config.from_address} (${r.config.from_name})\n\n`;
            
            message += '=== Tests ===\n';
            message += `${connectivity} Connectivity: ${r.connectivity.message}\n`;
            if (r.connectivity.response_time) {
                message += `   Response Time: ${r.connectivity.response_time}\n`;
            }
            if (r.connectivity.error) {
                message += `   Error: ${r.connectivity.error}\n`;
            }
            message += '\n';
            
            message += `${ssl} SSL/TLS Handshake: ${r.ssl_handshake.message}\n`;
            if (r.ssl_handshake.banner) {
                message += `   Banner: ${r.ssl_handshake.banner}\n`;
            }
            if (r.ssl_handshake.response_time) {
                message += `   Response Time: ${r.ssl_handshake.response_time}\n`;
            }
            if (r.ssl_handshake.error) {
                message += `   Error: ${r.ssl_handshake.error}\n`;
            }
            message += '\n';
            
            message += `${auth} Mail Configuration: ${r.auth_test.message}\n`;
            if (r.auth_test.error) {
                message += `   Error: ${r.auth_test.error}\n`;
            }
            
            const allPassed = r.connectivity.status === 'success' && 
                             r.ssl_handshake.status === 'success' && 
                             r.auth_test.status === 'success';
            
            if (allPassed) {
                message += '\n✅ All tests passed! Email system is ready.';
            } else {
                message += '\n⚠️ Some tests failed. Check errors above and your .env configuration.';
            }
            
            alert(message);
        } else {
            alert(`❌ SMTP Test Failed!\n\nError: ${data.error}\n\nCheck your mail configuration in .env file.`);
        }
    })
    .catch(error => {
        button.disabled = false;
        button.innerHTML = originalHTML;
        alert('Error testing SMTP: ' + error.message);
    });
}

// Bulk resend failed emails
function bulkResend() {
    const selectedIds = getSelectedEmailIds();
    
    if (selectedIds.length === 0) {
        alert('Please select emails to resend');
        return;
    }
    
    if (!confirm(`Resend ${selectedIds.length} selected email(s)?`)) {
        return;
    }
    
    fetch('{{ route("admin.email-logs.index") }}/bulk-resend', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || `Successfully resent ${data.count} emails`);
        location.reload();
    })
    .catch(error => {
        alert('Error resending emails: ' + error.message);
    });
}

// Bulk delete
function bulkDelete() {
    const selectedIds = getSelectedEmailIds();
    
    if (selectedIds.length === 0) {
        alert('Please select emails to delete');
        return;
    }
    
    if (!confirm(`Delete ${selectedIds.length} selected email log(s)? This cannot be undone.`)) {
        return;
    }
    
    fetch('{{ route("admin.email-logs.index") }}/bulk-delete', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || `Successfully deleted ${data.count} email logs`);
        location.reload();
    })
    .catch(error => {
        alert('Error deleting emails: ' + error.message);
    });
}

function toggleDebugPanel() {
    const content = document.getElementById('debugPanelContent');
    const icon = document.getElementById('debugToggleIcon');
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

function testTrackingPixel() {
    const testId = 'test-' + Math.random().toString(36).substring(7);
    const pixelUrl = '{{ route("email.track.open", ["id" => "TEST_ID"]) }}'.replace('TEST_ID', testId);
    
    alert(`Testing tracking pixel...\n\nURL: ${pixelUrl}\n\nCheck browser network tab and server logs.`);
    
    const img = new Image();
    img.onload = () => {
        console.log('Tracking pixel loaded successfully');
    };
    img.onerror = () => {
        console.error('Tracking pixel failed to load');
    };
    img.src = pixelUrl;
}

function clearOldLogs() {
    if (confirm('Delete all email logs older than 30 days? This cannot be undone.')) {
        fetch('{{ route("admin.email-logs.index") }}/clear-old', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Logs cleared successfully');
            location.reload();
        })
        .catch(error => {
            alert('Error clearing logs: ' + error.message);
        });
    }
}

function viewEmail(id, subject, body) {
    const modal = document.getElementById('emailPreviewModal');
    const modalSubject = document.getElementById('modalSubject');
    const iframe = document.getElementById('emailContentFrame');
    
    modalSubject.textContent = subject || 'Email Preview';
    iframe.srcdoc = body;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEmailPreview() {
    const modal = document.getElementById('emailPreviewModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('emailPreviewModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEmailPreview();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEmailPreview();
    }
});

// Auto-refresh functionality
let autoRefreshTimer = null;
let countdownTimer = null;
let secondsRemaining = 0;

const autoRefreshToggle = document.getElementById('autoRefreshToggle');
const autoRefreshInterval = document.getElementById('autoRefreshInterval');
const refreshCountdown = document.getElementById('refreshCountdown');

function startAutoRefresh() {
    const interval = parseInt(autoRefreshInterval.value) * 1000;
    secondsRemaining = parseInt(autoRefreshInterval.value);
    
    // Clear existing timers
    if (autoRefreshTimer) clearInterval(autoRefreshTimer);
    if (countdownTimer) clearInterval(countdownTimer);
    
    // Start countdown display
    refreshCountdown.classList.remove('hidden');
    updateCountdown();
    
    countdownTimer = setInterval(() => {
        secondsRemaining--;
        if (secondsRemaining <= 0) {
            secondsRemaining = parseInt(autoRefreshInterval.value);
        }
        updateCountdown();
    }, 1000);
    
    // Start auto-refresh
    autoRefreshTimer = setInterval(() => {
        console.log('Auto-refreshing page...');
        window.location.reload();
    }, interval);
}

function stopAutoRefresh() {
    if (autoRefreshTimer) clearInterval(autoRefreshTimer);
    if (countdownTimer) clearInterval(countdownTimer);
    refreshCountdown.classList.add('hidden');
}

function updateCountdown() {
    refreshCountdown.textContent = `${secondsRemaining}s`;
}

autoRefreshToggle.addEventListener('change', function() {
    if (this.checked) {
        autoRefreshInterval.disabled = false;
        startAutoRefresh();
    } else {
        autoRefreshInterval.disabled = true;
        stopAutoRefresh();
    }
});

autoRefreshInterval.addEventListener('change', function() {
    if (autoRefreshToggle.checked) {
        startAutoRefresh();
    }
});

// Save auto-refresh preference
autoRefreshToggle.addEventListener('change', function() {
    localStorage.setItem('emailLogsAutoRefresh', this.checked);
});

autoRefreshInterval.addEventListener('change', function() {
    localStorage.setItem('emailLogsRefreshInterval', this.value);
});

// Restore auto-refresh preference on load
window.addEventListener('DOMContentLoaded', function() {
    const savedAutoRefresh = localStorage.getItem('emailLogsAutoRefresh') === 'true';
    const savedInterval = localStorage.getItem('emailLogsRefreshInterval') || '10';
    
    if (savedAutoRefresh) {
        autoRefreshToggle.checked = true;
        autoRefreshInterval.value = savedInterval;
        autoRefreshInterval.disabled = false;
        startAutoRefresh();
    }
});

</script>
@endsection
