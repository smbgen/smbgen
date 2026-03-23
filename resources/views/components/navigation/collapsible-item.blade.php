@props([
    'label' => null,
    'icon' => null,
    'children' => [],
])

{{-- Parent item with expand toggle --}}
<div class="space-y-1">
    <button
        x-data="{ expanded: false }"
        @click="expanded = !expanded"
        @class([
            'w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg',
            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white',
            'hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors',
            'font-medium text-sm',
        ])
        type="button"
    >
        <div class="flex items-center gap-3 flex-1 min-w-0">
            @if($icon)
                <i class="{{ $icon }} text-base w-5 flex-shrink-0 text-center"></i>
            @endif
            <span class="truncate">{{ $label }}</span>
        </div>
        <i class="fas fa-chevron-right text-xs flex-shrink-0 transition-transform duration-200" x-bind:class="{ 'rotate-90': expanded }"></i>
    </button>

    <!-- Nested children -->
    <div
        x-show="expanded"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="space-y-1 ml-2 pl-3 border-l border-gray-200 dark:border-gray-700/50"
    >
        {{ $slot }}
    </div>
</div>
