@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Post #{{ $post->id }}</h1>
            <p class="admin-page-subtitle">Publish history and target status</p>
        </div>
        <div class="flex gap-2">
            @if (in_array($post->status, ['draft', 'failed']))
                <a href="{{ route('admin.social.posts.edit', $post) }}" class="btn-secondary">
                    <i class="fas fa-pen mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('admin.social.posts.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Post details --}}
        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Caption</h2>
                    @php
                        $statusBadge = [
                            'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                            'publishing' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'published' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            'cancelled' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge[$post->status] ?? '' }}">
                        {{ ucfirst($post->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $post->caption }}</p>
            </div>

            @if ($post->media->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-3">
                        Media <span class="text-gray-400 font-normal">({{ $post->media->count() }})</span>
                    </h2>
                    <div class="flex gap-2 flex-wrap">
                        @foreach ($post->media as $media)
                            @php $url = $media->getUrl(); @endphp
                            @if ($url)
                                <img src="{{ $url }}" alt="{{ $media->caption ?? '' }}"
                                     class="w-24 h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Per-platform targets --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Platform Targets</h2>
                <div class="space-y-3">
                    @foreach ($post->targets as $target)
                        @php
                            $account = $target->socialAccount;
                            $targetBadge = [
                                'pending' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                'publishing' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'published' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'skipped' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                            ];
                        @endphp
                        <div class="flex items-start justify-between gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <i class="{{ $account?->platformIcon() ?? 'fas fa-share-alt' }} text-gray-600 dark:text-gray-400 text-lg w-5 text-center"></i>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $account?->account_name ?? 'Deleted account' }}</p>
                                    @if ($target->platform_post_url)
                                        <a href="{{ $target->platform_post_url }}" target="_blank"
                                           class="text-xs text-blue-600 dark:text-blue-400 hover:underline truncate block">
                                            View live post →
                                        </a>
                                    @elseif ($target->last_error)
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">{{ $target->last_error }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $targetBadge[$target->status] ?? '' }}">
                                    {{ ucfirst($target->status) }}
                                    @if ($target->attempt_count > 0)
                                        <span class="ml-1 opacity-60">({{ $target->attempt_count }}×)</span>
                                    @endif
                                </span>
                                @if ($target->canRetry())
                                    <form action="{{ route('admin.social.targets.retry', $target) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                            Retry
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Meta sidebar --}}
        <div class="space-y-5">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Details</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="text-gray-900 dark:text-white">{{ $post->created_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @if ($post->scheduled_at)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Scheduled for</dt>
                            <dd class="text-gray-900 dark:text-white">{{ $post->scheduled_at->format('M j, Y g:i A') }}</dd>
                        </div>
                    @endif
                    @if ($post->published_at)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Published</dt>
                            <dd class="text-green-600 dark:text-green-400">{{ $post->published_at->format('M j, Y g:i A') }}</dd>
                        </div>
                    @endif
                    @if ($post->requires_approval)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Approval</dt>
                            <dd class="{{ $post->approved_at ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $post->approved_at ? 'Approved by '.$post->approvedBy?->name : 'Pending approval' }}
                            </dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Created by</dt>
                        <dd class="text-gray-900 dark:text-white">{{ $post->user?->name }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 space-y-2">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Actions</h3>

                @if ($post->status === 'scheduled' && $post->requires_approval && ! $post->approved_at)
                    <form action="{{ route('admin.social.posts.approve', $post) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary w-full text-sm">
                            <i class="fas fa-check mr-2"></i>Approve Post
                        </button>
                    </form>
                @endif

                @if ($post->status === 'scheduled')
                    <form action="{{ route('admin.social.posts.cancel', $post) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary w-full text-sm"
                                onclick="return confirm('Cancel this scheduled post?')">
                            <i class="fas fa-ban mr-2"></i>Cancel Post
                        </button>
                    </form>
                @endif

                @if (in_array($post->status, ['draft', 'cancelled']))
                    <form action="{{ route('admin.social.posts.destroy', $post) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full text-sm py-2 px-4 rounded-lg border border-red-200 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                onclick="return confirm('Permanently delete this post?')">
                            <i class="fas fa-trash mr-2"></i>Delete Post
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
