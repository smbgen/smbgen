@props(['title' => 'Quick Stats', 'icon' => 'fa-tachometer-alt'])

<div class="bg-white dark:bg-gradient-to-br dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas {{ $icon }} text-blue-600 dark:text-blue-400"></i>
            {{ $title }}
        </h3>
        <span class="text-gray-600 dark:text-gray-400 text-xs">Last 7 days</span>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <!-- New Clients -->
        <div class="bg-blue-50 dark:bg-blue-500/10 rounded-xl p-4 border border-blue-200 dark:border-blue-500/20">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-user-plus text-blue-600 dark:text-blue-400 text-sm"></i>
                <span class="text-gray-600 dark:text-gray-300 text-xs">New Clients</span>
            </div>
            <div class="text-gray-900 dark:text-white text-2xl font-bold">
                {{ \App\Models\Client::where('created_at', '>=', now()->subDays(7))->count() }}
            </div>
        </div>

        <!-- New Leads -->
        <div class="bg-purple-50 dark:bg-purple-500/10 rounded-xl p-4 border border-purple-200 dark:border-purple-500/20">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-inbox text-purple-600 dark:text-purple-400 text-sm"></i>
                <span class="text-gray-600 dark:text-gray-300 text-xs">New Leads</span>
            </div>
            <div class="text-gray-900 dark:text-white text-2xl font-bold">
                {{ \App\Models\LeadForm::where('created_at', '>=', now()->subDays(7))->count() }}
            </div>
        </div>

        @if(config('business.features.booking'))
        <!-- Completed Bookings -->
        <div class="bg-green-50 dark:bg-green-500/10 rounded-xl p-4 border border-green-200 dark:border-green-500/20">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-sm"></i>
                <span class="text-gray-600 dark:text-gray-300 text-xs">Completed</span>
            </div>
            <div class="text-gray-900 dark:text-white text-2xl font-bold">
                {{ \App\Models\Booking::where('status', 'completed')->where('updated_at', '>=', now()->subDays(7))->count() }}
            </div>
        </div>
        @endif

        <!-- Emails Sent -->
        @if(\Route::has('admin.email-logs.index'))
        <div class="bg-cyan-50 dark:bg-cyan-500/10 rounded-xl p-4 border border-cyan-200 dark:border-cyan-500/20">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-paper-plane text-cyan-600 dark:text-cyan-400 text-sm"></i>
                <span class="text-gray-600 dark:text-gray-300 text-xs">Emails Sent</span>
            </div>
            <div class="text-gray-900 dark:text-white text-2xl font-bold">
                {{ \App\Models\EmailLog::where('created_at', '>=', now()->subDays(7))->count() }}
            </div>
        </div>
        @endif
    </div>

    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between text-xs">
            <span class="text-gray-600 dark:text-gray-400">Total Activity</span>
            <span class="text-gray-900 dark:text-white font-bold">
                {{ \App\Models\Client::where('created_at', '>=', now()->subDays(7))->count() + 
                   \App\Models\LeadForm::where('created_at', '>=', now()->subDays(7))->count() }}
                <span class="text-gray-600 dark:text-gray-400">interactions</span>
            </span>
        </div>
    </div>
</div>
