@props(['metrics'])

@if(count($metrics) > 0)
<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
        <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400"></i>
        Business Metrics
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($metrics as $metric)
        <a href="{{ route($metric['route']) }}" class="group p-4 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="bg-{{ $metric['color'] }}-500/20 rounded-lg p-2">
                        <i class="fas {{ $metric['icon'] }} text-{{ $metric['color'] }}-600 dark:text-{{ $metric['color'] }}-400"></i>
                    </div>
                    <span class="text-gray-600 dark:text-gray-300 text-sm">{{ $metric['label'] }}</span>
                </div>
                @if(isset($metric['highlight']) && $metric['highlight'])
                <span class="flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
                @endif
            </div>
            <div class="flex items-end justify-between">
                <span class="text-gray-900 dark:text-white font-bold text-3xl">{{ $metric['value'] }}</span>
                @if(isset($metric['change']))
                <span class="text-{{ $metric['color'] }}-600 dark:text-{{ $metric['color'] }}-400 text-xs">
                    +{{ $metric['change'] }} {{ $metric['changeLabel'] ?? '' }}
                </span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
