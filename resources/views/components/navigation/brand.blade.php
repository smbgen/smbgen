@props([
    'href' => '#',
    'subtitle' => null,
    'compact' => false,
])

<a href="{{ $href }}" class="flex items-center gap-3 group">
    <div class="{{ $compact ? 'w-8 h-8 rounded-lg' : 'w-10 h-10 rounded-xl' }} bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-200">
        <i class="fas fa-bridge text-white {{ $compact ? 'text-xs' : 'text-lg' }}"></i>
    </div>
    <div class="flex flex-col min-w-0">
        <span class="text-gray-900 dark:text-white font-bold {{ $compact ? 'text-sm' : 'text-lg' }} leading-tight truncate max-w-40">{{ config('app.name') }}</span>
        @if($subtitle)
            <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $subtitle }}</span>
        @endif
    </div>
</a>
