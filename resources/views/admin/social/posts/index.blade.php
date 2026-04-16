@extends('layouts.admin')

@section('content')
<div class="py-6">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Social Media Posts</h1>
            <p class="admin-page-subtitle">Schedule and manage posts to LinkedIn, Facebook, and Instagram</p>
        </div>
        <a href="{{ route('admin.social.posts.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>New Post
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Metrics bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        @php
            $metricConfig = [
                ['label' => 'Total', 'key' => 'total', 'icon' => 'fas fa-layer-group', 'color' => 'text-gray-600 dark:text-gray-400'],
                ['label' => 'Draft', 'key' => 'draft', 'icon' => 'fas fa-pen', 'color' => 'text-gray-500 dark:text-gray-400'],
                ['label' => 'Scheduled', 'key' => 'scheduled', 'icon' => 'fas fa-calendar-check', 'color' => 'text-blue-600 dark:text-blue-400'],
                ['label' => 'Published', 'key' => 'published', 'icon' => 'fas fa-check-circle', 'color' => 'text-green-600 dark:text-green-400'],
                ['label' => 'Failed', 'key' => 'failed', 'icon' => 'fas fa-exclamation-circle', 'color' => 'text-red-600 dark:text-red-400'],
            ];
        @endphp
        @foreach ($metricConfig as $m)
            <a href="{{ route('admin.social.posts.index', ['status' => $m['key'] === 'total' ? null : $m['key']]) }}"
               class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:shadow-md transition-shadow">
                <i class="{{ $m['icon'] }} {{ $m['color'] }} text-xl mb-1"></i>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metrics[$m['key']] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $m['label'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.social.posts.index') }}" class="mb-4 flex gap-2 flex-wrap">
        <select name="status" class="form-input text-sm py-1.5 w-auto" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
    </form>

    @if ($posts->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mb-4">
                <i class="fas fa-calendar-plus text-indigo-600 dark:text-indigo-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No posts yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Create your first social media post to get started.</p>
            <a href="{{ route('admin.social.posts.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Create Post
            </a>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Post</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Platforms</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scheduled / Published</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($posts as $post)
                        @php
                            $statusBadge = [
                                'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'publishing' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'published' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'cancelled' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $badge = $statusBadge[$post->status] ?? $statusBadge['draft'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.social.posts.show', $post) }}"
                                   class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 line-clamp-2 block max-w-xs">
                                    {{ Str::limit($post->caption, 80) }}
                                </a>
                                @if ($post->media->isNotEmpty())
                                    <span class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 block">
                                        <i class="fas fa-image mr-1"></i>{{ $post->media->count() }} media
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1 flex-wrap">
                                    @foreach ($post->targets as $target)
                                        @php
                                            $account = $target->socialAccount;
                                            $targetColors = [
                                                'published' => 'text-green-500',
                                                'failed' => 'text-red-500',
                                                'pending' => 'text-blue-400',
                                                'publishing' => 'text-yellow-500',
                                                'skipped' => 'text-gray-400',
                                            ];
                                            $iconColor = $targetColors[$target->status] ?? 'text-gray-400';
                                        @endphp
                                        <span title="{{ $account?->account_name }} – {{ ucfirst($target->status) }}"
                                              class="{{ $iconColor }}">
                                            <i class="{{ $account?->platformIcon() ?? 'fas fa-share-alt' }}"></i>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                    {{ ucfirst($post->status) }}
                                </span>
                                @if ($post->requires_approval && ! $post->approved_at)
                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                        Pending approval
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                @if ($post->published_at)
                                    <span class="text-green-600 dark:text-green-400">
                                        <i class="fas fa-check mr-1"></i>{{ $post->published_at->format('M j, Y g:i A') }}
                                    </span>
                                @elseif ($post->scheduled_at)
                                    {{ $post->scheduled_at->format('M j, Y g:i A') }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.social.posts.show', $post) }}"
                                       class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View</a>
                                    @if (in_array($post->status, ['draft', 'failed']))
                                        <a href="{{ route('admin.social.posts.edit', $post) }}"
                                           class="text-xs text-gray-600 dark:text-gray-400 hover:underline">Edit</a>
                                    @endif
                                    @if ($post->status === 'scheduled')
                                        <form action="{{ route('admin.social.posts.cancel', $post) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-amber-600 dark:text-amber-400 hover:underline"
                                                    onclick="return confirm('Cancel this scheduled post?')">Cancel</button>
                                        </form>
                                    @endif
                                    @if (in_array($post->status, ['draft', 'cancelled']))
                                        <form action="{{ route('admin.social.posts.destroy', $post) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline"
                                                    onclick="return confirm('Delete this post?')">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $posts->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
