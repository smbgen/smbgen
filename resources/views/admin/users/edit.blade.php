@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="admin-page-header">
        <div>
            <div class="mb-3">
                <a href="{{ route('admin.users.index') }}" class="text-purple-400 hover:text-purple-300 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
            </div>
            <h1 class="admin-page-title">Edit User</h1>
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
        <form id="user-edit-form" action="{{ route('admin.users.update', $user) }}" method="POST">
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

            <div class="form-group">
                <label for="role" class="form-label">
                    Role <span class="text-red-500">*</span>
                </label>
                <select 
                    name="role" 
                    id="role" 
                    class="form-select"
                    required
                    @if($user->id === auth()->id()) disabled title="You cannot change your own role" @endif
                >
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                    <option value="client" {{ old('role', $user->role) === 'client' ? 'selected' : '' }}>Client</option>
                    <option value="company_administrator" {{ old('role', $user->role) === 'company_administrator' ? 'selected' : '' }}>Administrator</option>
                </select>
                @if($user->id === auth()->id())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <p class="form-help text-yellow-400">
                        <i class="fas fa-info-circle mr-1"></i>You cannot change your own role
                    </p>
                @else
                    <p class="form-help">
                        <strong>User:</strong> Standard access<br>
                        <strong>Administrator:</strong> Full system access
                    </p>
                @endif
            </div>

            @if($user->id !== auth()->id() && $user->role !== 'company_administrator')
                <div class="bg-yellow-900/20 border border-yellow-500 rounded-lg p-4 mb-6">
                    <h4 class="text-yellow-300 font-semibold mb-2 flex items-center">
                        <i class="fas fa-level-up-alt mr-2"></i>
                        Quick Elevation
                    </h4>
                    <p class="text-yellow-100 text-sm mb-3">
                        Instantly promote this user to <strong>Company Administrator</strong> with full system access.
                    </p>
                    <button type="submit" form="elevate-form" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition-colors" onclick="return confirm('Are you sure you want to elevate {{ addslashes($user->name) }} to Administrator? This will grant full system access.');">
                        <i class="fas fa-level-up-alt mr-2"></i>
                        Elevate to Administrator
                    </button>
                </div>
            @elseif($user->role === 'company_administrator' && $user->id !== auth()->id())
                <div class="bg-purple-900/20 border border-purple-500 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-user-shield text-purple-400 text-2xl mr-3"></i>
                        <div>
                            <h4 class="text-purple-300 font-semibold">Administrator Account</h4>
                            <p class="text-purple-100 text-sm">This user has full system access.</p>
                        </div>
                    </div>
                </div>
            @endif

            <hr class="border-gray-700 my-6">

            <div class="form-group">
                <h3 class="text-lg font-semibold text-gray-100 mb-3">Change Password (Optional)</h3>
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

            <div class="bg-gray-900/50 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-semibold text-gray-300 mb-2">Account Information</h4>
                <div class="text-sm text-gray-400 space-y-1">
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
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>

        @if($user->id !== auth()->id() && $user->role !== 'company_administrator')
            <form id="elevate-form" action="{{ route('admin.users.elevate', $user) }}" method="POST" class="hidden">
                @csrf
            </form>
        @endif

        @if($user->id !== auth()->id())
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="mt-6">
                @csrf
                @method('DELETE')
                <button 
                    type="submit" 
                    class="btn-danger"
                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                >
                    <i class="fas fa-trash mr-2"></i>Delete User
                </button>
            </form>
        @endif
        </div>
    </div>
</div>
@endsection
