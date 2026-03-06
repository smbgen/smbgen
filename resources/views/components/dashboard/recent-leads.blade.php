@props(['leads'])

<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
        <div class="bg-gradient-to-r from-green-600 dark:from-green-500 to-emerald-600 dark:to-emerald-500 rounded-xl p-2">
            <i class="fas fa-chart-line text-white"></i>
        </div>
        Recent Leads
    </h2>
    <div class="space-y-3">
        @forelse($leads as $lead)
        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold">
                    {{ substr($lead->name, 0, 1) }}
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-medium">{{ $lead->name }}</div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">{{ $lead->email }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-600 dark:text-gray-400 text-sm">{{ $lead->created_at->diffForHumans() }}</span>
                <a href="{{ route('admin.leads.show', $lead) }}" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm transition-colors">
                    View
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-600 dark:text-gray-400">
            <i class="fas fa-inbox text-5xl mb-4 opacity-50"></i>
            <p>No recent leads</p>
        </div>
        @endforelse
    </div>
    @if($leads->count() > 0)
    <div class="mt-6">
        <a href="{{ route('admin.leads.index') }}" class="block text-center bg-gray-700 dark:bg-gray-700 hover:bg-gray-600 dark:hover:bg-gray-600 text-white rounded-xl py-3 transition-colors font-medium">
            View All Leads <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
    @endif
</div>
