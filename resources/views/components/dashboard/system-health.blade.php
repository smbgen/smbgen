@props(['health'])

@if(count($health) > 0)
<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
        <i class="fas fa-heartbeat text-green-600 dark:text-green-400"></i>
        System Health
    </h3>
    <div class="space-y-3">
        @foreach($health as $check)
        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="rounded-lg p-2 
                    {{ $check['status'] === 'healthy' || $check['status'] === 'connected' || $check['status'] === 'clear' ? 'bg-green-100 dark:bg-green-500/20' : '' }}
                    {{ $check['status'] === 'warning' || $check['status'] === 'attention' ? 'bg-yellow-100 dark:bg-yellow-500/20' : '' }}
                    {{ $check['status'] === 'disconnected' || $check['status'] === 'error' ? 'bg-red-100 dark:bg-red-500/20' : '' }}
                ">
                    <i class="fas {{ $check['icon'] }} 
                        {{ $check['status'] === 'healthy' || $check['status'] === 'connected' || $check['status'] === 'clear' ? 'text-green-600 dark:text-green-400' : '' }}
                        {{ $check['status'] === 'warning' || $check['status'] === 'attention' ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                        {{ $check['status'] === 'disconnected' || $check['status'] === 'error' ? 'text-red-600 dark:text-red-400' : '' }}
                    "></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-medium">{{ $check['label'] }}</div>
                    @if(isset($check['message']))
                    <div class="text-gray-600 dark:text-gray-400 text-xs">{{ $check['message'] }}</div>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-medium
                    {{ $check['status'] === 'healthy' || $check['status'] === 'connected' || $check['status'] === 'clear' ? 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400' : '' }}
                    {{ $check['status'] === 'warning' || $check['status'] === 'attention' ? 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-600 dark:text-yellow-400' : '' }}
                    {{ $check['status'] === 'disconnected' || $check['status'] === 'error' ? 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400' : '' }}
                ">
                    {{ ucfirst($check['status']) }}
                </span>
                @if(isset($check['route']))
                <a href="{{ route($check['route']) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                    <i class="fas fa-external-link-alt text-sm"></i>
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
