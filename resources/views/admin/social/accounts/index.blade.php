@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Connected Accounts</h1>
            <p class="admin-page-subtitle">Manage your LinkedIn, Facebook, and Instagram connections</p>
        </div>
        <a href="{{ route('admin.social.accounts.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Add Account
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if ($accounts->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-4">
                <i class="fas fa-share-alt text-blue-600 dark:text-blue-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No accounts connected yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Connect your social media accounts to start scheduling posts.</p>
            <a href="{{ route('admin.social.accounts.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Add First Account
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($accounts as $account)
                @php
                    $statusColors = [
                        'connected' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'error' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        'revoked' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                    ];
                    $statusColor = $statusColors[$account->connection_status] ?? $statusColors['error'];

                    $platformColors = [
                        'facebook' => 'bg-blue-600',
                        'instagram' => 'bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400',
                        'linkedin' => 'bg-blue-700',
                    ];
                    $platformColor = $platformColors[$account->platform] ?? 'bg-gray-600';
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="{{ $platformColor }} w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm">
                                    <i class="{{ $account->platformIcon() }} text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $account->account_name }}</h3>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $account->platformLabel() }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ ucfirst($account->connection_status) }}
                            </span>
                        </div>

                        @if ($account->account_url)
                            <a href="{{ $account->account_url }}" target="_blank" rel="noopener noreferrer"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline truncate block mb-3">
                                <i class="fas fa-external-link-alt mr-1"></i>{{ $account->account_url }}
                            </a>
                        @endif

                        @if ($account->last_error && $account->connection_status !== 'connected')
                            <p class="text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded p-2 mb-3">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ Str::limit($account->last_error, 100) }}
                            </p>
                        @endif

                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                            @if ($account->last_used_at)
                                <span><i class="fas fa-clock mr-1"></i>Last used {{ $account->last_used_at->diffForHumans() }}</span>
                            @else
                                <span class="text-gray-400">Never used</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <form action="{{ route('admin.social.accounts.toggle', $account) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full text-xs py-1.5 rounded-lg border {{ $account->active ? 'border-amber-300 text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/20' : 'border-green-300 text-green-700 dark:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/20' }} transition-colors">
                                    {{ $account->active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.social.accounts.destroy', $account) }}" method="POST"
                                  onsubmit="return confirm('Remove this account? Existing scheduled posts targeting it will not publish.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs py-1.5 px-3 rounded-lg border border-red-200 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
