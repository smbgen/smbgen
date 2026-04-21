@extends('layouts.admin')

@section('content')
<div x-data="{
    selectedImages: {{ json_encode($selectedImageIds) }},
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
            <h1 class="admin-page-title">Edit Post #{{ $post->id }}</h1>
            <p class="admin-page-subtitle">Update caption, media, and schedule</p>
        </div>
        <a href="{{ route('admin.social.posts.show', $post) }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back
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

    <form action="{{ route('admin.social.posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')

        <template x-for="id in selectedImages" :key="id">
            <input type="hidden" name="cms_image_ids[]" :value="id">
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <label class="form-label">Caption <span class="text-red-500">*</span></label>
                    <textarea name="caption" rows="5" required class="form-input resize-none">{{ old('caption', $post->caption) }}</textarea>
                </div>

                @if ($recentImages->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Job Photos / Media</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedImages.length + ' selected'"></span>
                        </div>
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

            <div class="space-y-5">

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Publish To</h3>
                    @php $selectedAccountIds = $post->targets->pluck('social_account_id')->toArray(); @endphp
                    <div class="space-y-2">
                        @foreach ($accounts as $account)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="account_ids[]" value="{{ $account->id }}"
                                       class="rounded border-gray-300 dark:border-gray-600 text-blue-600"
                                       {{ in_array($account->id, old('account_ids', $selectedAccountIds)) ? 'checked' : '' }}>
                                <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="{{ $account->platformIcon() }}"></i>
                                    {{ $account->account_name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Schedule</h3>
                    <input type="datetime-local" name="scheduled_at"
                           value="{{ old('scheduled_at', $post->scheduled_at?->format('Y-m-d\TH:i')) }}"
                           class="form-input text-sm"
                           min="{{ now()->addMinutes(2)->format('Y-m-d\TH:i') }}">
                </div>

                <div class="flex flex-col gap-2">
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.social.posts.show', $post) }}" class="btn-secondary w-full text-center">
                        Cancel
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
