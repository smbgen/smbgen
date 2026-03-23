@props([
    'href' => '#',
    'active' => false,
    'icon' => 'fas fa-circle',
    'label' => '',
    'badge' => null,
    'badgeClass' => 'bg-primary-600/20 text-primary-400',
    'compact' => false,
])

<a href="{{ $href }}" @class(['sidebar-link', 'active' => $active])>
    <i class="{{ $icon }} {{ $compact ? 'ml-4 text-sm' : 'text-lg' }}"></i>
    <span @class([$compact ? 'text-sm' : ''])>{{ $label }}</span>

    @if(filled($badge))
        <span class="ml-auto {{ $badgeClass }} text-xs font-semibold px-2 py-0.5 rounded-full">{{ $badge }}</span>
    @endif
</a>
