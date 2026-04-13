<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl p-2">
                <i class="fas fa-envelope-open-text text-white"></i>
            </div>
            Email Analytics
        </h3>
        @if(\Route::has('admin.email-logs.index'))
        <a href="{{ route('admin.email-logs.index') }}" 
           class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 text-sm font-medium transition-colors flex items-center gap-2">
            View All Logs
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
        @endif
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Sent Today -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-blue-500/20 rounded-lg p-2">
                    <i class="fas fa-paper-plane text-blue-600 dark:text-blue-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $emailData['sentToday'] }}</span>
            </div>
            <div class="text-gray-600 dark:text-gray-400 text-sm">Sent Today</div>
        </div>

        <!-- Delivery Rate -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-green-500/20 rounded-lg p-2">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $emailData['deliveryRate'] }}%</span>
            </div>
            <div class="text-gray-600 dark:text-gray-400 text-sm">Delivery Rate (24h)</div>
        </div>

        <!-- Failed Emails -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-{{ $emailData['failedToday'] > 0 ? 'red' : 'gray' }}-500/20 rounded-lg p-2">
                    <i class="fas fa-exclamation-triangle text-{{ $emailData['failedToday'] > 0 ? 'red-600 dark:text-red-400' : 'gray-600 dark:text-gray-400' }}"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $emailData['failedToday'] }}</span>
            </div>
            <div class="text-gray-600 dark:text-gray-400 text-sm">Failed Today</div>
        </div>

        <!-- Total This Week -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-purple-500/20 rounded-lg p-2">
                    <i class="fas fa-chart-line text-purple-600 dark:text-purple-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $emailData['sentThisWeek'] }}</span>
            </div>
            <div class="text-gray-600 dark:text-gray-400 text-sm">This Week</div>
        </div>
    </div>

    @if($emailData['recentEmails']->isNotEmpty())
    <!-- Recent Email Activity -->
    <div>
        <h4 class="text-gray-900 dark:text-white font-semibold mb-3 flex items-center gap-2">
            <i class="fas fa-history text-gray-500 dark:text-gray-400 text-sm"></i>
            Recent Activity
        </h4>
        <div class="space-y-2">
            @foreach($emailData['recentEmails'] as $email)
            <a href="{{ route('admin.email-logs.show', $email->id) }}" 
               class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600/50 hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200 group">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="flex-shrink-0">
                        @if($email->status === 'sent' || $email->status === 'delivered')
                            <div class="bg-green-500/20 rounded-lg p-2">
                                <i class="fas fa-check text-green-600 dark:text-green-400 text-sm"></i>
                            </div>
                        @elseif($email->status === 'failed' || $email->status === 'bounced')
                            <div class="bg-red-500/20 rounded-lg p-2">
                                <i class="fas fa-times text-red-600 dark:text-red-400 text-sm"></i>
                            </div>
                        @else
                            <div class="bg-blue-500/20 rounded-lg p-2">
                                <i class="fas fa-paper-plane text-blue-600 dark:text-blue-400 text-sm"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-gray-900 dark:text-white text-sm font-medium truncate">
                            {{ $email->subject ?? 'No Subject' }}
                        </div>
                        <div class="text-gray-600 dark:text-gray-400 text-xs mt-1 flex items-center gap-2">
                            <span class="truncate">To: {{ $email->to_email }}</span>
                            <span class="text-gray-400 dark:text-gray-600">•</span>
                            <span class="flex-shrink-0">{{ $email->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0 ml-3">
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $email->status === 'sent' || $email->status === 'delivered' ? 'bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300' : '' }}
                        {{ $email->status === 'failed' || $email->status === 'bounced' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300' : '' }}
                        {{ !in_array($email->status, ['sent', 'delivered', 'failed', 'bounced']) ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300' : '' }}">
                        {{ ucfirst($email->status) }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @else
    <div class="text-center py-8">
        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-200 dark:border-gray-600/50">
            <i class="fas fa-inbox text-gray-400 dark:text-gray-500 text-3xl mb-3"></i>
            <p class="text-gray-600 dark:text-gray-400">No recent email activity</p>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    @if(\Route::has('admin.email.index'))
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-gray-600 dark:text-gray-400 text-sm">
                <i class="fas fa-lightbulb text-yellow-500 dark:text-yellow-400 mr-2"></i>
                Send emails to clients using the Email Composer
            </div>
            <a href="{{ route('admin.email.index') }}" 
               class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 text-sm">
                <i class="fas fa-pen"></i>
                Compose Email
            </a>
        </div>
    </div>
    @endif
</div>
