@props(['messages'])

@if($messages->count() > 0)
<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
            <div class="bg-gradient-to-r from-pink-600 dark:from-pink-500 to-rose-600 dark:to-rose-500 rounded-xl p-2">
                <i class="fas fa-comments text-white"></i>
            </div>
            Recent Messages
            @if($messages->where('is_read', false)->count() > 0)
            <span class="bg-red-600 dark:bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full animate-pulse">
                {{ $messages->where('is_read', false)->count() }} New
            </span>
            @endif
        </h2>
        <a href="{{ route('messages.index') }}" class="bg-pink-600 hover:bg-pink-700 dark:bg-pink-600 dark:hover:bg-pink-700 text-white px-4 py-2 rounded-lg transition-colors font-medium text-sm">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($messages as $message)
        <a href="{{ route('messages.show', $message) }}" class="block p-4 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all hover:scale-105 {{ !$message->is_read ? 'ring-2 ring-blue-500 dark:ring-blue-400' : '' }}">
            <div class="flex items-start gap-3 mb-2">
                <div class="bg-gradient-to-br from-pink-500 to-rose-500 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold flex-shrink-0">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-gray-900 dark:text-white font-semibold truncate">{{ $message->subject }}</span>
                        @if(!$message->read)
                        <span class="bg-blue-600 dark:bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full whitespace-nowrap">New</span>
                        @endif
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">{{ Str::limit($message->body, 80) }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-300 dark:border-gray-600">
                <span class="text-gray-500 dark:text-gray-400 text-xs">{{ $message->created_at->diffForHumans() }}</span>
                @if(!$message->read)
                <span class="text-blue-600 dark:text-blue-400 text-xs font-medium">Unread</span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
