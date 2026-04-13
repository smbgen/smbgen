@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ __('New Message') }}</h2>
    
    <div class="card">
        <div class="p-6">
            @if($recipients->count() == 0)
                <div class="bg-blue-900/20 border border-blue-500 text-blue-300 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <h5 class="font-semibold">No Recipients Available</h5>
                            <p class="text-sm mt-1">There are no users available to message at this time.</p>
                        </div>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="recipient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('To') }}
                            <span class="text-gray-400 font-normal text-xs ml-2">({{ $recipients->count() }} recipients available)</span>
                        </label>
                        
                        <!-- Search Box -->
                        <input type="text" id="recipient_search" 
                               class="form-input w-full mb-2 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="Search by name or email...">
                        
                        <select id="recipient_id" name="recipient_id" size="8"
                                class="form-select w-full bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500" required>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient['id'] }}" 
                                        data-name="{{ strtolower($recipient['name'] ?? '') }}"
                                        data-email="{{ strtolower($recipient['email'] ?? '') }}"
                                        {{ old('recipient_id') == $recipient['id'] ? 'selected' : '' }}>
                                    {{ $recipient['name'] ?? 'Unknown' }} - {{ $recipient['email'] ?? 'N/A' }}
                                    @if(isset($recipient['company']) && $recipient['company'])
                                        ({{ $recipient['company'] }})
                                    @endif
                                    <span class="text-gray-500">[{{ $recipient['type'] ?? 'User' }}]</span>
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Double-click or press Enter to select</p>
                        @error('recipient_id')
                            <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <script>
                        // Search functionality
                        document.getElementById('recipient_search').addEventListener('input', function(e) {
                            const search = e.target.value.toLowerCase();
                            const select = document.getElementById('recipient_id');
                            const options = select.querySelectorAll('option');
                            
                            options.forEach(option => {
                                const name = option.dataset.name || '';
                                const email = option.dataset.email || '';
                                const matches = name.includes(search) || email.includes(search);
                                option.style.display = matches ? '' : 'none';
                            });
                        });

                        // Allow double-click to auto-focus next field
                        document.getElementById('recipient_id').addEventListener('dblclick', function() {
                            document.getElementById('subject').focus();
                        });
                    </script>

                    <div class="mb-6">
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Subject') }}</label>
                        <input id="subject" type="text" name="subject" value="{{ old('subject') }}" 
                               class="form-input w-full bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="Message subject (optional)">
                        @error('subject')
                            <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Message') }}</label>
                        <textarea id="body" name="body" rows="8" 
                                  class="form-input w-full bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500" 
                                  placeholder="Type your message here..." required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('messages.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>{{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>{{ __('Send Message') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
