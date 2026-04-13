@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Activity Logs</h1>
            <p class="admin-page-subtitle">Monitor system activity and user actions</p>
        </div>
        <div class="flex gap-2">
            <button 
                onclick="document.getElementById('clear-logs-form').classList.toggle('hidden')"
                class="btn-danger">
                <i class="fas fa-trash mr-2"></i>Clear Old Logs
            </button>
        </div>
    </div>

    <div class="space-y-6">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <strong>Error: </strong>{{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="admin-card">
                <div class="admin-card-body">
                    <div class="text-gray-400 text-sm">Total Logs</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-200">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
            <div class="admin-card">
                <div class="admin-card-body">
                    <div class="text-gray-400 text-sm">Today</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-200">{{ number_format($stats['today']) }}</div>
                </div>
            </div>
            <div class="admin-card">
                <div class="admin-card-body">
                    <div class="text-gray-400 text-sm">This Week</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-200">{{ number_format($stats['this_week']) }}</div>
                </div>
            </div>
            <div class="admin-card">
                <div class="admin-card-body">
                    <div class="text-gray-400 text-sm">This Month</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-200">{{ number_format($stats['this_month']) }}</div>
                </div>
            </div>
        </div>

        <!-- Clear Logs Form (Hidden by default) -->
        <div id="clear-logs-form" class="hidden admin-card">
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.activity-logs.clear') }}" onsubmit="return confirm('Are you sure you want to delete old activity logs? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label for="older_than" class="form-label">Delete logs older than (days)</label>
                            <input type="number" name="older_than" id="older_than" value="90" min="1" required class="form-input">
                            @error('older_than')
                                <p class="form-help text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="btn-danger">
                            <i class="fas fa-trash mr-2"></i>Clear Logs
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-card">
            <div class="admin-card-body">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="form-group">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                placeholder="Search description..."
                                class="form-input">
                        </div>

                        <!-- Action Filter -->
                        <div class="form-group">
                            <label for="action" class="form-label">Action</label>
                            <select name="action" id="action" class="form-select">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $action)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- User Filter -->
                        <div class="form-group">
                            <label for="user_id" class="form-label">User</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="form-group">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                                class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                                class="form-input">
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="admin-card">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>User</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->action_color }}">
                                        <span class="mr-1">{{ $log->action_icon }}</span>
                                        {{ $log->formatted_action }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                        {{ $log->user?->name ?? 'System' }}
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        {{ $log->user?->email ?? 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 dark:text-gray-200">{{ $log->description }}</div>
                                </td>
                                <td class="whitespace-nowrap text-sm text-gray-400">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap text-sm text-gray-400">
                                    {{ $log->created_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.activity-logs.show', $log) }}" class="text-blue-700 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    No activity logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="admin-card-footer">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
