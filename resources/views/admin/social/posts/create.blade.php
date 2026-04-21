@extends('layouts.admin')

@section('content')
<div x-data="{
    selectedImages: [],
    toggleImage(id) {
        const idx = this.selectedImages.indexOf(id);
        if (idx === -1) {
            this.selectedImages.push(id);
        } else {
            this.selectedImages.splice(idx, 1);
        }
    },
    isSelected(id) { return this.selectedImages.includes(id); }
}" class="py-6">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Compose Post</h1>
            <p class="admin-page-subtitle">Write and schedule a social media post</p>
        </div>
        <a href="{{ route('admin.social.posts.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Posts
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-error mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($accounts->isEmpty())
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-6 mb-6">
            <p class="text-amber-800 dark:text-amber-200 font-medium">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                No connected accounts found.
                <a href="{{ route('admin.social.accounts.create') }}" class="underline ml-1">Add an account first.</a>
            </p>
        </div>
    @endif

    <form action="{{ route('admin.social.posts.store') }}" method="POST">
        @csrf

        {{-- Hidden fields for selected images --}}
        <template x-for="id in selectedImages" :key="id">
            <input type="hidden" name="cms_image_ids[]" :value="id">
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main composer --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Caption --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <label class="form-label">Caption <span class="text-red-500">*</span></label>
                    <textarea name="caption" rows="5" required
                              class="form-input resize-none"
                              placeholder="Write your post caption…&#10;&#10;LinkedIn: up to 3000 chars&#10;Facebook: up to 63,206 chars&#10;Instagram: up to 2200 chars (recommended)">{{ old('caption') }}</textarea>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Instagram recommends keeping captions under 2200 characters. LinkedIn allows up to 3000.
                    </p>
                </div>

                {{-- Job Photos / Media Picker --}}
                @if ($recentImages->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Job Photos / Media</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedImages.length + ' selected'"></span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                            Select images from your CMS library. Up to 10 images per post (Meta), 9 for LinkedIn.
                        </p>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach ($recentImages as $image)
                                <button type="button"
                                        @click="toggleImage({{ $image->id }})"
                                        :class="isSelected({{ $image->id }}) ? 'ring-2 ring-blue-500 ring-offset-1' : 'ring-1 ring-gray-200 dark:ring-gray-700'"
                                        class="relative rounded-lg overflow-hidden aspect-square focus:outline-none transition-all">
                                    <img src="{{ $image->getUrl() }}"
                                         alt="{{ $image->alt_text ?: $image->original_name }}"
                                         class="w-full h-full object-cover">
                                    <div x-show="isSelected({{ $image->id }})"
                                         class="absolute inset-0 bg-blue-500/20 flex items-center justify-center">
                                        <i class="fas fa-check-circle text-white text-2xl drop-shadow"></i>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">

                {{-- Target Platforms --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Publish To</h3>
                    @if ($accounts->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No accounts available.</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($accounts as $account)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="account_ids[]" value="{{ $account->id }}"
                                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600"
                                           {{ is_array(old('account_ids')) && in_array($account->id, old('account_ids')) ? 'checked' : '' }}>
                                    <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <i class="{{ $account->platformIcon() }}"></i>
                                        {{ $account->account_name }}
                                        <span class="text-xs text-gray-400">({{ $account->platformLabel() }})</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Schedule --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Schedule</h3>
                    <label class="form-label text-xs">Publish At (leave blank to save as draft)</label>
                    <input type="datetime-local" name="scheduled_at"
                           value="{{ old('scheduled_at') }}"
                           class="form-input text-sm"
                           min="{{ now()->addMinutes(2)->format('Y-m-d\TH:i') }}">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Times are in {{ config('app.timezone') }}.</p>
                </div>

                {{-- Approval gate --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Approval</h3>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="requires_approval" value="1"
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600"
                               {{ old('requires_approval') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Require approval before publishing</span>
                    </label>
                </div>

                {{-- Submit --}}
                <div class="flex flex-col gap-2">
                    <button type="submit" class="btn-primary w-full" {{ $accounts->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-calendar-check mr-2"></i>Save Post
                    </button>
                    <a href="{{ route('admin.social.posts.index') }}" class="btn-secondary w-full text-center">
                        Cancel
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
