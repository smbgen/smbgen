<x-guest-layout>
    <div class="px-4 w-full max-w-md relative">
        <!-- Company watermark background -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-5 overflow-hidden">
            <div class="text-9xl font-black text-white transform -rotate-12 whitespace-nowrap" style="font-size: clamp(4rem, 15vw, 10rem);">
                {{ config('app.company_name', 'smbgen') }}
            </div>
        </div>
        
        <div class="bg-gray-800/90 border border-gray-700 rounded-xl shadow-xl p-6 relative z-10">
            <h2 class="text-center text-2xl font-bold text-white mb-6">Set Your Password</h2>
            <p class="text-center text-gray-300 text-sm mb-6">Choose how you'd like to access your account</p>
            
            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-300" />
                    <div x-data="{ show: false }" class="relative">
                        <input id="password" class="block mt-1 w-full pr-12 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" x-bind:type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-300 hover:text-white z-10" aria-label="Toggle password visibility">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3l18 18"/>
                                <path d="M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42"/>
                                <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a20.3 20.3 0 0 1-3.06 4.38"/>
                                <path d="M6.61 6.61A10.94 10.94 0 0 0 1 12s4 8 11 8a10.94 10.94 0 0 0 5.39-1.61"/>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-300" />
                    <div x-data="{ show: false }" class="relative">
                        <input id="password_confirmation" class="block mt-1 w-full pr-12 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" x-bind:type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-300 hover:text-white z-10" aria-label="Toggle password visibility">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3l18 18"/>
                                <path d="M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42"/>
                                <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a20.3 20.3 0 0 1-3.06 4.38"/>
                                <path d="M6.61 6.61A10.94 10.94 0 0 0 1 12s4 8 11 8a10.94 10.94 0 0 0 5.39-1.61"/>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end pt-1">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-lg hover:shadow-xl">
                        {{ __('Set Password & Continue') }}
                    </button>
                </div>
            </form>
            
            <div class="flex items-center gap-3 mt-6 mb-6">
                <span class="h-px bg-gray-600 flex-1"></span>
                <span class="text-xs uppercase tracking-wider text-gray-400">OR</span>
                <span class="h-px bg-gray-600 flex-1"></span>
            </div>
            
            <!-- Google OAuth Option -->
            <a href="{{ route('auth.google.redirect') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-600 rounded-lg text-white hover:bg-gray-700 transition-colors">
                <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="mr-2">
                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                    <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                </svg>
                Continue with Google
            </a>
            
            <p class="text-center text-gray-400 text-xs mt-4">
                You can set a password or use Google to sign in to your account.
            </p>
        </div>
    </div>
</x-guest-layout>
