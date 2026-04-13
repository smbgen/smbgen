@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Lead Management</h1>
            <p class="admin-page-subtitle">View and manage all lead form submissions</p>
        </div>
        <div class="action-buttons flex gap-2">
            <!-- Email Notification Toggle -->
            <form action="{{ route('admin.leads.toggle-notifications') }}" method="POST" class="flex items-center">
                @csrf
                <label class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-600 transition-colors cursor-pointer">
                    <input type="checkbox" name="notify_on_new_leads" value="1" {{ auth()->user()->notify_on_new_leads ? 'checked' : '' }} 
                           onchange="this.form.submit()"
                           class="form-checkbox h-4 w-4 text-blue-500 rounded">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-bell text-blue-400 mr-2"></i>Email me new leads
                    </span>
                </label>
            </form>

            <a href="{{ route('admin.leads.export.csv', request()->query()) }}" 
               class="btn-secondary">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
            @if(config('business.features.cms'))
            <a href="{{ route('admin.cms.index') }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Manage CMS Pages
            </a>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @if(isset($totalLeads) && isset($todayLeads) && isset($cmsLeads))
            <div class="card bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200 dark:from-blue-900/50 dark:to-blue-800/30 dark:border-blue-700">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-700 dark:text-blue-300 text-sm font-medium">Total Leads</p>
                            <p class="text-3xl font-bold text-blue-900 dark:text-white mt-2">{{ $totalLeads }}</p>
                        </div>
                        <div class="bg-blue-200 dark:bg-blue-600/30 rounded-full p-4">
                            <i class="fas fa-users text-blue-700 dark:text-blue-300 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-gradient-to-br from-green-50 to-green-100 border-green-200 dark:from-green-900/50 dark:to-green-800/30 dark:border-green-700">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-700 dark:text-green-300 text-sm font-medium">Today's Leads</p>
                            <p class="text-3xl font-bold text-green-900 dark:text-white mt-2">{{ $todayLeads }}</p>
                        </div>
                        <div class="bg-green-200 dark:bg-green-600/30 rounded-full p-4">
                            <i class="fas fa-calendar-day text-green-700 dark:text-green-300 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200 dark:from-purple-900/50 dark:to-purple-800/30 dark:border-purple-700">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-700 dark:text-purple-300 text-sm font-medium">CMS Form Leads</p>
                            <p class="text-3xl font-bold text-purple-900 dark:text-white mt-2">{{ $cmsLeads }}</p>
                        </div>
                        <div class="bg-purple-200 dark:bg-purple-600/30 rounded-full p-4">
                            <i class="fas fa-file-alt text-purple-700 dark:text-purple-300 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-span-3 text-center text-gray-400 py-8">No stats available.</div>
        @endif
    </div>

    <!-- Filters -->
    <div class="admin-card">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.leads.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="form-label">Search</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Name or email..."
                               class="form-input">
                    </div>

                    <!-- Source Filter -->
                    <div>
                        <label class="form-label">Source</label>
                        <select name="source" class="form-select">
                            <option value="">All Sources</option>
                            <option value="cms" {{ request('source') === 'cms' ? 'selected' : '' }}>CMS Forms</option>
                            <option value="other" {{ request('source') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="form-label">Date From</label>
                        <input type="date" 
                               name="date_from" 
                               value="{{ request('date_from') }}"
                               class="form-input">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="form-label">Date To</label>
                        <input type="date" 
                               name="date_to" 
                               value="{{ request('date_to') }}"
                               class="form-input">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('admin.leads.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="admin-card">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Contact</th>
                        <th>Message</th>
                        <th>Source</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" 
                        onclick="window.location='{{ route('admin.leads.show', $lead) }}'">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
                                        <span class="text-white font-semibold">{{ substr($lead->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lead->name }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-envelope text-xs mr-1"></i>{{ $lead->email }}
                                    </div>
                                    @if($lead->form_data && isset($lead->form_data['phone']))
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-phone text-xs mr-1"></i>{{ $lead->form_data['phone'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 max-w-md">
                            @if($lead->message)
                                <div class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2" title="{{ $lead->message }}">
                                    {{ Str::limit($lead->message, 100) }}
                                </div>
                            @else
                                <span class="text-xs text-gray-500 dark:text-gray-500 italic">No message</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($lead->cmsPage)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 dark:bg-purple-600/20 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-600/30">
                                    <i class="fas fa-file-alt mr-1"></i>{{ $lead->cmsPage->title }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-600/20 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600/30">
                                    {{ $lead->source_site ?: 'Direct' }}
                                </span>
                            @endif
                            @if($lead->referer)
                                <div class="text-xs text-gray-500 dark:text-gray-500 mt-1 truncate max-w-[150px]" title="{{ $lead->referer }}">
                                    <i class="fas fa-link mr-1"></i>{{ parse_url($lead->referer, PHP_URL_HOST) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <div>{{ $lead->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">{{ $lead->created_at->format('h:i A') }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $lead->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.leads.show', $lead) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/40 hover:text-blue-300 transition-colors" 
                                   title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <form action="{{ route('admin.leads.convert', $lead) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Convert this lead to a client?');">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-600/20 text-green-400 hover:bg-green-600/40 hover:text-green-300 transition-colors" 
                                            title="Convert to Client">
                                        <i class="fas fa-user-plus text-sm"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.leads.destroy', $lead) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Delete this lead permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-600/20 text-red-400 hover:bg-red-600/40 hover:text-red-300 transition-colors" 
                                            title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p class="text-lg">No leads found</p>
                            @if(request()->hasAny(['search', 'source', 'date_from', 'date_to']))
                                <p class="text-sm mt-2">Try adjusting your filters</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $leads->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
