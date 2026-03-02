@extends('layouts.super-admin')

@section('content')
<div class="py-6">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h2 class="admin-page-title">Super Admin Management</h2>
            <p class="admin-page-subtitle">Manage super administrator accounts</p>
        </div>
        <div class="action-buttons">
            @if(\Illuminate\Support\Facades\Route::has('super-admin.users.create'))
                <a href="{{ route('super-admin.users.create') }}" class="btn-primary">
                    <i class="fas fa-crown mr-2"></i>Add Super Admin
                </a>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('status'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @php
        $totalUsers = $users->total();
        $verifiedCount = \App\Models\User::where('is_super_admin', true)->whereNotNull('email_verified_at')->count();
    @endphp

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Total Super Admins</p>
                    <p class="text-white text-3xl font-bold mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-crown text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Verified</p>
                    <p class="text-white text-3xl font-bold mt-1">{{ $verifiedCount }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    @if($users->count() > 0)
        <div class="admin-card">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <!-- User Column with Avatar -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @php
                                                $initials = collect(explode(' ', $user->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('');
                                                $colors = ['bg-red-500', 'bg-pink-500', 'bg-orange-500', 'bg-yellow-500', 'bg-purple-500', 'bg-indigo-500'];
                                                $colorIndex = ord($initials[0] ?? 'A') % count($colors);
                                            @endphp
                                            <div class="w-10 h-10 rounded-full {{ $colors[$colorIndex] }} flex items-center justify-center text-white font-bold text-sm">
                                                {{ $initials }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                                {{ $user->name }}
                                                @if($user->id === auth()->id())
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-red-500 text-white">
                                                        <i class="fas fa-star mr-1"></i>You
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email Column -->
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 dark:text-gray-100">{{ $user->email }}</div>
                                    @if($user->email_verified_at)
                                        <div class="text-xs text-green-400 mt-1">
                                            <i class="fas fa-check-circle mr-1"></i>Verified
                                        </div>
                                    @else
                                        <div class="text-xs text-yellow-400 mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Not verified
                                        </div>
                                    @endif
                                </td>

                                <!-- Status Column -->
                                <td class="px-6 py-4">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-green-900/30 text-green-300">
                                            <i class="fas fa-circle text-green-400 mr-1.5 text-[6px]"></i>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-900/30 text-yellow-300">
                                            <i class="fas fa-circle text-yellow-400 mr-1.5 text-[6px]"></i>Pending
                                        </span>
                                    @endif
                                </td>

                                <!-- Created Column -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                                </td>

                                <!-- Actions Column -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(\Illuminate\Support\Facades\Route::has('super-admin.users.edit'))
                                            <a href="{{ route('super-admin.users.edit', $user) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors"
                                               title="Edit User">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                        @endif
                                        
                                        @if(!$user->email_verified_at && \Illuminate\Support\Facades\Route::has('super-admin.users.verify'))
                                            <form action="{{ route('super-admin.users.verify', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors"
                                                        title="Verify Email">
                                                    <i class="fas fa-check-circle mr-1"></i>Verify
                                                </button>
                                            </form>
                                        @elseif(\Illuminate\Support\Facades\Route::has('super-admin.users.unverify'))
                                            <form action="{{ route('super-admin.users.unverify', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors"
                                                        onclick="return confirm('Remove email verification from {{ addslashes($user->name) }}?')"
                                                        title="Unverify Email">
                                                    <i class="fas fa-times-circle mr-1"></i>Unverify
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($user->id !== auth()->id() && \Illuminate\Support\Facades\Route::has('super-admin.users.destroy'))
                                            <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-800 hover:bg-red-900 text-white text-xs font-medium rounded-lg transition-colors"
                                                        onclick="return confirm('Are you sure you want to delete {{ addslashes($user->name) }}? This action cannot be undone.')"
                                                        title="Delete User">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(method_exists($users, 'hasPages') && $users->hasPages())
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    @else
        <div class="bg-gray-800 rounded-xl shadow-xl p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="bg-gray-700/50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-crown text-gray-400 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-100 mb-2">No Super Admins Found</h3>
                <p class="text-gray-400 mb-6">Get started by creating your first super administrator account.</p>
                @if(\Illuminate\Support\Facades\Route::has('super-admin.users.create'))
                    <a href="{{ route('super-admin.users.create') }}" class="btn-primary inline-flex items-center">
                        <i class="fas fa-crown mr-2"></i>Create Your First Super Admin
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
