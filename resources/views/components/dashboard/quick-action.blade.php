@props(['action'])

<a href="{{ route($action['route']) }}" class="group bg-gradient-to-br {{ $action['gradient'] }} hover:opacity-90 rounded-xl p-6 transition-all duration-300 hover:scale-105 shadow-lg">
    <i class="fas {{ $action['icon'] }} text-white dark:text-white text-3xl mb-3"></i>
    <div class="text-white dark:text-white font-semibold text-lg">{{ $action['title'] }}</div>
    <div class="text-white/80 dark:text-white/80 text-sm mt-1">{{ $action['description'] }}</div>
</a>
