@props(['card'])

<div class="group relative overflow-hidden bg-gradient-to-br {{ $card['gradient'] }} rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 cursor-pointer"
     x-data="{ count: {{ $card['value'] }}, target: {{ $card['value'] }}, hasNewActivity: {{ $card['value'] > 0 ? 'true' : 'false' }} }"
     x-init="
        // Animate counter on load - start from 0
        count = 0;
        let duration = 1000;
        let startTime = Date.now();
        
        let interval = setInterval(() => {
            let elapsed = Date.now() - startTime;
            let progress = Math.min(elapsed / duration, 1);
            
            count = Math.floor(progress * target);
            
            if (progress >= 1) {
                count = target;
                clearInterval(interval);
            }
        }, 16); // ~60fps
     ">
    <!-- Pulse indicator for new activity -->
    <div x-show="hasNewActivity && target > 0" class="absolute top-4 right-4">
        <span class="flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
        </span>
    </div>
    
    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
    <div class="relative">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white/20 dark:bg-white/20 rounded-xl p-3 group-hover:bg-white/30 dark:group-hover:bg-white/30 transition-colors">
                <i class="fas {{ $card['icon'] }} text-white dark:text-white text-2xl group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="text-gray-900 dark:text-white/80 text-sm font-medium">{{ $card['subtitle'] }}</span>
        </div>
        <div class="text-gray-900 dark:text-white text-4xl font-bold mb-2 tabular-nums" x-text="count"></div>
        <div class="text-gray-700 dark:text-white/90 text-sm font-medium">{{ $card['title'] }}</div>
        @if(isset($card['route']))
            <a href="{{ route($card['route']) }}" class="mt-4 inline-flex items-center text-gray-900 dark:text-white text-sm font-medium hover:text-gray-700 dark:hover:text-white/80 transition-colors group/link">
                {{ $card['linkText'] }} <i class="fas fa-arrow-right ml-2 group-hover/link:translate-x-1 transition-transform"></i>
            </a>
        @elseif(isset($card['anchor']))
            <a href="{{ $card['anchor'] }}" class="mt-4 inline-flex items-center text-gray-900 dark:text-white text-sm font-medium hover:text-gray-700 dark:hover:text-white/80 transition-colors group/link">
                {{ $card['linkText'] }} <i class="fas fa-arrow-right ml-2 group-hover/link:translate-x-1 transition-transform"></i>
            </a>
        @endif
    </div>
</div>
