@extends('layouts.super-admin')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="mb-6">
        @if(\Illuminate\Support\Facades\Route::has('super-admin.users.index'))
            <a href="{{ route('super-admin.users.index') }}" class="text-red-400 hover:text-red-300">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
        @endif
    </div>

    <div class="bg-gray-800 rounded-lg shadow-xl p-6">
        <h2 class="text-2xl font-bold text-gray-100 mb-6">Create New Super Admin</h2>

        @if($errors->any())
            <div class="bg-red-900/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(\Illuminate\Support\Facades\Route::has('super-admin.users.store'))
            <form action="{{ route('super-admin.users.store') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-100"
                        required
                    >
                </div>

                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-100"
                        required
                    >
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-100"
                        required
                    >
                    <p class="text-sm text-gray-400 mt-1">Minimum 8 characters</p>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-100"
                        required
                    >
                </div>

                <div class="bg-red-900/20 border border-red-500 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-crown text-red-400 text-xl mr-3 mt-1"></i>
                        <div>
                            <h4 class="text-red-300 font-semibold mb-1">Super Administrator Access</h4>
                            <p class="text-red-100 text-sm">
                                This user will have full platform control including tenant management, 
                                system diagnostics, and all administrative functions.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    @if(\Illuminate\Support\Facades\Route::has('super-admin.users.index'))
                        <a href="{{ route('super-admin.users.index') }}" class="btn-secondary">Cancel</a>
                    @endif
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-crown mr-2"></i>Create Super Admin
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
