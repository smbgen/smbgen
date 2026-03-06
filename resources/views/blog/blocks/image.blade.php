<div class="mb-6">
    <img src="{{ $block['url'] }}" alt="{{ $block['alt'] ?? '' }}" class="w-full rounded-lg">
    @if(isset($block['caption']))
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 text-center">{{ $block['caption'] }}</p>
    @endif
</div>
