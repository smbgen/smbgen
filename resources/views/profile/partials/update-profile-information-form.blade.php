<section class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <header class="mb-6">
        <div class="flex items-center mb-3">
            <div class="bg-blue-600 bg-opacity-20 rounded-full p-3 mr-3">
                <span class="text-white text-xl">👤</span>
            </div>
            <div>
                <h2 class="text-xl text-gray-900 dark:text-white mb-1 font-semibold">
                    {{ __('Profile Information') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-300 mb-0">
                    {{ __("Update your account's profile information and email address.") }}
                </p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    👤 {{ __('Full Name') }}
                </label>
                <div class="relative">
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" 
                        value="{{ old('name', $user->name) }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                        placeholder="Enter your full name" 
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <span class="text-gray-400">👤</span>
                    </div>
                </div>
                @error('name')
                    <div class="text-red-400 text-sm mt-2 flex items-center">
                        ⚠️ {{ $message }}
                    </div>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    📧 {{ __('Email Address') }}
                </label>
                <div class="relative">
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg" 
                        value="{{ old('email', $user->email) }}" 
                        readonly
                        autocomplete="username"
                        placeholder="Email address cannot be changed" 
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        @if($user->email_verified_at)
                            <span class="text-green-400" title="Email verified">✓</span>
                        @else
                            <span class="text-gray-400">📧</span>
                        @endif
                    </div>
                </div>
                @error('email')
                    <div class="text-red-400 text-sm mt-2 flex items-center">
                        ⚠️ {{ $message }}
                    </div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 p-4 bg-yellow-900/20 border border-yellow-600/25 rounded-lg">
                        <div class="flex items-start">
                            <span class="text-yellow-400 mr-2 mt-1">⚠️</span>
                            <div>
                                <p class="text-yellow-400 text-sm mb-1 font-medium">
                                    {{ __('Your email address is unverified.') }}
                                </p>
                                <button form="send-verification" class="text-blue-400 hover:text-blue-300 text-sm underline">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-3 p-4 bg-green-900/20 border border-green-600/25 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-green-400 mr-2">✓</span>
                                <p class="text-green-400 text-sm mb-0">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Account Information Display --}}
        <div class="mt-6 p-6 bg-gray-100 dark:bg-gray-700 rounded-lg">
            <div class="flex items-center mb-4">
                <div class="bg-blue-600 bg-opacity-20 rounded-full p-2 mr-3">
                    <span class="text-white">ℹ️</span>
                </div>
                <h3 class="text-lg text-gray-900 dark:text-white mb-0 font-semibold">Account Information</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-blue-400 mr-2">🛡️</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Account Type:</span>
                    </div>
                    <div>
                        @if($user->google_id)
                            <span class="inline-flex px-3 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                🔗 Google OAuth
                            </span>
                        @else
                            <span class="inline-flex px-3 py-2 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                🔒 Password
                            </span>
                        @endif
                    </div>
                </div>

                @if($user->google_id)
                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-red-400 mr-2">🔗</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Google ID:</span>
                    </div>
                    <span class="font-mono text-sm bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white px-3 py-2 rounded">{{ $user->google_id }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-blue-400 mr-2">👤</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Role:</span>
                    </div>
                    <span class="inline-flex px-3 py-2 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 capitalize">{{ $user->role ?? 'client' }}</span>
                </div>

                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-yellow-400 mr-2">📅</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Member Since:</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $user->created_at ? $user->created_at->format('M j, Y') : 'N/A' }}</span>
                </div>

                @if($user->email_verified_at)
                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-green-400 mr-2">✓</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Email Verified:</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-400 mr-1">✓</span>
                        <span class="text-green-400 font-medium">{{ $user->email_verified_at ? $user->email_verified_at->format('M j, Y') : 'N/A' }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="btn-primary px-6 py-3">
                ✓ {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div class="flex items-center text-green-400">
                    <span class="mr-2">✓</span>
                    <span class="font-medium">{{ __('Profile updated successfully!') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
