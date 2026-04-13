@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-6 space-y-6">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Messages</h1>
            <p class="admin-page-subtitle">View and manage your messages</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('messages.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>{{ __('New Message') }}
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Messages Table -->
    <div class="admin-card">
        @if($messages->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user mr-2"></i>From/To
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-envelope mr-2"></i>Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-calendar mr-2"></i>Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-info-circle mr-2"></i>Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-cog mr-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($messages as $message)
                                <tr class="{{ ($message->thread_unread_count ?? 0) > 0 ? 'bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors duration-200 cursor-pointer" onclick="window.location='{{ route('messages.show', $message) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $otherPerson = $message->sender_id === Auth::id() ? $message->recipient : $message->sender;
                                        @endphp
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center mr-3 text-white font-semibold">
                                                {{ substr($otherPerson->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $otherPerson->name ?? 'Unknown' }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $otherPerson->email ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $message->subject ?? 'No Subject' }}</div>
                                            @if(($message->thread_message_count ?? 0) > 1)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                    {{ $message->thread_message_count }} messages
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                            @if($message->sender_id === Auth::id())
                                                <span class="text-gray-500">You: </span>
                                            @endif
                                            {{ Str::limit($message->body, 60) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-gray-900 dark:text-gray-100">{{ $message->created_at ? $message->created_at->format('M j, Y') : 'N/A' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $message->created_at ? $message->created_at->format('g:i A') : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(($message->thread_unread_count ?? 0) > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/30 text-yellow-300 border border-yellow-500">
                                                <i class="fas fa-circle mr-1 text-xs"></i>{{ $message->thread_unread_count }} new
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-check mr-1"></i>Read
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('messages.show', $message) }}" class="btn-secondary text-xs" onclick="event.stopPropagation()">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $messages->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-500 dark:text-gray-400 mb-6">
                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-envelope text-2xl"></i>
                        </div>
                        <h4 class="text-xl font-semibold mb-2">No messages found</h4>
                        <p>Start a conversation by sending a message.</p>
                    </div>
                    <a href="{{ route('messages.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>{{ __('Send Your First Message') }}
                    </a>
                </div>
            @endif
    </div>
</div>
@endsection
