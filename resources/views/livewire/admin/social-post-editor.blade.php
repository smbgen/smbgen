<div class="space-y-6">

    {{-- Account selector --}}
    @if(count($accounts) > 1)
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-1">Post as</label>
        <select wire:model="socialAccountId" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @foreach($accounts as $account)
                <option value="{{ $account['id'] }}">
                    {{ $account['page_name'] ?? $account['account_name'] }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Title (optional) --}}
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-1">Title <span class="text-gray-500 text-xs">(internal only, not posted)</span></label>
        <input wire:model="title" type="text" placeholder="E.g. Q2 announcement…" maxlength="255"
            class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
    </div>

    {{-- Content editor / preview toggle --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-300">Post Content</label>
            <button type="button" wire:click="togglePreview"
                class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                {{ $previewMode ? '← Edit' : 'Preview →' }}
            </button>
        </div>

        @if($previewMode)
            {{-- Preview pane --}}
            <div class="bg-gray-700 border border-gray-600 rounded-lg p-4 min-h-[180px] text-white whitespace-pre-wrap text-sm leading-relaxed">
                @if(trim($content))
                    {{ $content }}
                @else
                    <span class="text-gray-500 italic">Nothing to preview yet…</span>
                @endif
            </div>
        @else
            {{-- Editor --}}
            <textarea wire:model.live="content" rows="8" maxlength="3000"
                placeholder="What do you want to share with your LinkedIn audience?"
                class="w-full bg-gray-700 border {{ $this->isOverLimit ? 'border-red-500' : 'border-gray-600' }} text-white rounded-lg px-3 py-2 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y text-sm leading-relaxed"></textarea>
        @endif

        {{-- Character counter --}}
        <div class="flex items-center justify-end mt-1">
            <span class="text-xs {{ $this->isOverLimit ? 'text-red-400 font-semibold' : ($this->charactersRemaining < 200 ? 'text-yellow-400' : 'text-gray-500') }}">
                {{ number_format($this->characterCount) }} / {{ number_format(\App\Livewire\Admin\SocialPostEditor::MAX_CHARACTERS) }}
            </span>
        </div>
    </div>

    {{-- Media uploads --}}
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-2">Images <span class="text-gray-500 text-xs">(JPEG/PNG/GIF, max 5MB each)</span></label>

        {{-- Existing media --}}
        @if(!empty($existingMediaPaths))
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($existingMediaPaths as $path)
                    <div class="relative group">
                        <img src="{{ Storage::url($path) }}" alt="Media" class="h-20 w-20 object-cover rounded-lg border border-gray-600" />
                        <button type="button" wire:click="removeMedia('{{ $path }}')"
                            class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            ×
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <input type="file" wire:model="mediaFiles" multiple accept="image/jpeg,image/png,image/gif"
            class="text-sm text-gray-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-gray-600 file:text-gray-200 hover:file:bg-gray-500 cursor-pointer" />

        <div wire:loading wire:target="mediaFiles" class="mt-1 text-xs text-blue-400">Uploading…</div>
    </div>

    {{-- Scheduling --}}
    <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-4">
        <h3 class="text-sm font-medium text-gray-300 mb-3 flex items-center gap-2">
            <i class="fas fa-clock text-blue-400"></i> Schedule
        </h3>
        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-xs text-gray-400 mb-1">Date</label>
                <input wire:model="scheduleDate" type="date" min="{{ now()->format('Y-m-d') }}"
                    class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="flex-1">
                <label class="block text-xs text-gray-400 mb-1">Time</label>
                <input wire:model="scheduleTime" type="time"
                    class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
            </div>
        </div>
    </div>

    {{-- Feedback messages --}}
    @if($errorMessage)
        <div class="bg-red-900/40 border border-red-700 rounded-lg px-4 py-3 text-sm text-red-300 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ $errorMessage }}
        </div>
    @endif

    @if($saved)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            class="bg-green-900/40 border border-green-700 rounded-lg px-4 py-3 text-sm text-green-300 flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ $savedMessage }}
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center gap-3 pt-2 border-t border-gray-700">
        <button type="button" wire:click="saveDraft" wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
            <i class="fas fa-save"></i>
            <span wire:loading.remove wire:target="saveDraft">Save Draft</span>
            <span wire:loading wire:target="saveDraft">Saving…</span>
        </button>

        <button type="button" wire:click="schedulePost" wire:loading.attr="disabled"
            @disabled($this->isOverLimit)
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
            <i class="fas fa-calendar-check"></i>
            <span wire:loading.remove wire:target="schedulePost">Schedule Post</span>
            <span wire:loading wire:target="schedulePost">Scheduling…</span>
        </button>

        @if($postId)
            <a href="{{ route('admin.linkedin.posts.publish', $postId) }}"
                onclick="return confirm('Publish this post to LinkedIn now?')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-medium transition-colors ml-auto">
                <i class="fab fa-linkedin"></i> Publish Now
            </a>
        @endif
    </div>

</div>
