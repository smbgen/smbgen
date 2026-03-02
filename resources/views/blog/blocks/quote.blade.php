<blockquote class="border-l-4 border-gray-300 dark:border-gray-600 pl-4 py-2 italic mb-6">
    <p class="text-lg">{{ $block['content'] }}</p>
    @if(isset($block['author']))
        <footer class="text-sm text-gray-600 dark:text-gray-400 mt-2">— {{ $block['author'] }}</footer>
    @endif
</blockquote>
