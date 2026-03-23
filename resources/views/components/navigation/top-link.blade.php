@props([
    'href' => '#',
    'active' => false,
    'label' => '',
])

<a href="{{ $href }}" @class([
    'top-nav-link',
    'top-nav-link-active' => $active,
])>
    {{ $label }}
</a>
