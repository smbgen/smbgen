@extends('layouts.client')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="space-y-6">
        <div class="card">
            <div class="bg-gray-800 px-6 py-4 rounded-t-lg">
                <h2 class="text-xl font-semibold text-gray-100 mb-0">Profile</h2>
            </div>
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div class="bg-gray-800 px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-100 mb-0">Update Password</h3>
            </div>
            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card">
            <div class="bg-gray-800 px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-100 mb-0">Delete Account</h3>
            </div>
            <div class="p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

        {{-- Account Activity Section --}}
        <div class="card">
            <div class="bg-gray-800 px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-100 mb-0">Account Activity</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                        <div>
                            <p class="mb-1 font-semibold text-gray-100">Last Login</p>
                            <p class="text-sm text-gray-400 mb-0">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="mb-1 font-semibold text-gray-100">{{ $user->updated_at ? $user->updated_at->format('M j, Y') : 'N/A' }}</p>
                            <p class="text-sm text-gray-400 mb-0">{{ $user->updated_at ? $user->updated_at->format('g:i A') : '' }}</p>
                        </div>
                    </div>

                    @if($user->google_id)
                    <div class="flex justify-between items-center p-4 bg-green-900/20 rounded-lg">
                        <div>
                            <p class="mb-1 font-semibold text-green-400">Google OAuth Status</p>
                            <p class="text-sm text-green-400 mb-0">Connected and verified</p>
                        </div>
                        <div class="text-right">
                            <span class="text-green-400 text-2xl">✓</span>
                        </div>
                    </div>
                    @endif

                    <div class="flex justify-between items-center p-4 bg-blue-900/20 rounded-lg">
                        <div>
                            <p class="mb-1 font-semibold text-blue-400">Account Status</p>
                            <p class="text-sm text-blue-400 mb-0">{{ ucfirst($user->role ?? 'client') }} account</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
