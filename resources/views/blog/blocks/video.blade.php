<div class="mb-6 aspect-video">
    @if(isset($block['platform']) && $block['platform'] === 'youtube')
        @php
            preg_match('/[?&]v=([^&]+)/', $block['url'], $matches);
            $videoId = $matches[1] ?? '';
        @endphp
        <iframe class="w-full h-full rounded-lg" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
    @else
        <video controls class="w-full h-full rounded-lg">
            <source src="{{ $block['url'] }}" type="video/mp4">
        </video>
    @endif
</div>
