<div class="mb-6">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @php
            $images = explode("\n", $block['images'] ?? '');
        @endphp
        @foreach($images as $imageUrl)
            @if(trim($imageUrl))
                <img src="{{ trim($imageUrl) }}" alt="Gallery image" class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-75 transition">
            @endif
        @endforeach
    </div>
</div>
