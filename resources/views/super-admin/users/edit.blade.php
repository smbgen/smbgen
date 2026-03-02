@extends('layouts.super-admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="admin-page-header">
        <div>
            <div class="mb-3">
                @if(\Illuminate\Support\Facades\Route::has('super-admin.users.index'))
                    <a href="{{ route('super-admin.users.index') }}" class="text-red-400 hover:text-red-300 text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Users
                    </a>
                @endif
            </div>
            <h1 class="admin-page-title">Edit Super Admin</h1>
            <p class="admin-page-subtitle">{{ $user->name }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-card">
        <div class="admin-card-body">

        <div class="admin-card-body">
        @if(\Illuminate\Support\Facades\Route::has('super-admin.users.update'))
            <form action="{{ route('super-admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $user->name) }}"
                        class="form-input"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', $user->email) }}"
                        class="form-input"
                        required
                    >
                </div>

                <div class="bg-red-900/20 border border-red-500 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-crown text-red-400 text-2xl mr-3"></i>
                        <div>
                            <h4 class="text-red-300 font-semibold">Super Administrator Account</h4>
                            <p class="text-red-100 text-sm">This user has full platform control.</p>
                        </div>
                    </div>
                </div>

                <hr class=\"border-gray-300 dark:border-gray-700 my-6\">

                <div class="form-group">
                    <h3 class=\"text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3\">Change Password (Optional)</h3>
                    <p class="form-help mb-4">Leave blank to keep the current password</p>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            New Password
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-input"
                        >
                        <p class="form-help">Minimum 8 characters</p>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            Confirm New Password
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            class="form-input"
                        >
                    </div>
                </div>

                <hr class="border-gray-700 my-6">

                <div class="bg-gray-100 dark:bg-gray-900/50 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-300 mb-2">Account Information</h4>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <p><strong>Created:</strong> {{ $user->created_at->format('M j, Y g:i A') }}</p>
                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M j, Y g:i A') }}</p>
                        @if($user->email_verified_at)
                            <p><strong>Email Verified:</strong> {{ $user->email_verified_at->format('M j, Y g:i A') }}</p>
                        @else
                            <p class="text-yellow-400"><strong>Email Status:</strong> Not verified</p>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="action-buttons">
                        @if(\Illuminate\Support\Facades\Route::has('super-admin.users.index'))
                            <a href="{{ route('super-admin.users.index') }}" class="btn-secondary">Cancel</a>
                        @endif
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>
        @endif

        @if($user->id !== auth()->id() && \Illuminate\Support\Facades\Route::has('super-admin.users.destroy'))
            <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="mt-6">
                @csrf
                @method('DELETE')
                <button 
                    type="submit" 
                    class="btn-danger"
                    onclick="return confirm('Are you sure you want to delete this super admin? This action cannot be undone.')"
                >
                    <i class="fas fa-trash mr-2"></i>Delete Super Admin
                </button>
            </form>
        @endif
        </div>
    </div>
</div>
@endsection
