@extends('layouts.admin')

@section('title', 'LinkedIn')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="fab fa-linkedin text-blue-500 text-3xl"></i> LinkedIn
            </h1>
            <p class="text-gray-400 text-sm mt-1">Manage your LinkedIn business pages and scheduled posts.</p>
        </div>
        <a href="{{ route('admin.linkedin.auth.redirect') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-700 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus"></i> Connect Account
        </a>
    </div>

    {{-- Session messages --}}
    @if(session('status'))
        <div class="bg-green-900/40 border border-green-700 text-green-300 rounded-lg px-4 py-3 text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-900/40 border border-red-700 text-red-300 rounded-lg px-4 py-3 text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    @if($accounts->isEmpty())
        {{-- Empty state --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-12 text-center">
            <i class="fab fa-linkedin text-gray-600 text-6xl mb-4"></i>
            <h2 class="text-xl font-semibold text-white mb-2">No LinkedIn accounts connected</h2>
            <p class="text-gray-400 mb-6 max-w-md mx-auto">
                Connect your LinkedIn business page to start scheduling and publishing posts directly from this dashboard.
            </p>
            <a href="{{ route('admin.linkedin.auth.redirect') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-700 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                <i class="fab fa-linkedin"></i> Connect LinkedIn Account
            </a>
        </div>
    @else
        {{-- Connected accounts grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($accounts as $account)
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center">
                                <i class="fab fa-linkedin-in text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ $account->page_name ?? $account->account_name }}</p>
                                <p class="text-gray-400 text-xs">{{ $account->account_name }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs {{ $account->active ? 'bg-green-900/50 text-green-400' : 'bg-gray-700 text-gray-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $account->active ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                            {{ $account->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-gray-700/50 rounded-lg p-2">
                            <p class="text-white font-semibold">{{ $account->social_posts_count }}</p>
                            <p class="text-gray-400 text-xs">Total</p>
                        </div>
                        <div class="bg-gray-700/50 rounded-lg p-2">
                            <p class="text-blue-400 font-semibold">{{ $account->scheduled_posts_count }}</p>
                            <p class="text-gray-400 text-xs">Scheduled</p>
                        </div>
                        <div class="bg-gray-700/50 rounded-lg p-2">
                            <p class="text-green-400 font-semibold">{{ $account->published_posts_count }}</p>
                            <p class="text-gray-400 text-xs">Published</p>
                        </div>
                    </div>

                    {{-- Token expiry warning --}}
                    @if($account->isTokenExpired())
                        <div class="bg-yellow-900/30 border border-yellow-700 rounded-lg px-3 py-2 text-xs text-yellow-400 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            Token expired — <a href="{{ route('admin.linkedin.auth.redirect') }}" class="underline hover:text-yellow-300">reconnect</a>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-1 border-t border-gray-700">
                        <a href="{{ route('admin.linkedin.posts.create') }}"
                            class="flex-1 text-center px-3 py-1.5 bg-blue-700 hover:bg-blue-600 text-white rounded-lg text-xs font-medium transition-colors">
                            <i class="fas fa-pen mr-1"></i> New Post
                        </a>
                        <a href="{{ route('admin.linkedin.posts.index') }}"
                            class="flex-1 text-center px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-xs font-medium transition-colors">
                            <i class="fas fa-list mr-1"></i> Posts
                        </a>
                        <form method="POST" action="{{ route('admin.linkedin.disconnect', $account) }}" onsubmit="return confirm('Disconnect this LinkedIn account?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-900/50 hover:bg-red-800 text-red-400 hover:text-red-300 rounded-lg text-xs font-medium transition-colors">
                                <i class="fas fa-unlink"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Quick link to posts --}}
        <div class="text-center pt-2">
            <a href="{{ route('admin.linkedin.posts.index') }}" class="text-blue-400 hover:text-blue-300 text-sm">
                View all posts <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    @endif

</div>
@endsection
