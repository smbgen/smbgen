{{-- Capability strip — sits between hero and first section --}}
<div class="bg-blue-600 py-4 px-6 overflow-x-auto">
    <div class="max-w-6xl mx-auto flex items-center justify-between gap-6 min-w-max md:min-w-0">
        @foreach([
            'Rapid App Dev',
            'Cloud Delivery',
            'Content Management',
            'Expert Design',
            'Marketing Automation',
            'Growth Marketing',
        ] as $i => $cap)
            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-widest whitespace-nowrap {{ $i === 0 ? 'text-white' : 'text-white/70' }}">
                <span class="text-blue-300">&#10022;</span>
                {{ $cap }}
            </div>
        @endforeach
    </div>
</div>
