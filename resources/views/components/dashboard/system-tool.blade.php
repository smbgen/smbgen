@props(['tool'])

<a href="{{ route($tool['route']) }}" class="flex items-center gap-3 p-3 bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-colors group">
    <div class="bg-{{ $tool['color'] }}-100 dark:bg-{{ $tool['color'] }}-500/20 rounded-lg p-2 group-hover:bg-{{ $tool['color'] }}-200 dark:group-hover:bg-{{ $tool['color'] }}-500/30 transition-colors">
        <i class="fas {{ $tool['icon'] }} text-{{ $tool['color'] }}-600 dark:text-{{ $tool['color'] }}-400"></i>
    </div>
    <span class="text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ $tool['title'] }}</span>
</a>
