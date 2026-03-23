@props([
    'id' => null,
    'label' => null,
    'defaultOpen' => false,
])

@php
    $sectionId = $id ?? 'nav_' . str()->slug($label);
    $defaultOpenJs = $defaultOpen ? 'true' : 'false';
@endphp

<div
    x-data="{
        open: localStorage.getItem('{{ $sectionId }}') ? JSON.parse(localStorage.getItem('{{ $sectionId }}')) : {{ $defaultOpenJs }},
        init() {
            this.$watch('open', () => {
                localStorage.setItem('{{ $sectionId }}', JSON.stringify(this.open));
            });
        }
    }"
    class="mb-6 space-y-1"
>
    <!-- Section Header (Clickable to toggle) -->
    <button
        @click="open = !open"
        class="w-full flex items-center justify-between px-2 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/30 transition-colors text-gray-700 dark:text-gray-300 font-semibold text-xs tracking-wider uppercase"
        type="button"
    >
        <span class="flex-1 text-left">{{ $label }}</span>
        <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': open }" style="font-size: 0.75rem;"></i>
    </button>

    <!-- Collapsible Content -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="space-y-1 pl-2"
    >
        {{ $slot }}
    </div>
</div>
