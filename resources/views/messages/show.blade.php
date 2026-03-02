@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-100 mb-1">{{ $message->subject ?? 'No Subject' }}</h2>
            <p class="text-gray-400">
                <i class="fas fa-comments mr-2"></i>Conversation with 
                @if($message->sender_id === Auth::id())
                    {{ $message->recipient->name ?? 'Unknown' }}
                @else
                    {{ $message->sender->name ?? 'Unknown' }}
                @endif
            </p>
        </div>
        <a href="{{ route('messages.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Messages') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-900/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Message Thread -->
    <div class="space-y-4 mb-6">
        @foreach($thread as $msg)
            <div class="card {{ $msg->sender_id === Auth::id() ? 'ml-8' : 'mr-8' }}">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full {{ $msg->sender_id === Auth::id() ? 'bg-blue-600' : 'bg-green-600' }} flex items-center justify-center text-white font-semibold">
                                {{ substr($msg->sender->name ?? 'U', 0, 1) }}
                            </div>
                        </div>
                        
                        <!-- Message Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-100">{{ $msg->sender->name ?? 'Unknown' }}</span>
                                    @if($msg->sender_id === Auth::id())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-900/30 text-blue-300 border border-blue-500">
                                            You
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{ $msg->created_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                            
                            @if($msg->subject && $msg->id === $thread->first()->id)
                                <div class="text-sm text-gray-400 mb-2">
                                    <i class="fas fa-tag mr-1"></i>{{ $msg->subject }}
                                </div>
                            @endif
                            
                            <div class="prose prose-invert max-w-none">
                                <div class="whitespace-pre-wrap text-gray-200 leading-relaxed">{{ $msg->body }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Reply Form -->
    <div class="card">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-gray-100 mb-0">
                <i class="fas fa-reply mr-2"></i>{{ __('Reply') }}
            </h3>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('messages.reply', $message) }}">
                @csrf

                <div class="mb-6">
                    <label for="body" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Your Reply') }}</label>
                    <textarea id="body" name="body" rows="6" 
                              class="form-input w-full bg-gray-800 border-gray-700 text-gray-200 focus:border-blue-500 focus:ring-blue-500" 
                              placeholder="Type your reply..." required></textarea>
                    @error('body')
                        <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary" onclick="console.log('Form submitted')">
                        <i class="fas fa-paper-plane mr-2"></i>{{ __('Send Reply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="reply"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submission detected');
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            console.log('Form data:', new FormData(form));
        });
    }
});
</script>
@endsection

