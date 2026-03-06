@props([
    'code' => '500',
    'title' => 'Error',
    'message' => 'An error occurred.',
    'icon' => '⚠️',
    'color' => 'red',
    'suggestions' => [],
    'debug' => false,
    'exception' => null,
])

@php
    $bgColorClass = match($color) {
        'yellow' => 'bg-yellow-900',
        'blue' => 'bg-blue-900',
        'purple' => 'bg-purple-900',
        default => 'bg-red-900',
    };
    
    $borderColorClass = match($color) {
        'yellow' => 'border-yellow-800/50',
        'blue' => 'border-blue-800/50',
        'purple' => 'border-purple-800/50',
        default => 'border-red-800/50',
    };
    
    $textColorClass = match($color) {
        'yellow' => 'text-yellow-200',
        'blue' => 'text-blue-200',
        'purple' => 'text-purple-200',
        default => 'text-red-200',
    };
    
    $boxBgColorClass = match($color) {
        'yellow' => 'bg-yellow-800/50',
        'blue' => 'bg-blue-800/50',
        'purple' => 'bg-purple-800/50',
        default => 'bg-red-800/50',
    };
@endphp

<div class="container py-8">
    <div class="{{ $bgColorClass }} rounded-lg p-8 text-white">
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">{{ $icon }}</div>
            <h1 class="text-4xl font-bold">{{ $code }} — {{ $title }}</h1>
            <p class="mt-2 {{ $textColorClass }}">{{ $message }}</p>
        </div>

        {{-- Auto-redirect countdown --}}
        <div class="{{ $boxBgColorClass }} rounded-lg p-4 mb-6 text-center">
            <p class="font-semibold mb-2">
                <i class="fas fa-clock mr-2"></i>Redirecting to login in <span id="countdown">5</span> seconds...
            </p>
            <p class="text-sm opacity-75">Or click below to navigate manually</p>
        </div>

        @if($debug && $exception)
            <div class="{{ $boxBgColorClass }} rounded-lg p-4 mb-6">
                <h3 class="font-semibold mb-3 flex items-center">
                    <i class="fas fa-bug mr-2"></i>Debug Information
                </h3>
                <div class="space-y-2 text-sm">
                    <div><strong>Type:</strong> <code class="bg-opacity-50 bg-black px-2 py-1 rounded">{{ get_class($exception) }}</code></div>
                    <div><strong>Message:</strong> {{ $exception->getMessage() }}</div>
                    <div><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</div>
                </div>

                <details class="mt-4">
                    <summary class="cursor-pointer font-medium hover:opacity-80 transition-opacity">
                        <i class="fas fa-code mr-2"></i>Stack Trace (click to expand)
                    </summary>
                    <pre class="mt-3 p-3 bg-black bg-opacity-30 rounded text-xs overflow-auto" style="max-height:40vh">{{ $exception->getTraceAsString() }}</pre>
                </details>
            </div>
        @elseif(count($suggestions) > 0)
            <div class="{{ $boxBgColorClass }} rounded-lg p-4 mb-6">
                <h3 class="font-semibold mb-2 flex items-center">
                    <i class="fas fa-lightbulb mr-2"></i>What can you do?
                </h3>
                <ul class="space-y-1 text-sm opacity-90">
                    @foreach($suggestions as $suggestion)
                        <li>• {{ $suggestion }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}

        <div class="text-center">
            <a href="{{ route('login') }}" class="btn-primary mr-3">
                <i class="fas fa-sign-in-alt mr-2"></i>Go to Login
            </a>
            <a href="{{ url()->previous() ?: url('/') }}" class="btn-secondary mr-3">
                <i class="fas fa-arrow-left mr-2"></i>Go Back
            </a>
            <a href="/" class="btn-secondary">
                <i class="fas fa-home mr-2"></i>Home
            </a>
        </div>

        <div class="text-center mt-6 pt-4 {{ $borderColorClass }} border-t">
            <p class="opacity-70 text-xs">
                {{ config('app.name') }}
            </p>
        </div>
    </div>
</div>

<script>
    // Auto-redirect countdown
    let seconds = 5;
    const countdownElement = document.getElementById('countdown');
    
    const interval = setInterval(() => {
        seconds--;
        if (countdownElement) {
            countdownElement.textContent = seconds;
        }
        
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = '{{ route('login') }}';
        }
    }, 1000);
    
    // Clear interval if user navigates away manually
    window.addEventListener('beforeunload', () => {
        clearInterval(interval);
    });
</script>
