@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">SIGNAL — Social Automation</h1>
            <p class="admin-page-subtitle">Schedule and manage social media posts across all platforms</p>
        </div>
        <div class="action-buttons">
            <button onclick="document.getElementById('modal-create-post').classList.remove('hidden')" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>New Post
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Total Posts</p>
            <p class="text-3xl font-black text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Scheduled</p>
            <p class="text-3xl font-black text-blue-400">{{ $stats['scheduled'] }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold mb-1">Published</p>
            <p class="text-3xl font-black text-emerald-400">{{ $stats['published'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.signal.index') }}" class="flex flex-wrap gap-3">
        <select name="platform" class="form-select text-sm" onchange="this.form.submit()">
            <option value="">All Platforms</option>
            @foreach($platforms as $platform)
                <option value="{{ $platform->value }}" {{ request('platform') === $platform->value ? 'selected' : '' }}>
                    {{ $platform->label() }}
                </option>
            @endforeach
        </select>
        <select name="status" class="form-select text-sm" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                    {{ ucfirst($status->value) }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- Posts Table -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left px-5 py-3 text-gray-400 font-bold uppercase tracking-wider text-xs">Platform</th>
                    <th class="text-left px-5 py-3 text-gray-400 font-bold uppercase tracking-wider text-xs">Content</th>
                    <th class="text-left px-5 py-3 text-gray-400 font-bold uppercase tracking-wider text-xs">Status</th>
                    <th class="text-left px-5 py-3 text-gray-400 font-bold uppercase tracking-wider text-xs">Scheduled</th>
                    <th class="text-left px-5 py-3 text-gray-400 font-bold uppercase tracking-wider text-xs">Client</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($posts as $post)
                    <tr class="hover:bg-gray-750 transition-colors">
                        <td class="px-5 py-4">
                            <span class="text-xs font-black uppercase tracking-widest text-violet-400">{{ $post->platform->label() }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-300 max-w-xs truncate">{{ $post->content }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                {{ match($post->status->value) {
                                    'published' => 'bg-emerald-900/50 text-emerald-400',
                                    'scheduled' => 'bg-blue-900/50 text-blue-400',
                                    'failed'    => 'bg-red-900/50 text-red-400',
                                    default     => 'bg-gray-700 text-gray-400',
                                } }}">
                                {{ $post->status->value }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-400 text-xs">
                            {{ $post->scheduled_at?->format('M d, Y H:i') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-gray-400 text-xs">{{ $post->client?->name ?? '—' }}</td>
                        <td class="px-5 py-4 text-right">
                            <form method="POST" action="{{ route('admin.signal.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-bold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-500">No posts yet. Create your first post above.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-700">{{ $posts->links() }}</div>
    </div>
</div>

<!-- Create Post Modal -->
<div id="modal-create-post" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8 w-full max-w-lg">
        <h3 class="text-white font-black text-xl mb-6">New Social Post</h3>
        <form method="POST" action="{{ route('admin.signal.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Platform</label>
                    <select name="platform" class="form-select w-full" required>
                        @foreach($platforms as $platform)
                            <option value="{{ $platform->value }}">{{ $platform->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="4" class="form-input w-full" maxlength="3000" placeholder="Write your post..." required></textarea>
                </div>
                <div>
                    <label class="form-label">Schedule (optional)</label>
                    <input type="datetime-local" name="scheduled_at" class="form-input w-full">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-primary flex-1">Save Post</button>
                <button type="button" onclick="document.getElementById('modal-create-post').classList.add('hidden')" class="btn-secondary flex-1">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
