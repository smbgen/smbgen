<div class="mb-6 p-4 rounded-lg border-l-4
    @if(isset($block['style']))
        @if($block['style'] === 'info') bg-blue-50 dark:bg-blue-900/20 border-blue-400
        @elseif($block['style'] === 'warning') bg-yellow-50 dark:bg-yellow-900/20 border-yellow-400
        @elseif($block['style'] === 'success') bg-green-50 dark:bg-green-900/20 border-green-400
        @else bg-gray-50 dark:bg-gray-800 border-gray-400
        @endif
    @else
        bg-gray-50 dark:bg-gray-800 border-gray-400
    @endif">
    {{ $block['content'] }}
</div>
